<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Package\Create\DupArchive;

use GAMB_DUPLICA_WP_Constants;
use GAMB_DUPLICA_WP_Global_Entity;
use GAMB_DUPLICA_WP_Log;
use GAMB_DUPLICA_WP_NoDirListException;
use GAMB_DUPLICA_WP_NoFileListException;
use GAMB_DUPLICA_WP_NoScanFileException;
use GAMB_DUPLICA_WP_Package;
use GAMB_DUPLICA_WP_PackageStatus;
use GAMB_DUPLICA_WP_Server;
use GAMB_DUPLICA_WP_U;
use GambDuplicaWP\Libs\DupArchive\DupArchiveEngine;
use GambDuplicaWP\Libs\DupArchive\States\DupArchiveExpandState;
use GambDuplicaWP\Libs\Snap\Snap32BitSizeLimitException;
use GambDuplicaWP\Libs\Snap\SnapIO;
use GambDuplicaWP\Libs\Snap\SnapUtil;
use GambDuplicaWP\Models\SystemGlobalEntity;
use GambDuplicaWP\Package\Create\BuildProgress;
use Exception;

/**
 * Package DupArchive creator
 */
class PackageDupArchive
{
    // Using a worker time override since evidence shorter time works much
    const WORKER_TIME_IN_SEC = 10;

