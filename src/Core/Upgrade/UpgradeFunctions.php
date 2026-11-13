<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Core\Upgrade;

use GAMB_DUPLICA_WP_Global_Entity;
use GAMB_DUPLICA_WP_Log;
use GAMB_DUPLICA_WP_Package;
use GAMB_DUPLICA_WP_Package_Template_Entity;
use GAMB_DUPLICA_WP_PackageStatus;
use GAMB_DUPLICA_WP_Schedule_Entity;
use GAMB_DUPLICA_WP_Secure_Global_Entity;
use GambDuplicaWP\Addons\ProBase\License\License;
use GambDuplicaWP\Libs\Snap\SnapUtil;
use GambDuplicaWP\Models\DynamicGlobalEntity;
use GambDuplicaWP\Utils\Crypt\CryptBlowfish;
use GambDuplicaWP\Core\Models\AbstractEntity;
use GambDuplicaWP\Models\Storages\AbstractStorageEntity;
use GambDuplicaWP\Models\Storages\Local\DefaultLocalStorage;
use GambDuplicaWP\Models\Storages\StoragesUtil;
use GambDuplicaWP\Package\Create\BuildComponents;
use GambDuplicaWP\Utils\Email\EmailSummary;
use Error;
use Exception;

/**
 * Utility class managing when the plugin is updated
 *
 * GAMB_DUPLICA_WP_Upgrade_U
 */
class UpgradeFunctions
{
    const LAST_VERSION_PACKAGE_DBONLY_FLAG    = '4.5.11.2';
    const LAST_VERSION_OLD_STORAGE_MONOLITHIC = '4.5.12.1';
    const LAST_VERSION_NO_EMAIL_SUMMARY       = '4.5.12.1';
    const FIRST_VERSION_WITH_NEW_TABLES       = '4.5.14-beta2';
    const FIRST_VERSION_DEFAULT_PURGE         = '4.5.20-beta1';
    const FIRST_VERSION_WITH_MULTISITE_DRM    = '4.5.21-beta1';

    const LEGACY_DB_PACKAGES_TABLE_NAME = 'gamb-duplica-wp_pro_packages';
    const LEGACY_DB_ENTITIES_TABLE_NAME = 'gamb-duplica-wp_pro_entities';

    /**
     * This function is executed when the plugin is activated and
     * every time the version saved in the wp_options is different from the plugin version both in upgrade and downgrade.
     *
     * @param false|string $currentVersion current gamb-duplica-wp version, false if is first installation
     * @param string       $newVersion     new gamb-duplica-wp Version
     *
     * @return void
     */
    public static function performUpgrade($currentVersion, $newVersion)
    {
        // before everything, init database
        self::updateDatabase($currentVersion);
        // Setup All Directories
        self::storeDupSecureKey($currentVersion);
        self::updateStorages($currentVersion);
        self::updateTemplates($currentVersion);
        self::updatePackageComponents($currentVersion);
        self::initEmailSummaryRecipients($currentVersion);
        self::moveDataToSecureGlobal();
        self::moveStorageSettingsToDynamicEntity();
        self::fixDoubleDefaultStorages();
        //important to run after updateStorages
        self::updateBackupRecordPurgeSettings($currentVersion);

        do_action('gamb-duplica-wp_pro_on_upgrade_version', $currentVersion, $newVersion);

        // Schedule custom cron event for cleanup of installer files if it should be scheduled
        GAMB_DUPLICA_WP_Global_Entity::cleanupScheduleSetup();
    }

    /**
     * Update database.
     *
     * @param false|string $currentVersion current gamb-duplica-wp version, false if is first installation
     *
     * @return void
     */
    public static function updateDatabase($currentVersion)
    {
        self::renameLegacyTablesName($currentVersion);

        AbstractEntity::initTable();
        GAMB_DUPLICA_WP_Package::initTable();
        GAMB_DUPLICA_WP_Global_Entity::getInstance()->save();
        GAMB_DUPLICA_WP_Secure_Global_Entity::getInstance()->save();
        StoragesUtil::initDefaultStorage();
        GAMB_DUPLICA_WP_Package_Template_Entity::create_default();
        GAMB_DUPLICA_WP_Package_Template_Entity::create_manual();
    }

