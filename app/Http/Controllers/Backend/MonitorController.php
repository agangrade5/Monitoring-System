<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\MonitorRepositoryInterface;
use App\Models\Monitor;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
/**
 * Constructor to inject the Monitor Repository.
 *
 * @param MonitorRepositoryInterface $monitorRepository
 */
    public function __construct(
        protected MonitorRepositoryInterface $monitorRepository
    ) {}

/**
 * Display a listing of the resource.
 *
 * @return View
 * 
 * This method fetches all monitors from the repository
 * and passes them to the view.
 */
  public function index()
{
    $monitors = $this->monitorRepository->getAll(request('search'));

    $title = 'Settings';

    return view(
        'backend.user.monitor',
        compact('monitors', 'title')
    );
}

/**
 * This method renders the view to create a new monitor.
 *
 * @return View
 */
    public function create()
    {
        return view('backend.monitor.create');
    }

/**
 * Store a newly created monitor in storage.
 *
 * @param  Request  $request
 * @return RedirectResponse
 *
 * This method validates the request data and creates a new monitor
 * using the validated data. It then redirects to the index page with
 * a success message.
 */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:255',
            'ip_address' => 'nullable|string|max:255',
            'type' => 'required|in:website,server,api',
            'check_interval' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $this->monitorRepository->create($validated);

        return redirect()
            ->route('monitor')
            ->with('success', 'Monitor created successfully.');
    }

/**
 * Edit the specified resource.
 *
 * @param int $id
 * @return View
 *
 * This method fetches a monitor by its ID and renders the edit view.
 * If the monitor is not found, it aborts with a 404 status code.
 */
    public function edit(int $id)
    {
        $monitor = $this->monitorRepository->findById($id);

        abort_if(!$monitor, 404);

        return view('backend.monitor.edit', compact('monitor'));
    }

/**
 * Update the specified monitor in storage.
 *
 * @param  Request  $request
 * @param int $id
 * @return RedirectResponse
 *
 * This method validates the request data and updates a monitor
 * using the validated data. It then redirects to the index page with
 * a success message.
 */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:255',
            'ip_address' => 'nullable|string|max:255',
            'type' => 'required|in:website,server,api',
            'check_interval' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $this->monitorRepository->update($id, $validated);

        return redirect()
            ->route('monitor')
            ->with('success', 'Monitor updated successfully.');
    }

/**
 * Deletes a monitor by its ID.
 *
 * @param int $id The ID of the monitor to delete.
 *
 * @return RedirectResponse Redirects to the index page with a success message.
 */
    public function destroy(int $id)
    {
        $this->monitorRepository->delete($id);

        return redirect()
            ->route('monitor')
            ->with('success', 'Monitor deleted successfully.');
    }

    /**
     * Toggle the active status of a monitor.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function toggleActive(int $id)
    {
        $monitor = $this->monitorRepository->findById($id);

        abort_if(!$monitor, 404);

        $this->monitorRepository->update($id, [
            'is_active' => !$monitor->is_active
        ]);

        return redirect()
            ->back()
            ->with('success', 'Monitor status updated successfully.');
    }
}