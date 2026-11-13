<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Models\Storages;

use GAMB_DUPLICA_WP_Log;
use GAMB_DUPLICA_WP_Package;
use GAMB_DUPLICA_WP_Package_Upload_Info;
use GambDuplicaWP\Core\Views\TplMng;

class UnknownStorage extends AbstractStorageEntity
{
    /**
     * Get storage adapter
     *
     * @return AbstractStorageAdapter
     */
    protected function getAdapter()
    {
        throw new \Exception('Unknown storage does not have an adapter');
    }

    /**
     * Return the storage type
     *
     * @return int
     */
    public static function getSType()
    {
        return -1000;
    }

    /**
     * Returns the FontAwesome storage type icon.
     *
     * @return string Returns the font-awesome icon
     */
    public static function getStypeIcon(): string
    {
        return '';
    }

    /**
     * Get priority, used to sort storages.
     * 100 is neutral value, 0 is the highest priority
     *
     * @return int
     */
    public static function getPriority()
    {
        return 10000;
    }

    /**
     * Get upload chunk size in bytes
     *
     * @return int bytes size, -1 unlimited
     */
    public function getUploadChunkSize()
    {
        return -1;
    }

    /**
     * Get download chunk size in bytes
     *
     * @return int bytes
     */
    public function getDownloadChunkSize()
    {
        return -1;
    }


    /**
     * Returns the storage type name.
     *
     * @return string
     */
    public static function getStypeName()
    {
        return __('Unknown', 'gamb-duplica-wp');
    }

    /**
     * Get location string
     *
     * @return string
     */
    public function getLocationString()
    {
        return __('Unknown', 'gamb-duplica-wp');
    }

    /**
     * Get HTML location link
     *
     * @return string
     */
    public function getHtmlLocationLink()
    {
        return '<span>' . __('Unknown', 'gamb-duplica-wp') . '</span>';
    }

    /**
     * Save entity
     *
     * @return bool True on success, or false on error.
     */
    public function save(): bool
    {
        // Isn't possibile save unknown storage
        return false;
    }

    /**
     * Check if storage is valid
     *
     * @return bool Return true if storage is valid and ready to use, false otherwise
     */
    public function isValid(): bool
    {
        return false;
    }

    /**
     * Is type selectable
     *
     * @return bool
     */
    public static function isSelectable(): bool
    {
        return false;
    }

    /**
     * List quick view
     *
     * @param bool $echo Echo or return
     *
     * @return string
     */
    public function getListQuickView($echo = true)
    {
        ob_start();
        ?>
        <div>
            <label><?php esc_html_e('Unknown storage type', 'gamb-duplica-wp') ?></label>
        </div>
        <?php
        if ($echo) {
            ob_end_flush();
            return '';
        } else {
            return (string) ob_get_clean();
        }
    }

    /**
     * Copy from default
     *
     * @param GAMB_DUPLICA_WP_Package             $package     the Backup
     * @param GAMB_DUPLICA_WP_Package_Upload_Info $upload_info the upload info
     *
     * @return void
     */
    public function copyFromDefault(GAMB_DUPLICA_WP_Package $package, GAMB_DUPLICA_WP_Package_Upload_Info $upload_info)
    {
        GAMB_DUPLICA_WP_Log::infoTrace("Copyng to Storage " . $this->name . '[ID: ' . $this->id . '] type:' . static::getStypeName());
        GAMB_DUPLICA_WP_Log::infoTrace('Do nothing sot unknown storage type');
    }

    /**
     * Purge old Backups
     *
     * @param array<string> $keepList List of Backups to keep
     *
     * @return false|string[] false on failure or array of deleted Backups
     */
    public function purgeOldPackages($keepList = [])
    {
        if ($this->config['max_packages'] <= 0) {
            return [];
        }

        GAMB_DUPLICA_WP_Log::infoTrace("Attempting to purge old Backups at " . $this->name . '[ID: ' . $this->id . '] type:' . static::getSTypeName());
        GAMB_DUPLICA_WP_Log::infoTrace('Do nothing sot unknown storage type');

        return false;
    }

    /**
     * Returns the config fields template data
     *
     * @return array<string, mixed>
     */
    protected function getConfigFieldsData()
    {
        return $this->getDefaultConfigFieldsData();
    }

    /**
     * Returns the default config fields template data
     *
     * @return array<string, mixed>
     */
    protected function getDefaultConfigFieldsData()
    {
        return ['storage' => $this];
    }

    /**
     * Returns the config fields template path
     *
     * @return string
     */
    protected function getConfigFieldsTemplatePath(): string
    {
        return 'admin_pages/storages/configs/unknown';
    }
}
