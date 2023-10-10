<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Si l'utilisateur est authentifié avec Sanctum...
        if (auth()->user()) {
            // Révoquer le token courant...
            $request->user()->currentAccessToken()->delete();
        }

        // Si l'utilisateur est authentifié via session...
        if (Auth::check()) {
            Auth::logout();
        }

        return response(['message' => 'Logged out'], 200);
    }
}
