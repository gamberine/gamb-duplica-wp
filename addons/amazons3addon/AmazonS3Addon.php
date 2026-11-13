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

namespace GambDuplicaWP\Addons\AmazonS3Addon;

use GambDuplicaWP\Addons\AmazonS3Addon\Models\AmazonS3Storage;
use GambDuplicaWP\Addons\AmazonS3Addon\Models\AmazonS3CompatibleStorage;
use GambDuplicaWP\Addons\AmazonS3Addon\Models\BackblazeStorage;
use GambDuplicaWP\Addons\AmazonS3Addon\Models\CloudflareStorage;
use GambDuplicaWP\Addons\AmazonS3Addon\Models\DigitalOceanStorage;
use GambDuplicaWP\Addons\AmazonS3Addon\Models\DreamStorage;
use GambDuplicaWP\Addons\AmazonS3Addon\Models\GoogleCloudStorage;
use GambDuplicaWP\Addons\AmazonS3Addon\Models\VultrStorage;
use GambDuplicaWP\Addons\AmazonS3Addon\Models\WasabiStorage;
use GambDuplicaWP\Addons\AmazonS3Addon\Utils\Autoloader;
use GambDuplicaWP\Core\Addons\AbstractAddonCore;
use GambDuplicaWP\Models\Storages\AbstractStorageEntity;

/**
 * Storage ftp/sftp addon class
 */
class AmazonS3Addon extends AbstractAddonCore
{
    const ADDON_PATH = __DIR__;

    /**
     * @return void
     */
    public function init()
    {
        Autoloader::register();

        add_action('gamb-duplica-wp_pro_daily_actions', [self::class, 'purgeOldS3MultipartUploads']);
        add_action('gamb-duplica-wp_pro_register_storage_types', [$this, 'registerStorages']);
        add_filter('gamb-duplica-wp_template_file', [self::class, 'getTemplateFile'], 10, 2);
        add_filter('gamb-duplica-wp_usage_stats_storages_infos', [self::class, 'getStorageUsageStats'], 10, 1);
    }

    /**
     * Register storages
     *
     * @return void
     */
    public function registerStorages()
    {
        AmazonS3Storage::registerType();
        AmazonS3CompatibleStorage::registerType();
        GoogleCloudStorage::registerType();
        BackblazeStorage::registerType();
        DreamStorage::registerType();
        DigitalOceanStorage::registerType();
        VultrStorage::registerType();
        CloudflareStorage::registerType();
        WasabiStorage::registerType();
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
        if (strpos($slugTpl, 'amazons3addon/') === 0) {
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

        $storageNums['storages_s3_count']            = 0;
        $storageNums['storages_s3_compatible_count'] = 0;

        foreach ($storages as $index => $storage) {
            switch ($storage->getStype()) {
                case AmazonS3Storage::getSType():
                    $storageNums['storages_s3_count']++;
                    break;
                case AmazonS3CompatibleStorage::getSType():
                case GoogleCloudStorage::getSType():
                case BackblazeStorage::getSType():
                case DreamStorage::getSType():
                case DigitalOceanStorage::getSType():
                case VultrStorage::getSType():
                case CloudflareStorage::getSType():
                case WasabiStorage::getSType():
                    $storageNums['storages_s3_compatible_count']++;
                    break;
            }
        }

        return $storageNums;
    }


    /**
     * Purge old S3 multipart uploads
     *
     * @return void
     */
    public static function purgeOldS3MultipartUploads()
    {
        if (($storages = AbstractStorageEntity::getAll()) == false) {
            return;
        }

        foreach ($storages as $storage) {
            if (!$storage instanceof \GambDuplicaWP\Addons\AmazonS3Addon\Models\AmazonS3Storage) {
                continue;
            }

            $storage->purgeMultipartUpload();
        }
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
