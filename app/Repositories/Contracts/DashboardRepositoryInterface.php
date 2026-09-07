<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface DashboardRepositoryInterface
{
    /**
     * Get all dashboard metrics and data for admin.
     *
     * @param User $user
     * @return array
     */
    public function getAdminDashboardData(User $user): array;

    /**
     * Get all dashboard metrics and data for user.
     *
     * @param User $user
     * @return array
     */
    public function getUserDashboardData(User $user): array;
}
