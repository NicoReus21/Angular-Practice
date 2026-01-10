<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Group;
use App\Models\UserGroup;
use Carbon\Carbon;


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

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!$user || !Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json(['message' => 'Password updated']);
    }

    public function googleLogin(Request $request)
    {
        $validated = $request->validate([
            'id_token' => 'required|string',
        ]);

        $clientId = config('services.google.client_id');
        if (!$clientId) {
            return response()->json(['message' => 'Google client ID not configured'], 500);
        }

        $tokenInfo = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $validated['id_token'],
        ]);

        if (!$tokenInfo->ok()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $tokenInfo->json();
        $audience = $payload['aud'] ?? null;
        $email = $payload['email'] ?? null;
        $emailVerified = ($payload['email_verified'] ?? '') === 'true' || ($payload['email_verified'] ?? false) === true;
        $name = $payload['name'] ?? $payload['given_name'] ?? null;

        if ($audience !== $clientId || !$email || !$emailVerified) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name ?: $email,
                'password' => Hash::make(Str::random(32)),
            ]
        );

        if ($emailVerified && !$user->email_verified_at) {
            $user->email_verified_at = Carbon::now();
            $user->save();
        }

        $systemUser = User::firstOrCreate(
            ['email' => 'system@sigba.test'],
            [
                'name' => 'System Admin',
                'password' => Hash::make(Str::random(32)),
            ]
        );

        $guestGroup = Group::firstOrCreate(
            ['name' => 'Invitados'],
            [
                'description' => 'Acceso limitado para usuarios externos',
                'id_parent_group' => null,
                'id_user_created' => $systemUser->id,
            ]
        );

        UserGroup::updateOrCreate(
            ['id_user' => $user->id, 'id_group' => $guestGroup->id],
            [
                'assigned_at' => Carbon::today()->toDateString(),
                'removed_at' => null,
                'id_user_created' => $systemUser->id,
            ]
        );

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
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
