<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    /**
     * Method create
     *
     * @param array $data
     *
     * @return User
     */
    public function create(array $data): User;

    /**
     * Method findByEmail
     *
     * @param string $email
     *
     * @return User
     */
    public function findByEmail(string $email): ?User;

    /**
     * Method to retrieve all users
     *
     * @return array<User>
     */
     public function getAllUsers(): LengthAwarePaginator;

    /**
     * Update user password.
     */
    public function updatePassword(
        User $user,
        string $password
    ): bool;
}
