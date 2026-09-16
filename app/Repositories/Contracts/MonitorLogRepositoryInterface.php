<?php

namespace App\Repositories\Contracts;

use App\Models\MonitorLog;
use Illuminate\Database\Eloquent\Collection;

interface MonitorLogRepositoryInterface
{
    /**
     * Create a new monitor log.
     *
     * @param array $data
     * @return MonitorLog
     */
    public function create(array $data): MonitorLog;

    /**
     * Get recent down logs for dashboard.
     *
     * @param int|null $userId
     * @param int $limit
     * @return Collection
     */
    public function getRecentDownLogs(?int $userId = null, int $limit = 10): Collection;

    /**
     * Get logs for a specific monitor.
     *
     * @param int $monitorId
     * @param int $limit
     * @return Collection
     */
    public function getLogsByMonitorId(int $monitorId, int $limit = 50): Collection;

    /**
     * Find log by ID.
     *
     * @param int $id
     * @return ?MonitorLog
     */
    public function findById(int $id): ?MonitorLog;

    /**
     * Delete a log entry.
     *
     * @param MonitorLog $log
     * @return bool
     */
    public function delete(MonitorLog $log): bool;
}
