<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class AuthController extends ApiController
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * Register a new user.
     *
     * @tags Authentication
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        // Prepare data for UserService
        $data = $request->only(['first_name', 'last_name', 'email', 'password']);
        $data['username'] = explode('@', $data['email'])[0] . rand(1000, 9999); // Auto-generate username or take from request if needed

        $user = $this->userService->createUser($data);

        // Assign default role 'user' if not handled in service
        if (!$user->hasAnyRole(\App\Models\Role::all())) {
            $user->assignRole(\App\Models\Role::SUBSCRIBER);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => $user,
            'token_type' => 'Bearer',
        ], 'User registered successfully.', 201);
    }
    /**
     * Login user and create token.
     *
     * @tags Authentication
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        // Create a token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'token_type' => 'Bearer',
        ], 'Login successful.');
    }

    /**
     * Get authenticated user.
     *
     * @tags Authentication
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        return $this->successResponse([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 'User details retrieved successfully.');
    }

    /**
     * Logout user (revoke token).
     *
     * @tags Authentication
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully');
    }

    /**
     * Revoke all tokens for the authenticated user.
     *
     * @tags Authentication
     */
    public function revokeAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return $this->successResponse(null, 'All tokens revoked successfully');
    }
}
