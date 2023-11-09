<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();

        return response(['message' => 'Logged out'], 200);
    }
}
