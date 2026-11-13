<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Addons\WpCliAddon;

use GAMB_DUPLICA_WP_Log;
use GAMB_DUPLICA_WP_Package;
use GAMB_DUPLICA_WP_PackageStatus;
use gamb-duplica-wp\Ajax\ServicesPackage;
use gamb-duplica-wp\Libs\Snap\SnapUtil;
use gamb-duplica-wp\Models\Storages\StoragesUtil;
use Exception;
use WP_CLI;

class BackupBuild
{
    /**
     * Process schedules by cron
     *
     * @param GAMB_DUPLICA_WP_Package $package Package to process
     *
     * @return void
     */
    public static function process(GAMB_DUPLICA_WP_Package $package)
    {
        StoragesUtil::getDefaultStorage()->initStorageDirectory(true);

        if (SnapUtil::isIniValChangeable('memory_limit')) {
            @ini_set('memory_limit', -1);
        }

        $start_time = time();
        GAMB_DUPLICA_WP_Log::trace("PACKAGE $package->ID:PROCESSING");

        if ($package->Status < GAMB_DUPLICA_WP_PackageStatus::AFTER_SCAN) {
            // Scan step built into package build - used by schedules - NOT manual build where scan is done in web service.
            GAMB_DUPLICA_WP_Log::trace("PACKAGE $package->ID:SCANNING");
            //After scanner runs.  Save FilterInfo (unreadable, warnings, globals etc)
            if (!$package->Archive->scanFiles(true)) {
                while ($package->Archive->scanFiles() !== true) {
                    GAMB_DUPLICA_WP_Log::trace("CONTINUE SCANNING");
                }
            }
            $scan_report = $package->createScanReport();
            $package->set_status(GAMB_DUPLICA_WP_PackageStatus::AFTER_SCAN);

            $end_time  = time();
            $scan_time = $end_time - $start_time;
            GAMB_DUPLICA_WP_Log::trace("SCAN TIME=$scan_time seconds");
            WP_CLI::debug("Scan result\n" . json_encode($scan_report, JSON_PRETTY_PRINT));
            if ($scan_report['Status'] > ServicesPackage::EXEC_STATUS_PASS) {
                if (empty($scan_report['Message'])) {
                    $scan_report['Message'] = 'Scan failed';
                }
                throw new Exception("Scan failed, Status: {$scan_report['Status']}, Message: {$scan_report['Message']}");
            } else {
                WP_CLI::success("Scan success");
            }
        } elseif ($package->Status < GAMB_DUPLICA_WP_PackageStatus::COPIEDPACKAGE) {
            GAMB_DUPLICA_WP_Log::trace("PACKAGE $package->ID:BUILDING");
            $package->run_build(false);
            $end_time   = time();
            $build_time = $end_time - $start_time;
            GAMB_DUPLICA_WP_Log::trace("BUILD TIME=$build_time seconds");
            if ($package->build_progress->hasCompleted()) {
                if ($package->build_progress->failed) {
                    throw new Exception("Build failed");
                }
            }
        } elseif ($package->Status < GAMB_DUPLICA_WP_PackageStatus::COMPLETE) {
            GAMB_DUPLICA_WP_Log::trace("PACKAGE $package->ID:STORAGE PROCESSING");
            $package->set_status(GAMB_DUPLICA_WP_PackageStatus::STORAGE_PROCESSING);
            $package->process_storages();
            $end_time   = time();
            $build_time = $end_time - $start_time;
            GAMB_DUPLICA_WP_Log::trace("STORAGE CHUNK PROCESSING TIME=$build_time seconds");
            if ($package->Status == GAMB_DUPLICA_WP_PackageStatus::COMPLETE) {
                GAMB_DUPLICA_WP_Log::trace("PACKAGE $package->ID COMPLETE");
            } elseif ($package->Status == GAMB_DUPLICA_WP_PackageStatus::ERROR) {
                GAMB_DUPLICA_WP_Log::trace("PACKAGE $package->ID IN ERROR STATE");
            }

            $packageCompleteStatuses = [
                GAMB_DUPLICA_WP_PackageStatus::COMPLETE,
                GAMB_DUPLICA_WP_PackageStatus::ERROR,
            ];
            if (in_array($package->Status, $packageCompleteStatuses)) {
                $info  = "\n";
                $info .= "********************************************************************************\n";
                $info .= "********************************************************************************\n";
                $info .= "gamb-duplica-wp PRO PACKAGE CREATION OR MANUAL STORAGE TRANSFER END: " . @date("Y-m-d H:i:s") . "\n";
                $info .= "NOTICE: Do NOT post to public sites or forums \n";
                $info .= "********************************************************************************\n";
                $info .= "********************************************************************************\n";
                GAMB_DUPLICA_WP_Log::infoTrace($info);
            }

            if ($package->Status == GAMB_DUPLICA_WP_PackageStatus::ERROR) {
                throw new Exception("Storage failed");
            }
        }
    }
}
