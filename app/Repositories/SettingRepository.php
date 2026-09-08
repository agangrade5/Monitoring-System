<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class SettingRepository implements SettingRepositoryInterface
{
    /**
     * Helper to safely decrypt a string value if encrypted.
     * 
     * @param ?string $value
     * @return string
     */
    public function decryptValue(?string $value): string
    {
        if (empty($value)) {
            return '';
        }
        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            return $value;
        }
    }

    /**
     * Get all system settings formatted in array keyed by type.
     * 
     * @return array
     */
    public function getAllSettingsFormatted(): array
    {
        $allSettings = Setting::all()->keyBy('type');

        $defaults = [
            'otp' => [
                'max_time' => 60,
                'otp_length' => 6,
                'is_default' => true,
                'default' => '999999',
            ],
            'twilio' => [
                'enable_twilio' => false,
                'twilio_account_sid' => '',
                'twilio_auth_token' => '',
                'twilio_from_number' => '',
            ],
            'email' => [
                'mail_mailer' => 'smtp',
                'mail_host' => '',
                'mail_port' => '587',
                'mail_encryption' => 'tls',
                'mail_username' => '',
                'mail_password' => '',
                'mail_from_address' => '',
                'mail_from_name' => '',
            ],
            'aws' => [
                'aws_access_key_id' => '',
                'aws_secret_access_key' => '',
                'aws_default_region' => 'us-east-1',
                'aws_bucket' => '',
            ],
        ];

        $result = [];
        foreach ($defaults as $type => $defaultValues) {
            if (isset($allSettings[$type]) && !empty($allSettings[$type]->value)) {
                $decoded = json_decode($allSettings[$type]->value, true);
                $merged = is_array($decoded) ? array_merge($defaultValues, $decoded) : $defaultValues;

                // Automatically provide decrypted values for password / secret fields
                if (isset($merged['mail_password'])) {
                    $merged['mail_password_decrypted'] = $this->decryptValue($merged['mail_password']);
                }
                if (isset($merged['twilio_auth_token'])) {
                    $merged['twilio_auth_token_decrypted'] = $this->decryptValue($merged['twilio_auth_token']);
                }
                if (isset($merged['aws_secret_access_key'])) {
                    $merged['aws_secret_access_key_decrypted'] = $this->decryptValue($merged['aws_secret_access_key']);
                }

                $result[$type] = $merged;
            } else {
                $result[$type] = $defaultValues;
            }
        }

        return $result;
    }

    /**
     * Get setting record by type.
     * 
     * @param string $type
     * @return ?Setting
     */
    public function getSettingByType(string $type): ?Setting
    {
        return Setting::where('type', $type)->first();
    }

    /**
     * Get setting array value by type with fallback defaults.
     * 
     * @param string $type
     * @param array $defaults
     * @return array
     */
    public function getSettingArray(string $type, array $defaults = []): array
    {
        $setting = $this->getSettingByType($type);
        if ($setting && !empty($setting->value)) {
            $decoded = json_decode($setting->value, true);
            if (is_array($decoded)) {
                return array_merge($defaults, $decoded);
            }
        }
        return $defaults;
    }

    /**
     * Save/Update setting by type.
     * 
     * @param string $type
     * @param array $data
     * @return Setting
     */
    public function saveSetting(string $type, array $data): Setting
    {
        return Setting::updateOrCreate(
            ['type' => $type],
            ['value' => json_encode($data)]
        );
    }

    /**
     * Method updateNotification
     * 
     * @param string $setting
     * @param bool $value
     * 
     * @return Setting
     *  
     */ 
    public function updateNotification(string $setting, bool $value)
    {
        $settingObj = Setting::first();
        if ($settingObj) {
            $settingObj->update([
                $setting => $value,
            ]);
        }
        return $settingObj;
    }

    /**
     * Updates settings with the given data.
     */
    public function updateSettings(array $data)
    {
        $settingObj = Setting::first();
        if ($settingObj) {
            $settingObj->update($data);
        }
        return $settingObj;
    }
}

