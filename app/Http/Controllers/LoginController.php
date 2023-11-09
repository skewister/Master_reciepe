<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
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

        return response([
            'message' => 'Success',
            'token' => $user->createToken('app-token')->plainTextToken,
            'user' => $user
        ]);
    }
}
