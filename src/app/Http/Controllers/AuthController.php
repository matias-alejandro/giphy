<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('giphy-api', ['*'], now()->addMinutes(30));

            return response()->json($token->accessToken, 200);
        }

        return response()->json(['error' => 'Credenciales inválidas'], 401);
    }

}
