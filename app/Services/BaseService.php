<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    protected BaseRepository $repository;

    public function __construct()
    {
        $this->repository = $this->getRepository();
    }

    /**
     * Lấy repository instance
     */
    abstract protected function getRepository(): BaseRepository;

    /**
     * Lấy tất cả bản ghi
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Lấy bản ghi theo ID
     */
    public function getById(int $id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * Lấy bản ghi theo điều kiện
     */
    public function getByConditions(array $conditions): ?Model
    {
        return $this->repository->findWhere($conditions);
    }

    /**
     * Lấy tất cả bản ghi theo điều kiện
     */
    public function getAllByConditions(array $conditions): Collection
    {
        return $this->repository->where($conditions);
    }

    /**
     * Tạo bản ghi mới
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * Cập nhật bản ghi
     */
    public function update(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Xóa mềm bản ghi
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Khôi phục bản ghi
     */
    public function restore(int $id): bool
    {
        return $this->repository->restore($id);
    }

    /**
     * Xóa vĩnh viễn bản ghi
     */
    public function forceDelete(int $id): bool
    {
        return $this->repository->forceDelete($id);
    }

    /**
     * Phân trang
     */
    public function paginate(int $perPage = 15, array $conditions = []): Paginator
    {
        return $this->repository->paginate($perPage, $conditions);
    }

    /**
     * Đếm bản ghi
     */
    public function count(array $conditions = []): int
    {
        return $this->repository->count($conditions);
    }

    /**
     * Kiểm tra bản ghi tồn tại
     */
    public function exists(int $id): bool
    {
        return $this->repository->exists($id);
    }
}
