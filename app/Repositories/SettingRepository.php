<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;

class SettingRepository implements SettingRepositoryInterface
{
    /**
     * Method getSettings
     * 
     * @return Setting
     * 
     */
   public function getSettings()
    {
        return Setting::firstOrCreate(
        [],
        [
            'email_notification' => 0,
            'sms_notification' => 0,
            'two_factor_authentication' => 0
        ]
    );
    }

    /**
     * Method updateNotification
     * 
     * @param string $setting
     * @param bool $value
     * @return Setting
     *  
     */ 
     public function updateNotification(string $setting, bool $value)
    {
        $settings = $this->getSettings();
        $settings->update([
            $setting => $value,
        ]);

        return $settings;
    }
}