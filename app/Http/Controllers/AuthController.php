<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        $isValidUser = Auth::attempt($data);

        if ($isValidUser) {
            /**
             * @var \App\Models\User $user
             */
            $user = Auth::user();
            $rights = DB::select("select m.[name] [menu],
            isnull(ur.[create], 0) [create],
            isnull(ur.[view], 0) [view],
            isnull(ur.[update], 0) [update],
            isnull(ur.[delete], 0) [delete],
            isnull(ur.[print], 0) [print]
            from menus m
            left join user_rights ur on  ur.menu_id = m.id and ur.user_id = ?", [$user->id]);
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Successfully logged in.',
                'user' => $user,
                'token' => $token,
                'rights' => $rights
            ]);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 400);
        }
    }

    public function logout(Request $request)
    {
        /**
         * @var \App\Models\User $user
         */
        $user  = Auth::user();

        $user->tokens()->delete();
        return response()->json([
            'message' => 'Successfully logged out.'
        ]);
    }
}
