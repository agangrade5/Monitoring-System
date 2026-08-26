<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Method create
     *
     * @param array $data
     *
     * @return User
     */
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    /**
     * Method updateUser
     *
     * @param string $email
     *
     * @return User
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Method to retrieve all users
     *
     * @return array<User>
     */
    public function getAllUsers(): LengthAwarePaginator
{
    return User::latest()->paginate(10);
}
}
