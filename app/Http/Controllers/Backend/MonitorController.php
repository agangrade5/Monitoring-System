<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\MonitorRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\Backend\Monitor\MonitorUserRequest;
use App\Services\MonitorService;
use App\Helpers\UtilityHelper;

class MonitorController extends Controller
{
    /**
     * Constructor to inject the Monitor Repository and Monitor Service.
     *
     * @param MonitorRepositoryInterface $monitorRepository
     * @param MonitorService $monitorService
     */
    public function __construct(
        protected MonitorRepositoryInterface $monitorRepository,
        protected MonitorService $monitorService
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return View
     * 
     * This method fetches monitors from the repository
     * filtered for the current authenticated user (or all if admin)
     * and passes them to the view.
     */
    public function index()
    {
        $user = auth()->user();
        $userId = ($user && !$user->hasRole('admin')) ? $user->id : null;
        $monitors = $this->monitorRepository->getAll(request('search'), $userId);
        $title = 'Monitor Websites & Domains';
        return view(
            'backend.monitor.index',
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
     * Display the specified monitor single detail overview.
     *
     * @param int $id
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function show(int $id)
    {
        $monitor = $this->monitorRepository->findById($id);
        abort_if(!$monitor, 404);
        $this->checkMonitorOwnership($monitor);

        $title = $monitor->name . ' - Health & Performance Overview';

        return view('backend.monitor.show', compact('monitor', 'title'));
    }

    /**
     * Store a newly created monitor in storage.
     *
     * @param  Request  $request
     * 
     * @return RedirectResponse
     */
    public function store(MonitorUserRequest $request)
    {
        $validated = $request->validated();
        $userId = auth()->user()->id;

        $urls = $validated['urls'] ?? (isset($validated['url']) ? [$validated['url']] : []);
        $urls = array_values(array_unique(array_filter($urls)));

        if (empty($urls)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Please provide at least one valid website URL or domain.');
        }

        $baseName = $validated['name'];
        $createdCount = 0;

        foreach ($urls as $url) {
            $host = parse_url($url, PHP_URL_HOST) ?: $url;
            $monitorName = (count($urls) > 1)
                ? "{$baseName} - {$host}"
                : $baseName;

            $monitorData = [
                'user_id' => $userId,
                'name' => $monitorName,
                'email' => $validated['email'],
                'url' => $url,
                'is_active' => $validated['is_active'] ?? true,
                'check_uptime' => $request->has('check_uptime'),
                'check_ssl' => $request->has('check_ssl'),
                'check_php' => $request->has('check_php'),
                'check_domain' => $request->has('check_domain'),
                'check_security_headers' => $request->has('check_security_headers'),
            ];

            $monitor = $this->monitorRepository->create($monitorData);
            $createdCount++;

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */
            UtilityHelper::customActivityLog(
                'monitor',
                'New monitor created successfully.',
                $monitor,
                [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );

            /*
            * Run all background monitor health checks via Service layer
            */
            $this->monitorService->runAllChecks($monitor->id);
        }

        /*
        * Redirect to index page with success message
        */
        $message = ($createdCount > 1)
            ? "{$createdCount} Websites / Monitors created successfully."
            : 'Website / Monitor created successfully.';

        return redirect()
            ->route('monitor')
            ->with('success', $message);
    }

    /**
     * Edit the specified resource.
     *
     * @param int $id
     * 
     * @return View
     */
    public function edit(int $id)
    {
        $monitor = $this->monitorRepository->findById($id);
        abort_if(!$monitor, 404);
        $this->checkMonitorOwnership($monitor);

        return view('backend.monitor.edit', compact('monitor'));
    }

    /**
     * Update the specified monitor in storage.
     *
     * @param  Request  $request
     * @param int $id
     * 
     * @return RedirectResponse
     */
    public function update(MonitorUserRequest $request, int $id)
    {
        $monitor = $this->monitorRepository->findById($id);
        abort_if(!$monitor, 404);
        $this->checkMonitorOwnership($monitor);

        $validated = $request->validated();
        $url = $validated['url'] ?? ($validated['urls'][0] ?? $monitor->url);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'url' => $url,
            'check_uptime' => $request->has('check_uptime'),
            'check_ssl' => $request->has('check_ssl'),
            'check_php' => $request->has('check_php'),
            'check_domain' => $request->has('check_domain'),
            'check_security_headers' => $request->has('check_security_headers'),
        ];

        if (isset($validated['is_active'])) {
            $updateData['is_active'] = $validated['is_active'];
        }

        $this->monitorRepository->update($id, $updateData);
        $monitor = $this->monitorRepository->findById($id);

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */
       UtilityHelper::customActivityLog(
            'monitor',
            'Monitor updated successfully.',
            $monitor,
            [
                'id' => $id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );


        // Run checks after update
        /*
        * Run all background monitor health checks via Service layer
        */
        $this->monitorService->runAllChecks($id);

        /*
        * Redirect to index page with success message
        */
        return redirect()
            ->route('monitor')
            ->with('success', 'Website / Monitor updated successfully.');
    }

    /**
     * Deletes a monitor by its ID.
     *
     * @param int $id The ID of the monitor to delete.
     *
     * @return RedirectResponse Redirects to the index page with a success message.
     */
    public function destroy(int $id, Request $request)
    {
        $monitor = $this->monitorRepository->findById($id);
        abort_if(!$monitor, 404);
        $this->checkMonitorOwnership($monitor);

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */
       UtilityHelper::customActivityLog(
            'monitor',
            'Monitor deleted successfully.',
            $monitor,
            [
                'id' => $id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        $this->monitorRepository->delete($id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Monitor deleted successfully.',
            ]);
        }

        return redirect()
            ->route('monitor')
            ->with('success', 'Monitor deleted successfully.');
    }

    /**
     * Toggle the active status of a monitor.
     *
     * @param int $id
     * 
     * @return RedirectResponse
     */
    public function toggleActive(Request $request,int $id)
    {
        $monitor = $this->monitorRepository->findById($id);
        abort_if(!$monitor, 404);
        $this->checkMonitorOwnership($monitor);

        $newStatus = !$monitor->is_active;
        $this->monitorRepository->update($id, [
            'is_active' => $newStatus
        ]);

      /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */
       UtilityHelper::customActivityLog(
            'monitor',
            'Monitor status updated successfully.',
            $monitor,
            [
                'id' => $id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );
        return redirect()
            ->back()
            ->with('success', 'Monitor status updated successfully.');
    }

    /**
     * Trigger an immediate check for a specific monitor.
     *
     * @param int $id
     * 
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function triggerCheck(Request $request,int $id)
    {
        $monitor = $this->monitorRepository->findById($id);
        abort_if(!$monitor, 404);
        $this->checkMonitorOwnership($monitor);

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */
       UtilityHelper::customActivityLog(
            'monitor',
            'Monitor check triggered successfully.',
            $monitor,
            [
                'id' => $id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );
        try {
            $this->monitorService->runAllChecks($id);
            $message = "Website checks triggered and updated successfully for {$monitor->name}.";
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return redirect()
                ->back()
                ->with('success', $message);
        } catch (\Throwable $e) {
            $errorMsg = 'An error occurred while running the checks: ' . $e->getMessage();

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', $errorMsg);
        }
    }

    /**
     * Send a test notification email for a specific monitor.
     *
     * @param Request $request
     * @param int $id
     * 
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function sendTestNotification(Request $request, int $id)
    {
        $monitor = $this->monitorRepository->findById($id);
        abort_if(!$monitor, 404);
        $this->checkMonitorOwnership($monitor);

        $result = $this->monitorService->sendTestNotification($id, auth()->user());

        if (!$result['success']) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($result, $result['code'] ?? 400);
            }
            return redirect()->back()->with('error', $result['message']);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    /**
     * Check if the authenticated user has ownership of the monitor or is an admin.
     *
     * @param mixed $monitor
     * @return void
     */
    protected function checkMonitorOwnership($monitor): void
    {
        $user = auth()->user();
        if ($user && !$user->hasRole('admin') && $monitor->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this monitor.');
        }
    }
}