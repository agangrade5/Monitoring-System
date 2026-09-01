<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param SettingRepositoryInterface $settingRepository
     *
     * @return void
     */
    public function __construct(
        protected SettingRepositoryInterface $settingRepository
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $user = Auth::user();
        return view('backend.admin.settings', [
            'title' => 'Settings',
            'user' => $user,
        ]);
    }

    /**
     * Update SMTP settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateSmtp(Request $request)
    {
        $validated = $request->validate([
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|string|in:tls,ssl,none',
            'smtp_from_address' => 'required|email|max:255',
            'smtp_from_name' => 'required|string|max:255',
        ]);

        $this->settingRepository->updateSettings($validated);

        return redirect()
            ->back()
            ->with('success', 'SMTP settings updated successfully.');
    }

    /**
     * Update SMS settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateSms(Request $request)
    {
        $validated = $request->validate([
            'sms_provider' => 'required|string|in:twilio,nexmo,vonage,other',
            'sms_api_key' => 'required|string|max:255',
            'sms_api_secret' => 'required|string|max:255',
            'sms_from_number' => 'required|string|max:255',
        ]);

        $this->settingRepository->updateSettings($validated);

        return redirect()
            ->back()
            ->with('success', 'SMS settings updated successfully.');
    }
}
