<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponseTrait;

class AuthController extends Controller
{
    use ResponseTrait;

    public function register(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors()->toArray(), 422);
        }

        $input = $request->only('name', 'email', 'password');
        $input['password'] = Hash::make($input['password']);
        try {
            $user = User::create($input);
            $token = $user->createToken('MyApp')->plainTextToken;

            return $this->sendSuccess('User registered successfully', [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return $this->sendError('An error occurred while registering the user.', ['error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors(), 422);
        }

        try {
            // Check if the credentials are valid
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $token = $user->createToken('MyApp')->plainTextToken;

                return $this->sendSuccess('Login successful', [
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $token,
                ]);
            }

            return $this->sendError('Unauthorized', [], 401);
        } catch (\Exception $e) {
            return $this->sendError('An error occurred during login.', ['error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            if (!$request->user()) {
                return $this->sendError('Unauthorized. Please login first.', [], 401);
            }

            $request->user()->currentAccessToken()->delete();
            return $this->sendSuccess('Logout successful');
        } catch (\Exception $e) {
            return $this->sendError('An error occurred during logout.', ['error' => $e->getMessage()], 500);
        }
    }
}
