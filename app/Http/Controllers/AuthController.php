<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Đăng ký tài khoản mới
     */
    public function register(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\User::where('email', $value)
                        ->where('del_flag', 0)
                        ->exists();
                    if ($exists) {
                        $fail('The email has already been taken.');
                    }
                },
            ],
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 200);
        }

        try {
            $user = $this->userService->register([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);

            $token = JWTAuth::fromUser($user);
            $refreshToken = $this->generateRefreshToken($user);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user,
                    'access_token' => $token,
                    'refresh_token' => $refreshToken,
                    'token_type' => 'Bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'errors' => ['error' => $e->getMessage()],
            ], 200);
        }
    }

    /**
     * Đăng nhập
     */
    public function login(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 200);
        }

        try {
            $credentials = $request->only('email', 'password');
            
            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'errors' => ['credentials' => 'Email hoặc password không đúng'],
                ], 200);
            }

            $user = auth('api')->user();
            $refreshToken = $this->generateRefreshToken($user);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'access_token' => $token,
                    'refresh_token' => $refreshToken,
                    'token_type' => 'Bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                ],
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not create token',
                'errors' => ['error' => $e->getMessage()],
            ], 200);
        }
    }

    /**
     * Đăng xuất
     */
    public function logout(): JsonResponse
    {
        try {
            auth('api')->logout();
            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout',
                'errors' => ['error' => $e->getMessage()],
            ], 200);
        }
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $refreshToken = $request->input('refresh_token');
            
            if (!$refreshToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Refresh token not provided',
                ], 200);
            }

            // Validate refresh token (có thể lưu vào cache hoặc database)
            // Ở đây sử dụng JWT để decode
            $decoded = JWTAuth::decode($refreshToken);
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid refresh token',
                ], 200);
            }

            $newToken = auth('api')->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'access_token' => $newToken,
                    'token_type' => 'Bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token',
                'errors' => ['error' => $e->getMessage()],
            ], 200);
        }
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function me(): JsonResponse
    {
        try {
            $user = auth('api')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'User info retrieved',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user info',
                'errors' => ['error' => $e->getMessage()],
            ], 200);
        }
    }

    /**
     * Tạo refresh token
     */
    private function generateRefreshToken($user): string
    {
        $payload = [
            'sub' => $user->id,
            'type' => 'refresh',
            'iat' => time(),
            'exp' => time() + (86400 * 7), // 7 days
        ];

        return JWTAuth::getJWTProvider()->encode($payload);
    }
}
