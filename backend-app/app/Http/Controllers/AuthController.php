<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['user' => $user, 'token' => $token]);
        }

         return response()->json(['message' => 'Unauthorized'], 401);
    }
    public function logout(Request $request)
    {
        //
    }
    public function register(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'email' => $credentials['email'],
            'name'=> $credentials['name'],
            'password' => bcrypt($credentials['password']),
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }
}
