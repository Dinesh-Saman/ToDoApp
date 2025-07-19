<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Hardcoded credentials (for demo purposes only)
    private const HARDCODED_EMAIL = 'admin@example.com';
    private const HARDCODED_PASSWORD = 'securepassword123';
    private const HARDCODED_NAME = 'Admin User';

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Generate and save token
    $token = Str::random(60);
    $user->api_token = $token;
    $user->save();

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user->only('id', 'name', 'email'),
    ], 200);
}

    protected function getOrCreateHardcodedUser()
    {
        // Find or create the hardcoded user
        return User::firstOrCreate(
            ['email' => self::HARDCODED_EMAIL],
            [
                'name' => self::HARDCODED_NAME,
                'password' => Hash::make(self::HARDCODED_PASSWORD),
                'api_token' => null, // Will be set when generating token
            ]
        );
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $user->clearToken();

        return response()->json(['message' => 'Logout successful'], 200);
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user->only('id', 'name', 'email'),
        ], 201);
    }
}