<?php

namespace App\Http\Controllers;

use App\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    protected BaseService $service;

    public function __construct()
    {
        $this->service = $this->getService();
    }

    /**
     * Lấy service instance
     */
    abstract protected function getService(): BaseService;

    /**
     * Trả về response thành công
     */
    protected function successResponse(
        $data = null,
        string $message = 'Success',
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Trả về response lỗi
     */
    protected function errorResponse(
        string $message = 'Error',
        $errors = null,
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    /**
     * Trả về response validation lỗi (status 200)
     */
    protected function validationErrorResponse(array $errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors,
        ], 200);
    }

    /**
     * Lấy tất cả bản ghi
     */
    public function index(): JsonResponse
    {
        $data = $this->service->getAll();
        return $this->successResponse($data, 'Records retrieved successfully');
    }

    /**
     * Lấy bản ghi theo ID
     */
    public function show(int $id): JsonResponse
    {
        $data = $this->service->getById($id);
        
        if (!$data) {
            return $this->errorResponse('Record not found', null, 200);
        }
        
        return $this->successResponse($data, 'Record retrieved successfully');
    }

    /**
     * Tạo bản ghi mới
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $this->service->create($request->all());
            return $this->successResponse($data, 'Record created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create record', ['error' => $e->getMessage()], 200);
        }
    }

    /**
     * Cập nhật bản ghi
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $success = $this->service->update($id, $request->all());
            
            if (!$success) {
                return $this->errorResponse('Record not found', null, 200);
            }
            
            $data = $this->service->getById($id);
            return $this->successResponse($data, 'Record updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update record', ['error' => $e->getMessage()], 200);
        }
    }

    /**
     * Xóa mềm bản ghi
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $success = $this->service->delete($id);
            
            if (!$success) {
                return $this->errorResponse('Record not found', null, 200);
            }
            
            return $this->successResponse(null, 'Record deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete record', ['error' => $e->getMessage()], 200);
        }
    }

    /**
     * Khôi phục bản ghi
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $success = $this->service->restore($id);
            
            if (!$success) {
                return $this->errorResponse('Record not found', null, 200);
            }
            
            return $this->successResponse(null, 'Record restored successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to restore record', ['error' => $e->getMessage()], 200);
        }
    }

    /**
     * Xóa vĩnh viễn bản ghi
     */
    public function forceDelete(int $id): JsonResponse
    {
        try {
            $success = $this->service->forceDelete($id);
            
            if (!$success) {
                return $this->errorResponse('Record not found', null, 200);
            }
            
            return $this->successResponse(null, 'Record permanently deleted');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete record', ['error' => $e->getMessage()], 200);
        }
    }
}
