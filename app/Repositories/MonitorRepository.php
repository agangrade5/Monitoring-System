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
     * @param int|null $userId
     * 
     * @return LengthAwarePaginator
     */
    public function getAll(?string $search = null, ?int $userId = null): LengthAwarePaginator
    {
        return $this->model
            ->with(['user', 'settings', 'checkResult'])
            ->when($userId, function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('url', 'like', "%{$search}%");
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
        return $this->model->with([
            'user',
            'settings',
            'checkResult',
            'logs' => fn($q) => $q->latest()
        ])->find($id);
    }

    /**
     * Creates a new monitor along with its settings and check results.
     */
    public function create(array $data): Monitor
    {
        $settingsData = [
            'check_uptime' => isset($data['check_uptime']) ? (bool) $data['check_uptime'] : false,
            'check_ssl' => isset($data['check_ssl']) ? (bool) $data['check_ssl'] : false,
            'check_php' => isset($data['check_php']) ? (bool) $data['check_php'] : false,
            'check_domain' => isset($data['check_domain']) ? (bool) $data['check_domain'] : false,
            'check_security_headers' => isset($data['check_security_headers']) ? (bool) $data['check_security_headers'] : false,
        ];

        unset(
            $data['check_uptime'],
            $data['check_ssl'],
            $data['check_php'],
            $data['check_domain'],
            $data['check_security_headers']
        );

        $monitor = $this->model->create($data);
        $monitor->settings()->create($settingsData);
        $monitor->checkResult()->create([]);

        return $monitor->fresh(['user', 'settings', 'checkResult']);
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

        $settingsKeys = ['check_uptime', 'check_ssl', 'check_php', 'check_domain', 'check_security_headers'];
        $settingsData = [];
        foreach ($settingsKeys as $key) {
            if (array_key_exists($key, $data)) {
                $settingsData[$key] = $data[$key];
                unset($data[$key]);
            }
        }

        if (!empty($settingsData)) {
            $monitor->settings()->updateOrCreate(['monitor_id' => $monitor->id], $settingsData);
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