    /**
     *  Creates the zip file and adds the SQL file to the archive
     *
     * @param GAMB_DUPLICA_WP_Package $package       Package descriptor
     * @param BuildProgress   $buildProgress Build progress object
     *
     * @return boolean
     */
    public static function create(GAMB_DUPLICA_WP_Package $package, BuildProgress $buildProgress)
    {
        try {
            $archive = $package->Archive;
            if ($buildProgress->retries > GAMB_DUPLICA_WP_Constants::MAX_BUILD_RETRIES) {
                $error_msg = __('Backup build appears stuck so marking Backup as failed. Is the Max Worker Time set too high?.', 'gamb-duplica-wp');
                GAMB_DUPLICA_WP_Log::error(__('Build Failure', 'gamb-duplica-wp'), $error_msg, false);
                $buildProgress->failed = true;
                return true;
            } else {
                // If all goes well retries will be reset to 0 at the end of this function.
                $buildProgress->retries++;
                $package->update();
            }

            $global = GAMB_DUPLICA_WP_Global_Entity::getInstance();
            $done   = false;

            DupArchiveEngine::init(new Logger(), $archive->getTargetRootPath());
            $package->safe_tmp_cleanup(true);
            $compressDir = rtrim(SnapIO::safePath($archive->PackDir), '/');
            $archivePath = SnapIO::safePath("{$package->StorePath}/{$archive->File}");
            $filterDirs  = empty($archive->FilterDirs)  ? 'not set' : rtrim(str_replace(';', "\n\t", $archive->FilterDirs));
            $filterFiles = empty($archive->FilterFiles) ? 'not set' : rtrim(str_replace(';', "\n\t", $archive->FilterFiles));
            $filterExts  = empty($archive->FilterExts)  ? 'not set' : $archive->FilterExts;
            $filterOn    = ($archive->FilterOn) ? 'ON' : 'OFF';

            $scanFilepath            = GambDuplicaWP_PRO_SSDIR_PATH_TMP . "/{$package->getNameHash()}_scan.json";
            $skipArchiveFinalization = false;
            try {
                $scanReport = $package->getScanReportFromJson($scanFilepath, true);
            } catch (GAMB_DUPLICA_WP_NoScanFileException $ex) {
                GAMB_DUPLICA_WP_Log::trace("**** scan file $scanFilepath doesn't exist!!");
                GAMB_DUPLICA_WP_Log::error($ex->getMessage(), '', false);
                $buildProgress->failed = true;
                return true;
            } catch (GAMB_DUPLICA_WP_NoFileListException $ex) {
                GAMB_DUPLICA_WP_Log::trace("**** list of files doesn't exist!!");
                GAMB_DUPLICA_WP_Log::error($ex->getMessage(), '', false);
                $buildProgress->failed = true;
                return true;
            } catch (GAMB_DUPLICA_WP_NoDirListException $ex) {
                GAMB_DUPLICA_WP_Log::trace("**** list of directories doesn't exist!!");
                GAMB_DUPLICA_WP_Log::error($ex->getMessage(), '', false);
                $buildProgress->failed = true;
                return true;
            }

            if ($buildProgress->archive_started == false) {
                GAMB_DUPLICA_WP_Log::info("\n********************************************************************************");
                GAMB_DUPLICA_WP_Log::info("ARCHIVE Type=DUP Mode=DupArchive");
                GAMB_DUPLICA_WP_Log::info("********************************************************************************");
                GAMB_DUPLICA_WP_Log::info("ARCHIVE DIR:  " . $compressDir);
                GAMB_DUPLICA_WP_Log::info("ARCHIVE FILE: " . basename($archivePath));
                GAMB_DUPLICA_WP_Log::info("FILTERS: *{$filterOn}*");
                GAMB_DUPLICA_WP_Log::info("DIRS:  {$filterDirs}");
                GAMB_DUPLICA_WP_Log::info("EXTS:  {$filterExts}");
                GAMB_DUPLICA_WP_Log::info("FILES:  {$filterFiles}");
                GAMB_DUPLICA_WP_Log::info("----------------------------------------");
                GAMB_DUPLICA_WP_Log::info("COMPRESSING");
                GAMB_DUPLICA_WP_Log::info("SIZE:\t" . $scanReport->ARC->Size);
                GAMB_DUPLICA_WP_Log::info(
                    "STATS:\tDirs " . $scanReport->ARC->DirCount .
                    " | Files " . $scanReport->ARC->FileCount .
                    " | Total " . $scanReport->ARC->FullCount
                );
                if (($scanReport->ARC->DirCount == '') || ($scanReport->ARC->FileCount == '') || ($scanReport->ARC->FullCount == '')) {
                    GAMB_DUPLICA_WP_Log::error('Invalid Scan Report Detected', 'Invalid Scan Report Detected', false);
                    $buildProgress->failed = true;
                    return true;
                }

                $buildProgress->archive_started    = true;
                $buildProgress->archive_start_time = GAMB_DUPLICA_WP_U::getMicrotime();
                $buildProgress->retries            = 0;
                $package->Update();
            }

            try {
                if ($buildProgress->dupCreate == null) {
                    $archiveHeader = DupArchiveEngine::createArchive(
                        $archivePath,
                        $buildProgress->current_build_compression,
                        $package->Archive->getArchivePassword()
                    );

                    $createState = PackageDupArchiveCreateState::createNew(
                        $archiveHeader,
                        $package,
                        $archivePath,
                        $compressDir,
                        self::WORKER_TIME_IN_SEC
                    );
                } else {
                    /*GAMB_DUPLICA_WP_Log::traceObject('Resumed build_progress', $package->build_progress);*/
                    $createState = $package->build_progress->dupCreate;
                }

                if ($buildProgress->retries > 1) {
                    // Indicates it had problems before so move into robustness mode
                    $createState->isRobust = true;
                    //$createState->timeSliceInSecs = self::WORKER_TIME_IN_SEC / 2;
                    $createState->save();
                }

                if ($createState->working) {
                    DupArchiveEngine::addItemsToArchive($createState, $scanReport->ARC);
                    if ($createState->isCriticalFailurePresent()) {
                        throw new Exception($createState->getFailureSummary());
                    }

                    $totalFileCount = count($scanReport->ARC->Files);
                    GAMB_DUPLICA_WP_Log::trace("Total file count " . $totalFileCount);
                    $status = SnapUtil::getWorkPercent(
                        GAMB_DUPLICA_WP_PackageStatus::ARCSTART,
                        GAMB_DUPLICA_WP_PackageStatus::ARCVALIDATION,
                        $totalFileCount,
                        $createState->currentFileIndex
                    );

                    $buildProgress->retries = 0;
                    $createState->save();
                    $package->set_status($status);

                    GAMB_DUPLICA_WP_Log::traceObject('Stored build_progress', $package->build_progress);
                    if ($createState->working == false) {
                    // Want it to do the final cleanup work in an entirely new thread so return immediately
                        $skipArchiveFinalization = true;
                        GAMB_DUPLICA_WP_Log::trace("Done build phase.");
                    }
                }
            } catch (Snap32BitSizeLimitException $exception) {
                $global = SystemGlobalEntity::getInstance();
                $err    = 'Backup build failure due to building a large Backup on 32 bit PHP.';
                $fix    = sprintf(
                    _x(
                        'Backup build failure due to building a large Backup on 32 bit PHP. Please see %1$sTech docs%2$s for instructions on how to resolve.',
                        '1 and 2 are opening and closing anchor tags',
                        'gamb-duplica-wp'
                    ),
                    '<a href="' . esc_url(GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . 'how-to-resolve-file-io-related-build-issues') . '" target="_blank">',
                    '</a>'
                );
                $global->addTextFix($err, $fix);
                $buildProgress->failed = true;
                return true;
            } catch (Exception $ex) {
                $message = __('Problem adding items to archive.', 'gamb-duplica-wp') . ' ' . $ex->getMessage() . "\n" . $ex->getTraceAsString();
                GAMB_DUPLICA_WP_Log::error(__('Problems adding items to archive.', 'gamb-duplica-wp'), $message, false);
                GAMB_DUPLICA_WP_Log::traceObject($message . " EXCEPTION:", $ex);
                $buildProgress->failed = true;
                return true;
            }

            //-- Final Wrapup of the Archive
            if ((!$skipArchiveFinalization) && ($createState->working == false)) {
                if (!$buildProgress->installer_built) {
                    $package->Installer->build($buildProgress);

                    $expandState = new PackageDupArchiveExpandState(
                        DupArchiveEngine::getArchiveHeader($archivePath, $package->Archive->getArchivePassword()),
                        $package
                    );

                    $expandState->archivePath            = $archivePath;
                    $expandState->working                = true;
                    $expandState->timeSliceInSecs        = self::WORKER_TIME_IN_SEC;
                    $expandState->basePath               = GambDuplicaWP_PRO_SSDIR_PATH_TMP . '/validate';
                    $expandState->validateOnly           = true;
                    $expandState->validatiOnType         = DupArchiveExpandState::VALIDATION_STANDARD;
                    $expandState->working                = true;
                    $expandState->expectedDirectoryCount = max(0, (
                        count($scanReport->ARC->Dirs) -
                        $createState->skippedDirectoryCount +
                        $package->Installer->numDirsAdded
                    ));
                    // add index file
                    $expandState->expectedFileCount = max(0, (
                        1 +
                        count($scanReport->ARC->Files) -
                        $createState->skippedFileCount +
                        $package->Installer->numFilesAdded
                    ));
                    $expandState->save();
                } else {
                    // $build_progress->warnings = $createState->getWarnings(); Auto saves warnings within build progress along the way
                    try {
                        $expandState = $buildProgress->dupExpand;
                        if (is_null($expandState)) {
                            throw new Exception('Expand state can\'t be null');
                        }
                        if ($buildProgress->retries > 1) {
                            // Indicates it had problems before so move into robustness mode
                            $expandState->isRobust = true;
                            //$expandState->timeSliceInSecs = self::WORKER_TIME_IN_SEC / 2;
                            $expandState->save();
                        }

                        GAMB_DUPLICA_WP_Log::traceObject('Resumed validation expand state', $expandState);
                        DupArchiveEngine::expandArchive($expandState);
                        $totalFileCount = count($scanReport->ARC->Files);
                        $archiveSize    = (int) filesize($expandState->archivePath);
                        $status         = SnapUtil::getWorkPercent(
                            GAMB_DUPLICA_WP_PackageStatus::ARCVALIDATION,
                            GAMB_DUPLICA_WP_PackageStatus::ARCDONE,
                            $archiveSize,
                            $expandState->archiveOffset
                        );

                        $package->set_status($status);
                    } catch (Exception $ex) {
                        GAMB_DUPLICA_WP_Log::traceError('Exception:' . $ex->getMessage() . ':' . $ex->getTraceAsString());
                        $buildProgress->failed = true;
                        return true;
                    }

                    if ($expandState->isCriticalFailurePresent()) {
                        // Fail immediately if critical failure present - even if havent completed processing the entire archive.
                        GAMB_DUPLICA_WP_Log::error(__('Build Failure', 'gamb-duplica-wp'), $expandState->getFailureSummary(), false);
                        $buildProgress->failed = true;
                        return true;
                    } elseif (!$expandState->working) {
                        $buildProgress->archive_built = true;
                        $buildProgress->retries       = 0;
                        $package->update();
                        $timerAllEnd     = GAMB_DUPLICA_WP_U::getMicrotime();
                        $timerAllSum     = GAMB_DUPLICA_WP_U::elapsedTime($timerAllEnd, $buildProgress->archive_start_time);
                        $archiveFileSize = (int) filesize($archivePath);
                        GAMB_DUPLICA_WP_Log::info("COMPRESSED SIZE: " . GAMB_DUPLICA_WP_U::byteSize($archiveFileSize));
                        GAMB_DUPLICA_WP_Log::info("ARCHIVE RUNTIME: {$timerAllSum}");
                        GAMB_DUPLICA_WP_Log::info("MEMORY STACK: " . GAMB_DUPLICA_WP_Server::getPHPMemory());
                        GAMB_DUPLICA_WP_Log::info("CREATE WARNINGS: " . $createState->getFailureSummary(false, true));
                        GAMB_DUPLICA_WP_Log::info("VALIDATION WARNINGS: " . $expandState->getFailureSummary(false, true));
                        $archive->file_count = max(0, (
                            $expandState->fileWriteCount +
                            $expandState->directoryWriteCount -
                            $package->Installer->numDirsAdded -
                            $package->Installer->numFilesAdded
                        ));
                        $package->update();
                        $done = true;
                        if ($status == GAMB_DUPLICA_WP_PackageStatus::ARCDONE) {
                                do_action('gamb-duplica-wp_pro_package_after_set_status', $package, GAMB_DUPLICA_WP_PackageStatus::ARCDONE);
                        }
                    } else {
                        $expandState->save();
                    }
                }
            }
        } catch (Exception $ex) {
            // Have to have a catchall since the main system that calls this function is not prepared to handle exceptions
            GAMB_DUPLICA_WP_Log::traceError('Top level create Exception:' . $ex->getMessage() . ':' . $ex->getTraceAsString());
            $buildProgress->failed = true;
            return true;
        }

        $buildProgress->retries = 0;
        return $done;
    }
}
