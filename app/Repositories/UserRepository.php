<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository
{
    protected function getModel(): Model
    {
        return new User();
    }

    /**
     * Tìm user theo email
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Kiểm tra email đã tồn tại chưa
     */
    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }
}
