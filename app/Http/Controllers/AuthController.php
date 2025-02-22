<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response(['error' => 'Invalid credentials'], 400);
        }

        $token = $user->createToken($user->name)->plainTextToken;

        return response(["token" => $token]);
    }

    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required']
        ]);

        $user = User::factory()->create($credentials);

        return response($user, 201);
    }

    public function logout(Request $request)
    {
        // dd($request->user()->tokens);
        $request->user()->tokens()->delete();
        return response(["message" => "logout success"]);
    }
}
