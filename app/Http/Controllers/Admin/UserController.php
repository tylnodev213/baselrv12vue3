<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Services\BaseService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        parent::__construct();
    }

    protected function getService(): BaseService
    {
        return $this->userService;
    }

    public function index(): JsonResponse
    {
        // Load team relationship
        $data = $this->userService->paginate(15);
        $data->load('team');
        return $this->successResponse($data, 'Users retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'team_id' => 'nullable|exists:teams,id',
            'role' => 'required|string|in:admin,user',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        
        try {
            $user = $this->userService->create($data);
            return $this->successResponse($user, 'User created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create user', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validator = validator($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'team_id' => 'nullable|exists:teams,id',
            'role' => 'sometimes|required|string|in:admin,user',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $data = $request->all();
        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        try {
            $success = $this->userService->update($id, $data);
            if (!$success) {
                return $this->errorResponse('User not found');
            }
            $user = $this->userService->getById($id);
            return $this->successResponse($user, 'User updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update user', ['error' => $e->getMessage()]);
        }
    }
}
