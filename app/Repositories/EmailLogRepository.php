<?php

namespace App\Repositories;

use App\Models\EmailLog;
use App\Repositories\Contracts\EmailLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EmailLogRepository implements EmailLogRepositoryInterface
{
    /**
     * Create a new email log.
     *
     * @param array $data
     * @return EmailLog
     */
    public function create(array $data): EmailLog
    {
        return EmailLog::create($data);
    }

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
    ): LengthAwarePaginator {
        $perPage = $perPage ?? (int) config('constants.pagination_limit.defaultPagination', 10);

        $query = EmailLog::query()
            ->with(['user', 'monitor'])
            ->latest('sent_at');

        /*
        |--------------------------------------------------------------------------
        | User Access Control
        |--------------------------------------------------------------------------
        */
        if (!$isAdmin) {
            $query->where('user_id', $userId);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter by Status (sent, failed, etc.)
        |--------------------------------------------------------------------------
        */
        if (!empty($status)) {
            $query->where('status', $status);
        }

        /*
        |--------------------------------------------------------------------------
        | Search Across All Relevant Columns & Relations
        |--------------------------------------------------------------------------
        */
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('recipient_email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('alert_type', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('error_message', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('monitor', function ($monitorQuery) use ($search) {
                        $monitorQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('url', 'like', "%{$search}%");
                    });
            });
        }

        return $query
            ->paginate($perPage)
            ->withQueryString();
    }

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
    ): Collection {
        $query = EmailLog::query()
            ->with(['user', 'monitor'])
            ->latest('sent_at');

        if (!$isAdmin) {
            $query->where('user_id', $userId);
        }

        return $query
            ->limit($limit)
            ->get();
    }

    /**
     * Find email log by ID.
     *
     * @param int $id
     * @return ?EmailLog
     */
    public function findById(int $id): ?EmailLog
    {
        return EmailLog::query()
            ->with(['user', 'monitor'])
            ->find($id);
    }

    /**
     * Delete email log.
     *
     * @param EmailLog $emailLog
     * @return bool
     */
    public function delete(EmailLog $emailLog): bool
    {
        return (bool) $emailLog->delete();
    }
}
