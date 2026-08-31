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

     /**
      * Method updateSettings
      * 
      * @param array $data
      * @return Setting
      */
     public function updateSettings(array $data);
}