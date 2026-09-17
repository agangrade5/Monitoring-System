<?php

namespace App\Repositories;

use App\Models\MonitorLog;
use App\Repositories\Contracts\MonitorLogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MonitorLogRepository implements MonitorLogRepositoryInterface
{
    /**
     * Create a new monitor log.
     *
     * @param array $data
     * @return MonitorLog
     */
    public function create(array $data): MonitorLog
    {
        return MonitorLog::create($data);
    }

    /**
     * Get recent down logs for dashboard.
     *
     * @param int|null $userId
     * @param int $limit
     * @return Collection
     */
    public function getRecentDownLogs(?int $userId = null, int $limit = 10): Collection
    {
        $query = MonitorLog::query()
            ->with('monitor')
            ->where('status', 'down')
            ->latest();

        if ($userId !== null) {
            $query->whereHas('monitor', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        return $query->take($limit)->get();
    }

    /**
     * Get logs for a specific monitor.
     *
     * @param int $monitorId
     * @param int $limit
     * @return Collection
     */
    public function getLogsByMonitorId(int $monitorId, int $limit = 50): Collection
    {
        return MonitorLog::query()
            ->where('monitor_id', $monitorId)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Find log by ID.
     *
     * @param int $id
     * @return ?MonitorLog
     */
    public function findById(int $id): ?MonitorLog
    {
        return MonitorLog::query()
            ->with('monitor')
            ->find($id);
    }

    /**
     * Delete a log entry.
     *
     * @param MonitorLog $log
     * @return bool
     */
    public function delete(MonitorLog $log): bool
    {
        return (bool) $log->delete();
    }
}
