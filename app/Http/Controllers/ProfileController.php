<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get current user profile
     */
    public function show(): JsonResponse
    {
        $user = auth('api')->user();
        $user->load('team');
        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Update current user profile
     */
    public function update(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        
        $validator = validator($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 200);
        }

        $data = $request->only(['name', 'email', 'phone', 'notes']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $this->userService->update($user->id, $data);
        
        $updatedUser = $this->userService->getById($user->id);
        $updatedUser->load('team');

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $updatedUser,
        ]);
    }

    /**
     * Get team members
     */
    public function teamMembers(): JsonResponse
    {
        $user = auth('api')->user();
        
        if (!$user->team_id) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'User does not belong to any team'
            ]);
        }

        $members = $this->userService->getAllByConditions(['team_id' => $user->team_id]);
        
        return response()->json([
            'success' => true,
            'data' => $members,
        ]);
    }
}
