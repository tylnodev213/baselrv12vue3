<?php

namespace App\Models;

use App\Enums\DeleteFlag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;

    protected $casts = [
        'del_flag' => DeleteFlag::class,
    ];

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected static function booted(): void
    {
        // Tự động filter các bản ghi đã xóa (del_flag = 1)
        static::addGlobalScope('notDeleted', function (Builder $builder) {
            $builder->where('del_flag', DeleteFlag::OFF);
        });
    }

    /**
     * Lấy tất cả bản ghi bao gồm những bản ghi đã xóa
     */
    public static function withDeleted()
    {
        return static::withoutGlobalScopes();
    }

    /**
     * Lấy chỉ những bản ghi đã xóa
     */
    public static function onlyDeleted()
    {
        return static::withoutGlobalScopes()->where('del_flag', DeleteFlag::ON);
    }

    /**
     * Soft delete: đánh dấu bản ghi là đã xóa
     */
    public function softDelete(): bool
    {
        return $this->update(['del_flag' => DeleteFlag::ON]);
    }

    /**
     * Khôi phục bản ghi đã xóa
     */
    public function restore(): bool
    {
        return $this->update(['del_flag' => DeleteFlag::OFF]);
    }

    /**
     * Kiểm tra bản ghi có bị xóa không
     */
    public function isDeleted(): bool
    {
        return $this->del_flag === DeleteFlag::ON;
    }
}
