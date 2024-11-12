<?php

namespace App\Http\Services;

use App\Http\Interfaces\AuthInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthInterface
{
    public function register($request)
    {
        User::create(
            $request->all()
        );

        return response()->json(['user_created' => __('messages.user_registered')]);
    }

    public function login($request)
    {
        // Attempt to authenticate the user
        $user = User::where('email', $request->email)->first();

        // Check if the user exists and if the password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => __('messages.Invalid_login_credentials'),
                'data' => []
            ], 401);
        }

        // Create a Sanctum token for the user
        $token = $user->createToken('API Token')->plainTextToken;

        // Return token and user details
        return response()->json([
            'message' => __('message.login_successfully'),
            'data' => [
                'token' => $token,
                'user' => $user
            ]
        ], 200);
    }
}
