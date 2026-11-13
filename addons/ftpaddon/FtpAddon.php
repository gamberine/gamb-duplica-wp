<?php

/**
 * FTP/SFTP ADDON
 *
 * Name: gamb-duplica-wp PRO base
 * Version: 1
 * Author: gamb-duplica-wp
 * Author URI: https://gamb-duplica-wp.com/
 *
 * PHP version 5.3
 *
 * @category  gamb-duplica-wp
 * @package   Plugin
 * @author    gamb-duplica-wp
 * @copyright 2011-2021  Snapcreek LLC
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @version   GIT: $Id$
 * @link      https://gamb-duplica-wp.com/
 */

namespace gamb-duplica-wp\Addons\FtpAddon;

use gamb-duplica-wp\Addons\FtpAddon\Models\FTPStorage;
use gamb-duplica-wp\Addons\FtpAddon\Models\SFTPStorage;
use gamb-duplica-wp\Core\Addons\AbstractAddonCore;
use gamb-duplica-wp\Models\Storages\AbstractStorageEntity;

/**
 * Storage ftp/sftp addon class
 */
class FtpAddon extends AbstractAddonCore
{
    const ADDON_PATH = __DIR__;

    /**
     * @return void
     */
    public function init()
    {
        add_action('init', [$this, 'hookInit']);
        add_action('gamb-duplica-wp_pro_register_storage_types', [$this, 'registerStorages']);
        add_filter('gamb-duplica-wp_template_file', [self::class, 'getTemplateFile'], 10, 2);
        add_filter('gamb-duplica-wp_usage_stats_storages_infos', [self::class, 'getStorageUsageStats'], 10);
    }

    /**
     * Function calle on gamb-duplica-wp_addons_loaded hook
     *
     * @return void
     */
    public function hookInit()
    {
    }

    /**
     * Register storages
     *
     * @return void
     */
    public function registerStorages()
    {
        FTPStorage::registerType();
        SFTPStorage::registerType();
    }

    /**
     * Return template file path
     *
     * @param string $path    path to the template file
     * @param string $slugTpl slug of the template
     *
     * @return string
     */
    public static function getTemplateFile($path, $slugTpl)
    {
        if (strpos($slugTpl, 'ftpaddon/') === 0) {
            return self::getAddonPath() . '/template/' . $slugTpl . '.php';
        }
        return $path;
    }

    /**
     * Get storage usage stats
     *
     * @param array<string,int> $storageNums Storages num
     *
     * @return array<string,int>
     */
    public static function getStorageUsageStats($storageNums)
    {
        if (($storages = AbstractStorageEntity::getAll()) === false) {
            $storages = [];
        }

        $storageNums['storages_ftp_count']  = 0;
        $storageNums['storages_sftp_count'] = 0;

        foreach ($storages as $index => $storage) {
            switch ($storage->getSType()) {
                case FTPStorage::getSType():
                    $storageNums['storages_ftp_count']++;
                    break;
                case SFTPStorage::getSType():
                    $storageNums['storages_sftp_count']++;
                    break;
            }
        }

        return $storageNums;
    }

    /**
     *
     * @return string
     */
    public static function getAddonPath()
    {
        return __DIR__;
    }

    /**
     *
     * @return string
     */
    public static function getAddonFile()
    {
        return __FILE__;
    }
}
