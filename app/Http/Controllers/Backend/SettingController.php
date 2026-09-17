<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Http\Requests\Backend\Setting\{
    OtpSettingRequest,
    TwilioSettingRequest,
    EmailSettingRequest,
    AwsSettingRequest,
    EnableGoogle2faRequest
};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, Crypt};
use App\Helpers\UtilityHelper;
use App\Services\GoogleTwoFactorService;

class SettingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param SettingRepositoryInterface $settingRepository
     * @param GoogleTwoFactorService $googleTwoFactorService
     *
     * @return void
     */
    public function __construct(
        protected SettingRepositoryInterface $settingRepository,
        private readonly GoogleTwoFactorService $googleTwoFactorService,
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
        $settings = $this->settingRepository->getAllSettingsFormatted($user?->id);
        return view('backend.admin.settings', [
            'title' => 'Settings',
            'user' => $user,
            'settings' => $settings,
            'otpData' => $settings['otp'],
            'twilioData' => $settings['twilio'],
            'mailData' => $settings['mail'],
            'awsData' => $settings['aws'],
            'notificationData' => $settings['notifications'],
            'reportData' => $settings['report'],
        ]);
    }

    /**
     * Update Notification settings in database settings table.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateNotificationSettings(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $notificationDefaults = config('constants.user_defaults.notifications');

        // Fetch existing notification settings before update to detect master switch toggle changes
        $existing = $this->settingRepository->getSettingArray('notifications', [], $userId);
        $prevEmailEnabled = isset($existing['email']['enabled'])
            ? (bool) $existing['email']['enabled']
            : (bool) ($notificationDefaults['email']['enabled'] ?? false);
        $prevSmsEnabled = isset($existing['sms']['enabled'])
            ? (bool) $existing['sms']['enabled']
            : (bool) ($notificationDefaults['sms']['enabled'] ?? false);

        $newEmailEnabled = $request->boolean('email_enabled');
        $newSmsEnabled = $request->boolean('sms_enabled');

        $payload = [
            'email' => array_combine(
                array_keys($notificationDefaults['email']),
                [
                    $newEmailEnabled,
                    $request->boolean('email_down_event'),
                    $request->boolean('email_up_event'),
                    $request->boolean('email_ssl_domain_expiry'),
                ]
            ),

            'sms' => array_combine(
                array_keys($notificationDefaults['sms']),
                [
                    $newSmsEnabled,
                    $request->boolean('sms_down_event'),
                    $request->boolean('sms_up_event'),
                    $request->boolean('sms_ssl_domain_expiry'),
                ]
            ),
        ];

        $setting = $this->settingRepository->saveSetting('notifications', $payload, $userId);

        // Activity log ONLY when Email Master Toggle state changed
        if ($prevEmailEnabled !== $newEmailEnabled) {
            $desc = $newEmailEnabled
                ? 'Email notifications enabled.'
                : 'Email notifications disabled.';

            UtilityHelper::customActivityLog(
                'setting',
                $desc,
                $setting,
                [
                    'channel' => 'email',
                    'enabled' => $newEmailEnabled,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );
        }

        // Activity log ONLY when SMS Master Toggle state changed
        if ($prevSmsEnabled !== $newSmsEnabled) {
            $desc = $newSmsEnabled
                ? 'SMS notifications enabled.'
                : 'SMS notifications disabled.';

            UtilityHelper::customActivityLog(
                'setting',
                $desc,
                $setting,
                [
                    'channel' => 'sms',
                    'enabled' => $newSmsEnabled,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Notification settings updated successfully!',
            'data' => $payload,
        ]);
    }

    /**
     * Update Email Report settings in database settings table.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateReportSettings(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $reportDefaults = config('constants.user_defaults.report.report_email');

        // Fetch existing report settings before update to detect master switch toggle changes
        $existing = $this->settingRepository->getSettingArray('report', [], $userId);
        $prevReportEnabled = isset($existing['report_email']['enabled'])
            ? (bool) $existing['report_email']['enabled']
            : (bool) ($reportDefaults['enabled'] ?? false);

        $newReportEnabled = $request->boolean('report_email_enabled');

        $payload = [
            'report_email' => [
                'enabled' => $newReportEnabled,
                'weekly' => $request->boolean('report_weekly'),
                'monthly' => $request->boolean('report_monthly'),
            ],
        ];

        $setting = $this->settingRepository->saveSetting('report', $payload, $userId);

        // Activity log ONLY when Email Report Master Toggle state changed
        if ($prevReportEnabled !== $newReportEnabled) {
            $desc = $newReportEnabled
                ? 'Email reporting enabled.'
                : 'Email reporting disabled.';

            UtilityHelper::customActivityLog(
                'setting',
                $desc,
                $setting,
                [
                    'channel' => 'report_email',
                    'enabled' => $newReportEnabled,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Report settings updated successfully!',
            'data' => $payload,
        ]);
    }

    /**
     * Update OTP settings in database settings table.
     *
     * @param OtpSettingRequest $request
     *
     * @return JsonResponse
     */
    public function updateOtpSettings(OtpSettingRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $existingOtp = $this->settingRepository->getSettingArray('otp');

        $payload = [
            'max_time' => (int) $validated['otp_max_time'],
            'otp_length' => (int) $validated['otp_length'],
            'is_default' => (bool) $validated['otp_is_default'],
            'default' => (string) ( $validated['otp_default'] ?? $existingOtp['default'] ?? '' ),
        ];

            $setting =   $this->settingRepository->saveSetting('otp', $payload);
             /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */
            UtilityHelper::customActivityLog(
                'setting',
                'Updated OTP Settings successfully.',
                $setting,
                [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );

        $setting =   $this->settingRepository->saveSetting('otp', $payload);
            /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */
        UtilityHelper::customActivityLog(
            'Setting',
            'Updated OTP Settings successfully.',
            $setting,
            [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'OTP Settings updated successfully!',
            'data' => $payload,
        ]);
    }

    /**
     * Update Twilio settings in database settings table.
     *
     * @param TwilioSettingRequest $request
     *
     * @return JsonResponse
     */
    public function updateTwilioSettings(TwilioSettingRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $existingTwilio = $this->settingRepository->getSettingArray('twilio');

        $rawToken = (string) ($validated['twilio_auth_token'] ?? '');
        if (!empty($rawToken)) {
            $twilioAuthToken = Crypt::encryptString($rawToken);
        } else {
            $twilioAuthToken = $existingTwilio['twilio_auth_token'] ?? '';
        }

        $payload = [
            'twilio_account_sid' => (string) ($validated['twilio_account_sid'] ?? ''),
            'twilio_auth_token' => $twilioAuthToken,
            'twilio_from_number' => (string) ($validated['twilio_from_number'] ?? ''),
        ];

        $setting = $this->settingRepository->saveSetting('twilio', $payload);
          /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */
            UtilityHelper::customActivityLog(
                'setting',
                'Updated Twilio SMS Settings successfully.',
                $setting,
                [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );

        return response()->json([
            'status' => true,
            'message' => 'Twilio SMS Settings updated successfully!',
            'data' => $payload,
        ]);
    }

    /**
     * Update Email (SMTP) settings in database settings table.
     *
     * @param EmailSettingRequest $request
     *
     * @return JsonResponse
     */
    public function updateEmailSettings(EmailSettingRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $existingEmail = $this->settingRepository->getSettingArray('mail');

        $rawPassword = (string) ($validated['mail_password'] ?? '');
        if (!empty($rawPassword)) {
            $mailPassword = Crypt::encryptString($rawPassword);
        } else {
            $mailPassword = $existingEmail['mail_password'] ?? '';
        }

        $payload = [
            'mail_mailer' => (string) ($validated['mail_mailer'] ?? 'smtp'),
            'mail_host' => (string) ($validated['mail_host'] ?? ''),
            'mail_port' => (string) ($validated['mail_port'] ?? '587'),
            'mail_encryption' => (string) ($validated['mail_encryption'] ?? 'tls'),
            'mail_username' => (string) ($validated['mail_username'] ?? ''),
            'mail_password' => $mailPassword,
            'mail_from_address' => (string) ($validated['mail_from_address'] ?? ''),
            'mail_from_name' => (string) ($validated['mail_from_name'] ?? ''),
        ];

        $setting = $this->settingRepository->saveSetting('mail', $payload);
             /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */
            UtilityHelper::customActivityLog(
                'setting',
                'Updated Email (SMTP) Settings successfully.',
                $setting,
                [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );

        return response()->json([
            'status' => true,
            'message' => 'Email (SMTP) Settings updated successfully!',
            'data' => $payload,
        ]);
    }

    /**
     * Update AWS Cloud settings in database settings table.
     *
     * @param AwsSettingRequest $request
     *
     * @return JsonResponse
     */
    public function updateAwsSettings(AwsSettingRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $existingAws = $this->settingRepository->getSettingArray('aws');

        $rawSecret = (string) ($validated['aws_secret_access_key'] ?? '');
        if (!empty($rawSecret)) {
            $awsSecretKey = Crypt::encryptString($rawSecret);
        } else {
            $awsSecretKey = $existingAws['aws_secret_access_key'] ?? '';
        }

        $payload = [
            'aws_access_key_id' => (string) ($validated['aws_access_key_id'] ?? ''),
            'aws_secret_access_key' => $awsSecretKey,
            'aws_default_region' => (string) ($validated['aws_default_region'] ?? 'us-east-1'),
            'aws_bucket' => (string) ($validated['aws_bucket'] ?? ''),
        ];

        $setting = $this->settingRepository->saveSetting('aws', $payload);
            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */
            UtilityHelper::customActivityLog(
                'setting',
                'Updated AWS Cloud Settings successfully.',
                $setting,
                [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );
        return response()->json([
            'status' => true,
            'message' => 'AWS Cloud Settings updated successfully!',
            'data' => $payload,
        ]);
    }

    /**
     * Setup 2FA for the user.
     *
     * @return JsonResponse
     */
    public function twoFaSetup(): JsonResponse
    {
        $user = Auth::user();

        $secret = $this->googleTwoFactorService->generateSecretKey();

        session(['2fa_setup_secret' => $secret]);

        $qrCodeSvg = $this->googleTwoFactorService->getQrCodeSvg(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json([
            'success' => true,
            'qr_code' => $qrCodeSvg,
            'secret' => $secret,
        ]);
    }

    /**
     * Enable 2FA for the user.
     *
     * @param EnableGoogle2faRequest $request
     *
     * @return JsonResponse
     */
    public function twoFaEnable(EnableGoogle2faRequest $request): JsonResponse
    {
        $secret = session('2fa_setup_secret');

        if (!$secret) {
            return response()->json([
                'success' => false,
                'message' => 'Setup session expired, please scan the QR code again.',
            ], 422);
        }

        $isValid = $this->googleTwoFactorService->verifyKey(
            $secret,
            $request->input('one_time_password')
        );

        if (!$isValid) {
            return response()->json([
                'success' => false,
                'message' => 'The code you entered is incorrect.',
            ], 422);
        }

        $user = Auth::user();
        $user->google2fa_secret = $secret;
        $user->google2fa_enabled = true;
        $user->google2fa_enabled_at = now();
        $user->save();

        session()->forget('2fa_setup_secret');

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */
        UtilityHelper::customActivityLog(
            'auth',
            'Google 2FA enabled.',
            $user,
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Google Authenticator has been enabled for your account.',
        ]);
    }

    /**
     * Disable 2FA for the user.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function twoFaDisable(Request $request): JsonResponse
    {
        $request->validate([
            'password' => [
                'required',
                'current_password:web',
            ],
        ]);

        $user = Auth::user();
        $user->google2fa_secret = null;
        $user->google2fa_enabled = false;
        $user->google2fa_enabled_at = null;
        $user->save();

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */
        UtilityHelper::customActivityLog(
            'auth',
            'Google 2FA disabled.',
            $user,
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Google Authenticator has been disabled for your account.',
        ]);
    }
}
