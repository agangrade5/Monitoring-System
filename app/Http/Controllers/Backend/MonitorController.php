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
        /*
        * Dispatch jobs to check uptime, ssl certificate, php version and domain expiry
        */
        CheckUptimeJob::dispatchSync($monitor->id);
        CheckSslCertificateJob::dispatchSync($monitor->id);
        CheckPhpVersionJob::dispatchSync($monitor->id);
        CheckDomainExpiryJob::dispatchSync($monitor->id);
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
        // Run checks after update
        /*
        * Dispatch jobs to check uptime, ssl certificate, php version and domain expiry
        */
    CheckUptimeJob::dispatchSync($id);
    CheckSslCertificateJob::dispatchSync($id);
    CheckPhpVersionJob::dispatchSync($id);
    CheckDomainExpiryJob::dispatchSync($id);
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