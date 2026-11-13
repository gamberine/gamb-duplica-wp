<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Package;

use GAMB_DUPLICA_WP_Package;
use GambDuplicaWP\Installer\Models\MigrateData;
use GambDuplicaWP\Package\Recovery\RecoveryPackage;

class PackageUtils
{
    /**
     * Update CREATED AFTER INSTALL FLAGS
     *
     * @param MigrateData $migrationData migration data
     *
     * @return void
     */
    public static function updateCreatedAfterInstallFlags(MigrateData $migrationData)
    {
        if ($migrationData->restoreBackupMode == false) {
            return;
        }

        // Refresh recovery Backup set beforw backup
        $ids = GAMB_DUPLICA_WP_Package::dbSelect('FIND_IN_SET(\'' . GAMB_DUPLICA_WP_Package::FLAG_DISASTER_SET . '\', `flags`)', 0, 0, '', 'ids');
        if (count($ids)) {
            RecoveryPackage::setRecoveablePackage($ids[0]);
        }

        // Update all backups with created after restore flag or created after install time
        GAMB_DUPLICA_WP_Package::dbSelectCallback(
            function (GAMB_DUPLICA_WP_Package $package): void {
                $package->updateMigrateAfterInstallFlag();
                $package->save();
            },
            'FIND_IN_SET(\'' . GAMB_DUPLICA_WP_Package::FLAG_CREATED_AFTER_RESTORE . '\', `flags`) OR 
            (
                `id` > ' .  $migrationData->packageId . ' AND
                `created` < \'' . esc_sql($migrationData->installTime) . '\'
            )'
        );
    }
}
