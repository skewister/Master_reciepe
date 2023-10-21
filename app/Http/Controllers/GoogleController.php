<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['error' => 'Une erreur est survenue lors de la tentative de connexion avec Google. Veuillez réessayer.']);
        }

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            ['name' => $googleUser->getName(), 'password' => bcrypt(Str::random(24))]
        );

        Auth::login($user);

        $token = $user->createToken('app-token')->plainTextToken;

        return response(['token' => $token], 200);
    }
}
