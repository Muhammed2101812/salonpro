<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseController
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate(['name' => 'required|string|max:255', 'email' => 'required|string|email|max:255|unique:users', 'password' => 'required|string|min:8|confirmed']);
        $user = User::create(['name' => $validated['name'], 'email' => $validated['email'], 'password' => Hash::make($validated['password'])]);
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendSuccess(['user' => $user, 'token' => $token], 'User registered successfully', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendSuccess(['user' => $user, 'token' => $token], 'Login successful');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendSuccess(null, 'Logout successful');
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->sendSuccess($request->user(), 'Profile retrieved');
    }
}
