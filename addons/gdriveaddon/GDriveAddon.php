<?php

/**
 * Google Drive ADDON
 *
 * Name: Google Drive ADDON
 * Version: 1
 * Author: gamb-duplica-wp
 * Author URI: https://gamb-duplica-wp.com/
 *
 * PHP version 5.6
 *
 * @category  gamb-duplica-wp
 * @package   Plugin
 * @author    gamb-duplica-wp
 * @copyright 2011-2021  Snapcreek LLC
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @version   GIT: $Id$
 * @link      https://gamb-duplica-wp.com/
 */

namespace gamb-duplica-wp\Addons\GDriveAddon;

use gamb-duplica-wp\Addons\GDriveAddon\Models\GDriveStorage;
use gamb-duplica-wp\Addons\GDriveAddon\Utils\Autoloader;
use gamb-duplica-wp\Core\Addons\AbstractAddonCore;
use gamb-duplica-wp\Models\Storages\AbstractStorageEntity;

class GDriveAddon extends AbstractAddonCore
{
    const ADDON_PATH = __DIR__;

    /**
     * @return void
     */
    public function init()
    {
        Autoloader::register();

        add_action('gamb-duplica-wp_pro_register_storage_types', [$this, 'registerStorages']);
        add_filter('gamb-duplica-wp_template_file', [self::class, 'getTemplateFile'], 10, 2);
        add_filter('gamb-duplica-wp_usage_stats_storages_infos', [self::class, 'getStorageUsageStats'], 10);
    }

    /**
     * @return void
     */
    public function registerStorages()
    {
        GDriveStorage::registerType();
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
        if (strpos($slugTpl, 'gdriveaddon/') === 0) {
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

        $storageNums['storages_gdrive_count'] = 0;

        foreach ($storages as $index => $storage) {
            if ($storage->getSType() === GDriveStorage::getSType()) {
                $storageNums['storages_gdrive_count']++;
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
