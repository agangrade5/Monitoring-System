<?php

namespace App\Repositories\Contracts;

use App\Models\Monitor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MonitorRepositoryInterface
{
    /**
     * Retrieves all monitors from the database.
     * 
     * @param string|null $search
     * @return LengthAwarePaginator<Monitor> A paginator containing all monitors.
     */
    public function getAll(?string $search = null): LengthAwarePaginator;

    /**
     * Retrieves a monitor by its ID.
     * 
     * @param int $id The ID of the monitor to retrieve.
     * @return Monitor|null The retrieved monitor, or null if not found.
     */
    public function findById(int $id): ?Monitor;

    /**
     * Creates a new monitor in the database.
     * 
     * @param array $data The data to create the monitor with.
     * @return Monitor The created monitor.
     */
    public function create(array $data): Monitor;

    /**
     * Updates an existing monitor in the database.
     * 
     * @param int $id The ID of the monitor to update.
     * @param array $data The data to update the monitor with.
     * @return bool True if the update was successful, false otherwise.
     */
    public function update(int $id, array $data): bool;

    
    public function delete(int $id): bool;
}