<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;

class SettingRepository implements SettingRepositoryInterface
{

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

    /**
     * Updates settings with the given data.
     */
    public function updateSettings(array $data)
    {
        $settings = $this->getSettings();
        $settings->update($data);
        return $settings;
    }
}