    /**
     * Upate endpoints
     *
     * @param false|string $currentVersion current gamb-duplica-wp version
     *
     * @return void
     */
    protected static function updateStorages($currentVersion)
    {
        if ($currentVersion == false || version_compare($currentVersion, self::LAST_VERSION_OLD_STORAGE_MONOLITHIC, '>')) {
            return;
        }

        if (($storages = AbstractStorageEntity::getAll()) == false) {
            // Don't generare error, just return
            return;
        }

        foreach ($storages as $storage) {
            // Update storage endpoint
            $storage->save();
        }

        // Update default storage
        $defaultStorage = StoragesUtil::getDefaultStorage();
        $defaultStorage->updateFromGlobal($currentVersion);
        $defaultStorage->save();

        $oldDefaultId = DefaultLocalStorage::OLD_VIRTUAL_STORAGE_ID;
        $newDefaultId = StoragesUtil::getDefaultStorageId();

        $global     = GAMB_DUPLICA_WP_Global_Entity::getInstance();
        $storageIds = $global->getManualModeStorageIds();

        if (in_array($oldDefaultId, $storageIds)) {
            $storageIds   = array_diff($storageIds, [$oldDefaultId]);
            $storageIds[] = $newDefaultId;
            $storageIds   = array_values($storageIds);
            $global->setManualModeStorageIds($storageIds);
            $global->save();
        }

        GAMB_DUPLICA_WP_Schedule_Entity::listCallback(function (GAMB_DUPLICA_WP_Schedule_Entity $schedule) use ($oldDefaultId, $newDefaultId): void {
            if (!in_array($oldDefaultId, $schedule->storage_ids)) {
                return;
            }
            $schedule->storage_ids   = array_diff($schedule->storage_ids, [$oldDefaultId]);
            $schedule->storage_ids[] = $newDefaultId;
            $schedule->storage_ids   = array_values($schedule->storage_ids);
            $schedule->save();
        });

        GAMB_DUPLICA_WP_Package::by_status_callback(
            function (GAMB_DUPLICA_WP_Package $package) use ($oldDefaultId, $newDefaultId): void {
                $save = false;

                if ($package->active_storage_id == $oldDefaultId) {
                    $package->active_storage_id = $newDefaultId;
                    $save                       = true;
                }

                foreach ($package->upload_infos as $key => $info) {
                    if ($info->getStorageId() != $oldDefaultId) {
                        continue;
                    }
                    $info->setStorageId($newDefaultId);
                    $save = true;
                }
                if ($save) {
                    $package->save();
                }
            },
            [
                [
                    'op'     => '>=',
                    'status' => GAMB_DUPLICA_WP_PackageStatus::COMPLETE,
                ],
            ]
        );
    }

    /**
     * Set default recipients for email summary
     *
     * @param false|string $currentVersion current version of plugin
     *
     * @return void
     */
    protected static function initEmailSummaryRecipients($currentVersion)
    {
        if ($currentVersion == false || version_compare($currentVersion, self::LAST_VERSION_NO_EMAIL_SUMMARY, '>')) {
            return;
        }

        $global = GAMB_DUPLICA_WP_Global_Entity::getInstance();
        $global->setEmailSummaryRecipients(EmailSummary::getDefaultRecipients());
        $global->save();
    }

