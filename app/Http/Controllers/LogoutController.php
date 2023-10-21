<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        if (auth()->user()) {
            auth()->user()->currentAccessToken()->revoke();
        }

        if (Auth::check()) {
            Auth::logout();
        }

        return response(['message' => 'Logged out'], 200);
    }
}
