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

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response(['error' => 'Invalid credentials'], 401);
        }

        $token = Auth::user()->createToken('app-token')->plainTextToken;

        return response(['message' => 'Success', 'token' => $token])->cookie(
            'token', $token, 60 * 48, null, null, false, true
        );
    }
}