    /**
     * Implement defaults for Backup components
     *
     * @param string $currentVersion current version of plugin
     *
     * @return void
     */
    protected static function updatePackageComponents($currentVersion)
    {
        if ($currentVersion == false || version_compare($currentVersion, self::LAST_VERSION_PACKAGE_DBONLY_FLAG, '>')) {
            return;
        }

        GAMB_DUPLICA_WP_Package_Template_Entity::listCallback(function (GAMB_DUPLICA_WP_Package_Template_Entity $template): void {
            if (count($template->components) > 0) {
                return;
            }
            if ($template->archive_export_onlydb) {
                $template->components = [BuildComponents::COMP_DB];
            } else {
                $template->components = BuildComponents::COMPONENTS_DEFAULT;
            }
            $template->save();
        });

        GAMB_DUPLICA_WP_Package::by_status_callback(function (GAMB_DUPLICA_WP_Package $package): void {
            if (count($package->components) > 0) {
                return;
            }
            if ($package->Archive->ExportOnlyDB) {
                $package->components = [BuildComponents::COMP_DB];
            } else {
                $package->components = BuildComponents::COMPONENTS_DEFAULT;
            }
            $package->save();
        });
    }

    /**
     * Upate templates
     *
     * @param false|string $currentVersion current gamb-duplica-wp version
     *
     * @return void
     */
    protected static function updateTemplates($currentVersion)
    {
        // Update templates one when coming from 4.5.2 or below
        if ($currentVersion == false || version_compare($currentVersion, '4.5.3', '>=')) {
            return;
        }

        $templates = GAMB_DUPLICA_WP_Package_Template_Entity::getAll();
        if (!is_array($templates)) {
            return;
        }

        foreach ($templates as $template) {
            $template->save();
        }
    }

    /**
     * Save DUP SECURE KEY
     *
     * @param false|string $currentVersion current gamb-duplica-wp version
     *
     * @return void
     */
    protected static function storeDupSecureKey($currentVersion)
    {
        if ($currentVersion !== false && SnapUtil::versionCompare($currentVersion, '4.5.0', '<=', 3)) {
            CryptBlowfish::createWpConfigSecureKey(true, true);
        } else {
            CryptBlowfish::createWpConfigSecureKey(false, false);
        }
    }

    /**
     * Rename legacy tables
     *
     * @param false|string $currentVersion current gamb-duplica-wp version
     *
     * @return void
     */
    public static function renameLegacyTablesName($currentVersion)
    {
        if (
            version_compare($currentVersion, self::FIRST_VERSION_WITH_NEW_TABLES, '>=')
        ) {
            return;
        }

        /** @var \wpdb $wpdb */
        global $wpdb;

        // RENAME OLD TABLES BEFORE 4.5.14
        $mapping = [
            $wpdb->base_prefix . self::LEGACY_DB_PACKAGES_TABLE_NAME => GAMB_DUPLICA_WP_Package::getTableName(),
            $wpdb->base_prefix . self::LEGACY_DB_ENTITIES_TABLE_NAME => AbstractEntity::getTableName(),
        ];

        foreach ($mapping as $oldTable => $newTable) {
            $oldTableQuery = $wpdb->prepare("SHOW TABLES LIKE %s", $oldTable);
            $newTableQuery = $wpdb->prepare("SHOW TABLES LIKE %s", $newTable);
            if (
                $wpdb->get_var($oldTableQuery) === $oldTable &&
                $wpdb->get_var($newTableQuery) !== $newTable
            ) {
                $wpdb->query("RENAME TABLE `" . esc_sql($oldTable) . "` TO `" . esc_sql($newTable) . "`");
            }
        }
    }

    /**
     * Move data tu secure global
     *
     * @return void
     */
    protected static function moveDataToSecureGlobal()
    {
        $global = GAMB_DUPLICA_WP_Global_Entity::getInstance();
        if (($global->lkp !== '') || ($global->basic_auth_password !== '')) {
            SnapUtil::errorLog('setting sglobal');
            $sglobal                      = GAMB_DUPLICA_WP_Secure_Global_Entity::getInstance();
            $sglobal->lkp                 = $global->lkp;
            $sglobal->basic_auth_password = $global->basic_auth_password;
            $global->lkp                  = '';
            $global->basic_auth_password  = '';
            $sglobal->save();
            $global->save();
        }
    }

