<?php

namespace App\Repositories\Contracts;

use App\Models\User;

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
}
