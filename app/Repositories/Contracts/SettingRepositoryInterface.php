<?php

namespace App\Repositories\Contracts;

interface SettingRepositoryInterface
{
    /**
     * Method getSettings
     * 
     * @return array
     * 
     */
    public function getSettings();

    /**
     * Method updateNotification
     * 
     * @param string $setting
     * @param bool $value
     * 
     */
     public function updateNotification(string $setting, bool $value);
}