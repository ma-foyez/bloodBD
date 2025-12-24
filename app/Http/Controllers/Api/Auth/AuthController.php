<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'mobile' => ['nullable', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'dob' => ['nullable', 'date'],
            'blood_group' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'is_weight_50kg' => ['nullable', 'boolean'],
            'last_donation' => ['nullable', 'date'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'post_office' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        // Prepare data for UserService
        $data = $request->only([
            'name',
            'email',
            'username',
            'mobile',
            'password',
            'dob',
            'blood_group',
            'occupation',
            'is_weight_50kg',
            'last_donation',
            'division_id',
            'district_id',
            'area_id',
            'post_office'
        ]);

        $user = $this->userService->createUser($data);

        // Assign default role 'subscriber' if not handled in service
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
     * Login user with email or mobile and create token.
     *
     * @tags Authentication
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email_or_mobile' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $emailOrMobile = $request->email_or_mobile;

        // Try to find user by email or mobile
        $user = User::where('email', $emailOrMobile)
            ->orWhere('mobile', $emailOrMobile)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        // Create a token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Load relationships
        $user->load(['division', 'district', 'area']);

        return $this->successResponse([
            'data' => [
                'id' => $user->id,
                'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                'email' => $user->email,
                'username' => $user->username,
                'mobile' => $user->mobile,
                'dob' => $user->dob,
                'blood_group' => $user->blood_group,
                'occupation' => $user->occupation,
                'is_weight_50kg' => $user->is_weight_50kg,
                'last_donation' => $user->last_donation,
                'is_active' => $user->is_active,
                // 'is_approved' => $user->is_approved,
                'pic' => $user->pic,
                'registered_at' => $user->created_at,
                'address' => [
                    'division' => $user->division ? $user->division->name : null,
                    'district' => $user->district ? $user->district->name : null,
                    'area' => $user->area ? $user->area->name : null,
                    'post_office' => $user->post_office,
                ],
                'access_token' => $token,

            ],
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

        // Load relationships
        $user->load(['division', 'district', 'area']);

        return $this->successResponse([
            'id' => $user->id,
            'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
            'email' => $user->email,
            'username' => $user->username,
            'mobile' => $user->mobile,
            'dob' => $user->dob,
            'blood_group' => $user->blood_group,
            'occupation' => $user->occupation,
            'is_weight_50kg' => $user->is_weight_50kg,
            'last_donation' => $user->last_donation,
            'location' => [
                'division' => $user->division ? [
                    'id' => $user->division->id,
                    'name' => $user->division->name,
                    'bn_name' => $user->division->bn_name,
                ] : null,
                'district' => $user->district ? [
                    'id' => $user->district->id,
                    'name' => $user->district->name,
                    'bn_name' => $user->district->bn_name,
                ] : null,
                'area' => $user->area ? [
                    'id' => $user->area->id,
                    'name' => $user->area->name,
                    'bn_name' => $user->area->bn_name,
                ] : null,
                'post_office' => $user->post_office,
            ],
            'is_active' => $user->is_active,
            'is_approved' => $user->is_approved,
            'pic' => $user->pic,
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
