<?php

namespace App\Repositories\Contracts;

interface SettingRepositoryInterface
{

    /**
     * Get all system settings formatted in array keyed by type.
     * 
     * @return array
     */
    public function getAllSettingsFormatted(): array;

    /**
     * Get setting record by type.
     * 
     * @param string $type
     * @return mixed
     */
    public function getSettingByType(string $type);

    /**
     * Get setting array value by type with fallback defaults.
     * 
     * @param string $type
     * @param array $defaults
     * @return array
     */
    public function getSettingArray(string $type, array $defaults = []): array;

    /**
     * Save/Update setting by type.
     * 
     * @param string $type
     * @param array $data
     * @return mixed
     */
    public function saveSetting(string $type, array $data);

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
     * @return mixed
     */
    public function updateSettings(array $data);
}

