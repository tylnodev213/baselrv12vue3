<?php

namespace App\Repositories;

use App\Enums\DeleteFlag;
use App\Models\BaseModel;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct()
    {
        $this->model = $this->getModel();
    }

    /**
     * Lấy model instance
     */
    abstract protected function getModel(): Model;

    /**
     * Lấy tất cả bản ghi
     */
    public function all(): Collection
    {
        return $this->model::all();
    }

    /**
     * Lấy bản ghi theo ID
     */
    public function find(int $id): ?Model
    {
        return $this->model::find($id);
    }

    /**
     * Lấy bản ghi theo điều kiện
     */
    public function findWhere(array $conditions): ?Model
    {
        $query = $this->model::query();
        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }
        return $query->first();
    }

    /**
     * Lấy tất cả bản ghi theo điều kiện
     */
    public function where(array $conditions): Collection
    {
        $query = $this->model::query();
        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }
        return $query->get();
    }

    /**
     * Tạo bản ghi mới
     */
    public function create(array $data): Model
    {
        if ($this->model instanceof BaseModel) {
            $data['del_flag'] = DeleteFlag::OFF;
        }
        return $this->model::create($data);
    }

    /**
     * Cập nhật bản ghi
     */
    public function update(int $id, array $data): bool
    {
        $model = $this->find($id);
        if (!$model) {
            return false;
        }
        return $model->update($data);
    }

    /**
     * Xóa mềm bản ghi
     */
    public function delete(int $id): bool
    {
        $model = $this->find($id);
        if (!$model) {
            return false;
        }
        if ($model instanceof BaseModel) {
            return $model->softDelete();
        }
        return $model->delete();
    }

    /**
     * Khôi phục bản ghi đã xóa
     */
    public function restore(int $id): bool
    {
        $model = $this->model::withDeleted()->find($id);
        if (!$model) {
            return false;
        }
        if ($model instanceof BaseModel) {
            return $model->restore();
        }
        return false;
    }

    /**
     * Xóa vĩnh viễn bản ghi
     */
    public function forceDelete(int $id): bool
    {
        $model = $this->model::withDeleted()->find($id);
        if (!$model) {
            return false;
        }
        return $model->forceDelete();
    }

    /**
     * Phân trang
     */
    public function paginate(int $perPage = 15, array $conditions = []): Paginator
    {
        $query = $this->model::query();
        
        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }
        
        return $query->paginate($perPage);
    }

    /**
     * Lấy model query builder
     */
    public function query(): Builder
    {
        return $this->model::query();
    }

    /**
     * Kiểm tra bản ghi tồn tại
     */
    public function exists(int $id): bool
    {
        return $this->model::where('id', $id)->exists();
    }

    /**
     * Đếm bản ghi
     */
    public function count(array $conditions = []): int
    {
        $query = $this->model::query();
        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }
        return $query->count();
    }
}
