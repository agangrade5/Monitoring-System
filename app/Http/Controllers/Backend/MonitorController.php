<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\MonitorRepositoryInterface;
use Illuminate\Http\Request;
use App\Rules\{NoScripts, ValidEmailDomain, ValidUrl, ValidMobile};
use App\Jobs\CheckUptimeJob;
use App\Jobs\CheckSslCertificateJob;
use App\Jobs\CheckPhpVersionJob;
use App\Jobs\CheckDomainExpiryJob;
use App\Jobs\CheckSecurityHeadersJob;
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
    public function index(){
        $monitors = $this->monitorRepository->getAll(request('search'));
        $title = 'Monitor Websites & Domains';
        return view(
            'backend.user.monitor.index',
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
        return view('backend.user.monitor.create');
    }

    /**
     * Display the specified monitor single detail overview.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show(int $id)
    {
        $monitor = $this->monitorRepository->findById($id);
        abort_if(!$monitor, 404);

        $title = $monitor->name . ' - Health & Performance Overview';

        return view('backend.user.monitor.show', compact('monitor', 'title'));
    }

/**
 * Store a newly created monitor in storage.
 *
 * @param  Request  $request
 * @return RedirectResponse
 */
    public function store(Request $request)
    {
         $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
            new NoScripts(),
        ],

        'email' => [
            'nullable',
            'string',
            'email',
            'max:50',
            'required_without:mobile',
            new NoScripts(),
            new ValidEmailDomain(),
        ],

        'mobile' => [
            'nullable',
            'string',
            'max:15',
            'required_without:email',
            new ValidMobile(),
        ],

        'url' => [
            'nullable',
            'string',
            'max:255',
            new ValidUrl(),
        ],

        'is_active' => [
            'nullable',
            'boolean',
        ],
    ]);

        $validated['user_id'] = auth()->id() ?? 1;
        $monitor = $this->monitorRepository->create($validated);

        if (function_exists('activity')) {
            activity('monitor')
                ->causedBy(auth()->user())
                ->performedOn($monitor)
                ->log("Created monitor: {$monitor->name}");
        }

        /*
        * Dispatch jobs to check uptime, ssl certificate, php version and domain expiry
        */
        CheckUptimeJob::dispatchSync($monitor->id);
        CheckSslCertificateJob::dispatchSync($monitor->id);
        CheckPhpVersionJob::dispatchSync($monitor->id);
        CheckDomainExpiryJob::dispatchSync($monitor->id);
        CheckSecurityHeadersJob ::dispatchSync($monitor->id);
        /*
        * Redirect to index page with success message
        */
        return redirect()
            ->route('monitor')
            ->with('success', 'Website / Monitor created successfully.');
    }

/**
 * Edit the specified resource.
 *
 * @param int $id
 * @return View
 */
    public function edit(int $id)
    {
        $monitor = $this->monitorRepository->findById($id);
        abort_if(!$monitor, 404);
        return view('backend.user.monitor.edit', compact('monitor'));
    }

/**
 * Update the specified monitor in storage.
 *
 * @param  Request  $request
 * @param int $id
 * @return RedirectResponse
 */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
            new NoScripts(),
        ],

        'email' => [
            'nullable',
            'string',
            'email',
            'max:50',
            'required_without:mobile',
            new NoScripts(),
            new ValidEmailDomain(),
        ],

        'mobile' => [
            'nullable',
            'string',
            'max:15',
            'required_without:email',
            new ValidMobile(),
        ],

        'url' => [
            'nullable',
            'string',
            'max:255',
            new ValidUrl(),
        ],

        'is_active' => [
            'nullable',
            'boolean',
        ],
    ]);

        $this->monitorRepository->update($id, $validated);
        $monitor = $this->monitorRepository->findById($id);

        if (function_exists('activity') && $monitor) {
            activity('monitor')
                ->causedBy(auth()->user())
                ->performedOn($monitor)
                ->log("Updated monitor: {$monitor->name}");
        }

        // Run checks after update
        /*
        * Dispatch jobs to check uptime, ssl certificate, php version and domain expiry
        */
        CheckUptimeJob::dispatchSync($id);
        CheckSslCertificateJob::dispatchSync($id);
        CheckPhpVersionJob::dispatchSync($id);
        CheckDomainExpiryJob::dispatchSync($id);
        CheckSecurityHeadersJob ::dispatchSync($id);
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
    public function destroy(int $id)
    {
        $monitor = $this->monitorRepository->findById($id);
        if (function_exists('activity')) {
            activity('monitor')
                ->causedBy(auth()->user())
                ->log("Deleted monitor: " . ($monitor->name ?? "#{$id}"));
        }

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
        $newStatus = !$monitor->is_active;
        $this->monitorRepository->update($id, [
            'is_active' => $newStatus
        ]);

        if (function_exists('activity')) {
            activity('monitor')
                ->causedBy(auth()->user())
                ->performedOn($monitor)
                ->log(($newStatus ? "Resumed" : "Paused") . " monitor: {$monitor->name}");
        }

        return redirect()
            ->back()
            ->with('success', 'Monitor status updated successfully.');
    }

    /**
     * Trigger an immediate check for a specific monitor.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function triggerCheck(int $id)
    {
        $monitor = $this->monitorRepository->findById($id);
        abort_if(!$monitor, 404);

        if (function_exists('activity')) {
            activity('monitor')
                ->causedBy(auth()->user())
                ->performedOn($monitor)
                ->log("Triggered instant health check for: {$monitor->name}");
        }

        try {
            CheckUptimeJob::dispatchSync($id);
            CheckSslCertificateJob::dispatchSync($id);
            CheckPhpVersionJob::dispatchSync($id);
            CheckDomainExpiryJob::dispatchSync($id);
            CheckSecurityHeadersJob::dispatchSync($id);

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
}