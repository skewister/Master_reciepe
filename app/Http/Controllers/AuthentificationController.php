<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthentificationController extends Controller
{
    // Méthode pour s'inscire normal
    public function register(Request $request)
    {
        $customMessages = [
            'name.required' => 'Le champ nom est obligatoire.',
            'email.required' => 'Le champ email est obligatoire.',
            'email.email' => "L'adresse email doit être une adresse email valide.",
            'email.unique' => "L'adresse email est déjà utilisée.",
            'password.required' => 'Le champ mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ], $customMessages);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user);

        $token = $user->createToken('app-token')->plainTextToken;

        return response(['token' => $token], 201);
    }


    // Méthode de connexion standard
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!(Auth::attempt($request->only('email', 'password')))) {
            return response(['error' => 'Invalid credentials'], 401);
        }

        $user = User::where('email', $request->input('email'))->first();


        Auth::login($user);

        return response([
            'message' => 'Success',
            'token' => $user->createToken('app-token')->plainTextToken,
            'user' => $user
        ]);
    }


    // Méthode de déconnexion
    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();

        return response(['message' => 'Logged out'], 200);
    }


    // Méthode pour rediriger vers Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }


    // Méthode pour gérer le callback de Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect('/')->withErrors(['error' => 'Une erreur est survenue.']);
        }

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            ['name' => $googleUser->getName(), 'password' => bcrypt(Str::random(24))]
        );

        Auth::login($user);
        $token = $user->createToken('app-token')->plainTextToken;

        return redirect(env('FRONTEND_URL') . "/login/success?token=$token");
    }


}
