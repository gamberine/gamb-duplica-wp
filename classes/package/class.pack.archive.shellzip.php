<?php

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use GambDuplicaWP\Libs\Snap\SnapIO;
use GambDuplicaWP\Libs\Shell\Shell;
use GambDuplicaWP\Models\Storages\AbstractStorageEntity;
use GambDuplicaWP\Models\Storages\Local\LocalStorage;
use GambDuplicaWP\Models\SystemGlobalEntity;
use GambDuplicaWP\Package\Create\BuildProgress;

/**
 *  Creates a zip file using Shell_Exec and the system zip command
 *  Not available on all system
 **/
class GAMB_DUPLICA_WP_ShellZip
{
    /**
     * Creates the zip file and adds the SQL file to the archive
     *
     * @param GAMB_DUPLICA_WP_Package $package        The Backup object
     * @param BuildProgress   $build_progress The build progress object
     *
     * @return boolean
     */
    public static function create(GAMB_DUPLICA_WP_Package $package, BuildProgress $build_progress): bool
    {
        $archive = $package->Archive;
        try {
            if ($package->Status == GAMB_DUPLICA_WP_PackageStatus::ARCSTART) {
                $error_text    = __('Zip process getting killed due to limited server resources.', 'gamb-duplica-wp');
                $fix_text      = __('Click to switch Archive Engine to DupArchive.', 'gamb-duplica-wp');
                $system_global = SystemGlobalEntity::getInstance();
                $system_global->addQuickFix(
                    $error_text,
                    $fix_text,
                    [
                        'global' => ['archive_build_mode' => 3],
                    ]
                );
                GAMB_DUPLICA_WP_Log::traceError("$error_text  **RECOMMENDATION: $fix_text");
                if ($build_progress->retries > 1) {
                    $build_progress->failed = true;
                    return true;
                } else {
                    $build_progress->retries++;
                    $package->update();
                }
            }

            $package->set_status(GAMB_DUPLICA_WP_PackageStatus::ARCSTART);
            $package->safe_tmp_cleanup(true);
            $compressDir  = rtrim(SnapIO::safePath($archive->PackDir), '/');
            $zipPath      = SnapIO::safePath("{$package->StorePath}/{$archive->File}");
            $sql_filepath = SnapIO::safePath("{$package->StorePath}/{$package->Database->File}");
            $filterDirs   = empty($archive->FilterDirs) ? 'not set' : rtrim(str_replace(';', "\n\t", $archive->FilterDirs));
            $filterFiles  = empty($archive->FilterFiles) ? 'not set' : rtrim(str_replace(';', "\n\t", $archive->FilterFiles));
            $filterExts   = empty($archive->FilterExts) ? 'not set' : $archive->FilterExts;
            $filterOn     = ($archive->FilterOn) ? 'ON' : 'OFF';
            $scanFilepath = GambDuplicaWP_PRO_SSDIR_PATH_TMP . "/{$package->getNameHash()}_scan.json";
            // LOAD SCAN REPORT
            try {
                $scanReport = $package->getScanReportFromJson($scanFilepath);
            } catch (GAMB_DUPLICA_WP_NoScanFileException $ex) {
                GAMB_DUPLICA_WP_Log::trace("**** scan file $scanFilepath doesn't exist!!");
                GAMB_DUPLICA_WP_Log::error($ex->getMessage(), '', false);
                $build_progress->failed = true;
                return true;
            } catch (GAMB_DUPLICA_WP_NoFileListException $ex) {
                GAMB_DUPLICA_WP_Log::trace("**** list of files doesn't exist!!");
                GAMB_DUPLICA_WP_Log::error($ex->getMessage(), '', false);
                $build_progress->failed = true;
                return true;
            } catch (GAMB_DUPLICA_WP_NoDirListException $ex) {
                GAMB_DUPLICA_WP_Log::trace("**** list of directories doesn't exist!!");
                GAMB_DUPLICA_WP_Log::error($ex->getMessage(), '', false);
                $build_progress->failed = true;
                return true;
            }

            GAMB_DUPLICA_WP_Log::info("\n********************************************************************************");
            GAMB_DUPLICA_WP_Log::info("ARCHIVE  Type=ZIP Mode=Shell");
            GAMB_DUPLICA_WP_Log::info("********************************************************************************");
            GAMB_DUPLICA_WP_Log::info("ARCHIVE DIR:  " . $compressDir);
            GAMB_DUPLICA_WP_Log::info("ARCHIVE FILE: " . basename($zipPath));
            GAMB_DUPLICA_WP_Log::info("FILTERS: *{$filterOn}*");
            GAMB_DUPLICA_WP_Log::info("DIRS:  {$filterDirs}");
            GAMB_DUPLICA_WP_Log::info("EXTS:  {$filterExts}");
            GAMB_DUPLICA_WP_Log::info("FILES:  {$filterFiles}");
            GAMB_DUPLICA_WP_Log::info("----------------------------------------");
            GAMB_DUPLICA_WP_Log::info("COMPRESSING");
            GAMB_DUPLICA_WP_Log::info("SIZE:\t" . $scanReport->ARC->Size);
            GAMB_DUPLICA_WP_Log::info("STATS:\tDirs " . $scanReport->ARC->DirCount . " | Files " . $scanReport->ARC->FileCount . " | Total " . $scanReport->ARC->FullCount);
            $build_progress->archive_started    = true;
            $build_progress->archive_start_time = GAMB_DUPLICA_WP_U::getMicrotime();
            $contains_root                      = false;
            $exclude_string                     = '';
            $filterDirs                         = $archive->FilterDirsAll;
            $filterExts                         = $archive->FilterExtsAll;
            $filterFiles                        = $archive->FilterFilesAll;
            // DIRS LIST
            foreach ($filterDirs as $filterDir) {
                if (trim($filterDir) != '') {
                    $relative_filter_dir = GAMB_DUPLICA_WP_U::getRelativePath($compressDir, $filterDir);
                    GAMB_DUPLICA_WP_Log::trace("Adding relative filter dir $relative_filter_dir for $filterDir relative to $compressDir");
                    if (trim($relative_filter_dir) == '') {
                        $contains_root = true;
                        break;
                    } else {
                        $exclude_string .= GAMB_DUPLICA_WP_Zip_U::customShellArgEscapeSequence($relative_filter_dir) . "**\* ";
                        $exclude_string .= GAMB_DUPLICA_WP_Zip_U::customShellArgEscapeSequence($relative_filter_dir) . " ";
                    }
                }
            }

            //EXT LIST
            foreach ($filterExts as $filterExt) {
                $exclude_string .= "\*.$filterExt ";
            }

            //FILE LIST
            foreach ($filterFiles as $filterFile) {
                if (trim($filterFile) != '') {
                    $relative_filter_file = GAMB_DUPLICA_WP_U::getRelativePath($compressDir, trim($filterFile));
                    GAMB_DUPLICA_WP_Log::trace("Full file=$filterFile relative=$relative_filter_file compressDir=$compressDir");
                    $exclude_string .= "\"$relative_filter_file\" ";
                }
            }

            //DB ONLY
            if ($package->isDBOnly()) {
                $contains_root = true;
            }


            if ($contains_root == false) {
                // Only attempt to zip things up if root isn't in there since stderr indicates when it cant do anything
                $storages = AbstractStorageEntity::getAll();
                foreach ($storages as $storage) {
                    if ($storage->getSType() !== LocalStorage::getSType()) {
                        continue;
                    }
                    /** @var LocalStorage $storage */
                    if ($storage->isFilterProtection()) {
                        continue;
                    }
                    $storagePath     = SnapIO::safePath($storage->getLocationString());
                    $storagePath     = GAMB_DUPLICA_WP_U::getRelativePath($compressDir, $storagePath);
                    $exclude_string .= "$storagePath**\* ";
                }

                $relative_backup_dir = GAMB_DUPLICA_WP_U::getRelativePath($compressDir, GambDuplicaWP_PRO_SSDIR_PATH);
                $exclude_string     .= "$relative_backup_dir**\* ";
                $params              = Shell::getCompressionParam($build_progress->current_build_compression);
                if (strlen($package->Archive->getArchivePassword()) > 0) {
                    $params .= ' --password ' . escapeshellarg($package->Archive->getArchivePassword());
                }
                $params .= ' -rq';

                $command  = 'cd ' . escapeshellarg($compressDir);
                $command .= ' && ' . escapeshellcmd(GAMB_DUPLICA_WP_Zip_U::getShellExecZipPath()) . ' ' . $params . ' ';
                $command .= escapeshellarg($zipPath) . ' ./';
                $command .= " -x $exclude_string 2>&1";
                GAMB_DUPLICA_WP_Log::infoTrace("SHELL COMMAND: $command");
                $shellOutput = Shell::runCommand($command, Shell::AVAILABLE_COMMANDS);
                GAMB_DUPLICA_WP_Log::trace("After shellzip command");
                if ($shellOutput !== false && !$shellOutput->isEmpty()) {
                    $stderr        = $shellOutput->getOutputAsString();
                    $error_text    = "Error executing shell exec zip: $stderr.";
                    $system_global = SystemGlobalEntity::getInstance();
                    if (GAMB_DUPLICA_WP_STR::contains($stderr, 'quota')) {
                        $fix_text = __("Account out of space so purge large files or talk to your host about increasing quota.", 'gamb-duplica-wp');
                        $system_global->addTextFix($error_text, $fix_text);
                    } elseif (GAMB_DUPLICA_WP_STR::contains($stderr, 'such file or')) {
                        $fix_text = sprintf(
                            "%s <a href='" . GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . "how-to-resolve-zip-format-related-build-issues' target='_blank'>%s</a>",
                            __('See FAQ:', 'gamb-duplica-wp'),
                            __('How to resolve "zip warning: No such file or directory"?', 'gamb-duplica-wp')
                        );
                        $system_global->addTextFix($error_text, $fix_text);
                    } else {
                        $fix_text = __("Click on button to switch to the DupArchive engine.", 'gamb-duplica-wp');
                        $system_global->addQuickFix(
                            $error_text,
                            $fix_text,
                            [
                                'global' => ['archive_build_mode' => 3],
                            ]
                        );
                    }
                    GAMB_DUPLICA_WP_Log::error("$error_text  **RECOMMENDATION: $fix_text", '', false);
                    $build_progress->failed = true;
                    return true;
                } else {
                    GAMB_DUPLICA_WP_Log::trace("Stderr is null");
                }

                $file_count_string = '';
                if (!file_exists($zipPath)) {
                    $file_count_string = sprintf(__('Zip file %s does not exist.', 'gamb-duplica-wp'), $zipPath);
                } elseif (GAMB_DUPLICA_WP_U::getExeFilepath('zipinfo') != null) {
                    GAMB_DUPLICA_WP_Log::trace("zipinfo exists");
                    $file_count_string = "zipinfo -t '$zipPath'";
                } elseif (GAMB_DUPLICA_WP_U::getExeFilepath('unzip') != null) {
                    GAMB_DUPLICA_WP_Log::trace("zipinfo doesn't exist so reverting to unzip");
                    $file_count_string = "unzip -l '$zipPath' | wc -l";
                }

                if ($file_count_string != '') {
                    $shellOutput = Shell::runCommand($file_count_string . ' | awk \'{print $1 }\'', Shell::AVAILABLE_COMMANDS);
                    $file_count  = ($shellOutput !== false)
                        ? trim($shellOutput->getOutputAsString())
                        : null;

                    if (is_numeric($file_count)) {
                    // Accounting for the sql and installer back files
                        $archive->file_count = (int) $file_count + 2;
                    } else {
                        $error_text = sprintf(
                            __("Error retrieving file count in Shell Zip %s.", 'gamb-duplica-wp'),
                            $file_count_string
                        );
                        GAMB_DUPLICA_WP_Log::trace("Executed file count string of $file_count_string");
                        GAMB_DUPLICA_WP_Log::trace($error_text);
                        $fix_text      = __("Click on button to switch to the DupArchive engine.", 'gamb-duplica-wp');
                        $system_global = SystemGlobalEntity::getInstance();
                        $system_global->addQuickFix(
                            $error_text,
                            $fix_text,
                            [
                                'global' => ['archive_build_mode' => 3],
                            ]
                        );
                        GAMB_DUPLICA_WP_Log::error("$error_text  **RECOMMENDATION:$fix_text", '', false);
                        GAMB_DUPLICA_WP_Log::trace("$error_text  **RECOMMENDATION:$fix_text");
                        $build_progress->failed = true;
                        $archive->file_count    = -2;
                        return true;
                    }
                } else {
                    GAMB_DUPLICA_WP_Log::trace("zipinfo doesn't exist");
                    // The -1 and -2 should be constants since they signify different things
                    $archive->file_count = -1;
                }
            } else {
                $archive->file_count = 2;
                // Installer bak and database.sql
            }

            GAMB_DUPLICA_WP_Log::trace("archive file count from shellzip is $archive->file_count");
            $build_progress->archive_built = true;
            $build_progress->retries       = 0;
            $package->update();
            $timerAllEnd = GAMB_DUPLICA_WP_U::getMicrotime();
            $timerAllSum = GAMB_DUPLICA_WP_U::elapsedTime($timerAllEnd, $build_progress->archive_start_time);
            $zipFileSize = @filesize($zipPath);
            GAMB_DUPLICA_WP_Log::info("COMPRESSED SIZE: " . GAMB_DUPLICA_WP_U::byteSize($zipFileSize));
            GAMB_DUPLICA_WP_Log::info("ARCHIVE RUNTIME: {$timerAllSum}");
            GAMB_DUPLICA_WP_Log::info("MEMORY STACK: " . GAMB_DUPLICA_WP_Server::getPHPMemory());
        } catch (Exception $e) {
            GAMB_DUPLICA_WP_Log::error("Runtime error in shell exec zip compression.", "Exception: {$e}");
        }

        return true;
    }
}
