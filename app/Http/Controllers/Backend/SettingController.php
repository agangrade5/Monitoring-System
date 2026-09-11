<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Models\Setting;
use App\Http\Requests\Backend\Setting\OtpSettingRequest;
use App\Http\Requests\Backend\Setting\TwilioSettingRequest;
use App\Http\Requests\Backend\Setting\EmailSettingRequest;
use App\Http\Requests\Backend\Setting\AwsSettingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\UtilityHelper;

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
        $payload = [
            'email' => [
                'enabled' => (bool) $request->input('email_enabled', false),
                'down_event' => (bool) $request->input('email_down_event', false),
                'up_event' => (bool) $request->input('email_up_event', false),
                'ssl_domain_expiry' => (bool) $request->input('email_ssl_domain_expiry', false),
            ],
            'sms' => [
                'enabled' => (bool) $request->input('sms_enabled', false),
                'down_event' => (bool) $request->input('sms_down_event', false),
                'up_event' => (bool) $request->input('sms_up_event', false),
                'ssl_domain_expiry' => (bool) $request->input('sms_ssl_domain_expiry', false),
            ],
        ];

        $setting = $this->settingRepository->saveSetting('notifications', $payload, Auth::id());

        UtilityHelper::customActivityLog(
            'setting',
            'Updated Alert Notification Settings successfully.',
            $setting,
            [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

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
        $payload = [
            'report_email' => [
                'enabled' => (bool) $request->input('report_email_enabled', false),
                'Weekly' => (bool) $request->input('report_weekly', false),
                'Monthly' => (bool) $request->input('report_monthly', false),
            ],
        ];

        $setting = $this->settingRepository->saveSetting('report', $payload, Auth::id());

        UtilityHelper::customActivityLog(
            'setting',
            'Updated Email Report Settings successfully.',
            $setting,
            [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

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
}
