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
        // Default values
        $defaults = config('constants.settings', []);

        $result = [];

        foreach ($defaults as $type => $defaultValues) {

            if (
                isset($allSettings[$type]) &&
                !empty($allSettings[$type]->value)
            ) {
                $decoded = json_decode(
                    $allSettings[$type]->value,
                    true
                );

                $merged = is_array($decoded)
                    ? array_merge($defaultValues, $decoded)
                    : $defaultValues;
            } else {
                $merged = $defaultValues;
            }

            // Decrypted values
            if (isset($merged['mail_password'])) {
                $merged['mail_password_decrypted'] =
                    $this->decryptValue($merged['mail_password']);
            }

            if (isset($merged['twilio_auth_token'])) {
                $merged['twilio_auth_token_decrypted'] =
                    $this->decryptValue($merged['twilio_auth_token']);
            }

            if (isset($merged['aws_secret_access_key'])) {
                $merged['aws_secret_access_key_decrypted'] =
                    $this->decryptValue($merged['aws_secret_access_key']);
            }

            $result[$type] = $merged;
        }

        return $result;
    }

    /**
     * Get setting record by type.
     *
     * @param string $type
     * 
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
     * 
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
     * 
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

