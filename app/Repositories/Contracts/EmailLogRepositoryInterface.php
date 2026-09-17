<?php

namespace App\Repositories\Contracts;

use App\Models\EmailLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmailLogRepositoryInterface
{
    /**
     * Create a new email log.
     *
     * @param array $data
     * @return EmailLog
     */
    public function create(array $data): EmailLog;

    /**
     * Get paginated email logs with filtering and search.
     *
     * @param int $userId
     * @param bool $isAdmin
     * @param string|null $search
     * @param string|null $status
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getLogs(
        int $userId,
        bool $isAdmin = false,
        ?string $search = null,
        ?string $status = null,
        ?int $perPage = null
    ): LengthAwarePaginator;

    /**
     * Get recent email logs.
     *
     * @param int $userId
     * @param bool $isAdmin
     * @param int $limit
     * @return Collection
     */
    public function getRecentLogs(
        int $userId,
        bool $isAdmin = false,
        int $limit = 5
    ): Collection;

    /**
     * Find email log by ID.
     *
     * @param int $id
     * @return ?EmailLog
     */
    public function findById(int $id): ?EmailLog;

    /**
     * Delete email log.
     *
     * @param EmailLog $emailLog
     * @return bool
     */
    public function delete(EmailLog $emailLog): bool;
}