    /**
     * Move storage settings to dynamic entity from global
     *
     * @return void
     */
    protected static function moveStorageSettingsToDynamicEntity()
    {
        $global  = GAMB_DUPLICA_WP_Global_Entity::getInstance();
        $keys    = [
            'dropbox_upload_chunksize_in_kb',
            'dropbox_transfer_mode',
            'gdrive_upload_chunksize_in_kb',
            'gdrive_transfer_mode',
            's3_upload_part_size_in_kb',
            'onedrive_upload_chunksize_in_kb',
            'local_upload_chunksize_in_MB',
        ];
        $migrate = [];
        foreach ($keys as $key) {
            if ($global->{$key} === null) {
                continue;
            }
            $migrate[$key]  = $global->{$key};
            $global->{$key} = null;
        }
        if (count($migrate) === 0) {
            return;
        }
        $dGlobal = DynamicGlobalEntity::getInstance();
        foreach ($migrate as $key => $value) {
            $dGlobal->setVal($key, $value);
        }
        $dGlobal->save();
        $global->save();
    }

    /**
     * Removed double default storages
     *
     * @return void
     */
    protected static function fixDoubleDefaultStorages()
    {
        try {
            $defaultStorageId = StoragesUtil::getDefaultStorageId();
            $doubleStorageIds = StoragesUtil::removeDoubleDefaultStorages();

            if ($doubleStorageIds === []) {
                return;
            }

            // Auto assign references to the correct default storage
            GAMB_DUPLICA_WP_Schedule_Entity::listCallback(
                function (GAMB_DUPLICA_WP_Schedule_Entity $schedule) use ($defaultStorageId, $doubleStorageIds): void {
                    $save = false;
                    foreach ($schedule->storage_ids as $key => $storageId) {
                        if (!in_array($storageId, $doubleStorageIds)) {
                            continue;
                        }
                        $schedule->storage_ids[$key] = $defaultStorageId;
                        $save                        = true;
                    }
                    $schedule->storage_ids = array_values(array_unique($schedule->storage_ids));
                    if ($save) {
                        $schedule->save();
                    }
                }
            );

            GAMB_DUPLICA_WP_Package::by_status_callback(
                function (GAMB_DUPLICA_WP_Package $package) use ($defaultStorageId, $doubleStorageIds): void {
                    $save = false;
                    if (in_array($package->active_storage_id, $doubleStorageIds)) {
                        $package->active_storage_id = $defaultStorageId;
                        $save                       = true;
                    }
                    foreach ($package->upload_infos as $key => $info) {
                        if (!in_array($info->getStorageId(), $doubleStorageIds)) {
                            continue;
                        }
                        $info->setStorageId($defaultStorageId);
                        $save = true;
                    }
                    if ($save) {
                        $package->save();
                    }
                },
                [
                    [
                        'op'     => '>=',
                        'status' => GAMB_DUPLICA_WP_PackageStatus::COMPLETE,
                    ],
                ]
            );
        } catch (Exception | Error $e) {
            GAMB_DUPLICA_WP_Log::trace("Error fixing remove double storage: " . $e->getMessage());
            return;
        }
    }

    /**
     * Sets the correct backup purge setting based on previous default local storage settings
     *
     * @param false|string $currentVersion current gamb-duplica-wp version
     *
     * @return void
     */
    public static function updateBackupRecordPurgeSettings($currentVersion)
    {
        if ($currentVersion == false || version_compare($currentVersion, self::FIRST_VERSION_DEFAULT_PURGE, '>=')) {
            return;
        }

        $global = GAMB_DUPLICA_WP_Global_Entity::getInstance();
        if (StoragesUtil::getDefaultStorage()->isPurgeEnabled()) {
            $global->setPurgeBackupRecords(AbstractStorageEntity::BACKUP_RECORDS_REMOVE_DEFAULT);
        } else {
            $global->setPurgeBackupRecords(AbstractStorageEntity::BACKUP_RECORDS_REMOVE_NEVER);
        }

        $global->save();
    }
}
