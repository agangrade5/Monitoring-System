<?php

namespace App\Repositories;

use App\Models\Monitor;
use App\Repositories\Contracts\MonitorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MonitorRepository implements MonitorRepositoryInterface
{
    /**
     * Constructor to inject the Monitor model.
     */
    public function __construct(
        protected Monitor $model
    ) {}

    /**
     * Retrieves all monitors with pagination and optional search filtering.
     *
     * @param string|null $search
     * @return LengthAwarePaginator
     */
    public function getAll(?string $search = null): LengthAwarePaginator
    {
        return $this->model
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('url', 'like', "%{$search}%")
                      ->orWhere('ip_address', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);
    }

    /**
     * Retrieves a monitor by its ID.
     */
    public function findById(int $id): ?Monitor
    {
        return $this->model->find($id);
    }

    /**
     * Creates a new monitor.
     */
    public function create(array $data): Monitor
    {
        return $this->model->create($data);
    }

    /**
     * Updates a monitor with the given data.
     */
    public function update(int $id, array $data): bool
    {
        $monitor = $this->findById($id);

        if (!$monitor) {
            return false;
        }

        return $monitor->update($data);
    }

    /**
     * Deletes a monitor by its ID.
     */
    public function delete(int $id): bool
    {
        $monitor = $this->findById($id);

        if (!$monitor) {
            return false;
        }

        return $monitor->delete();
    }
}