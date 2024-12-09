<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        $isValidUser = Auth::attempt($data);

        if ($isValidUser){
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Successfully logged in.',
                'user' => $user,
                'token' => $token
            ]);
        } else {
            return response()->json('Invalid credentials', 401);
        }
    }

    public function logout(Request $request)
    {
        $user  = Auth::user();

        $user->tokens()->delete();
        return response()->json([
            'message' => 'Successfully logged out.'
        ]);
    }

}
