<?php

namespace App\Http\Controllers;

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

        // Tentez de vous authentifier...
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response(['error' => 'Invalid credentials'], 401);
        }

        // Générez un token...
        $token = Auth::user()->createToken('app-token')->plainTextToken;

        // Définissez la durée du cookie...
        $minutes = 60 * 48; // 2 jours

        // Envoyez la réponse avec le cookie...
        return response(['message' => 'Success'])->cookie(
            'token', $token, $minutes, null, null, false, true
        );
    }
}

