<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->repository = $userRepository;
    }

    protected function getRepository(): BaseRepository
    {
        return $this->userRepository;
    }

    /**
     * Đăng ký user mới
     */
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return $this->userRepository->create($data);
    }

    /**
     * Tìm user theo email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Kiểm tra email đã tồn tại
     */
    public function emailExists(string $email): bool
    {
        return $this->userRepository->emailExists($email);
    }

    /**
     * Verify password
     */
    public function verifyPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }
}
