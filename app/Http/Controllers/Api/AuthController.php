<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password salah'
            ], 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'message' => 'success',
            'user' => $user,
        ]);
    }


    function register(Request $request)
    {

        $request->validate([
            "name" => 'required',
            "email" => 'required|email|unique:users,email',
            "password" => 'required'
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password), //untuk encrypt password
        ]);

        return response()->json([
            'message' => 'Berhasil Register',
        ]);
    }

    function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'messaage' => 'Berhasil logout'
        ]);
    }
}
