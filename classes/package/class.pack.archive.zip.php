<?php

/**
 * Class to create a zip file using PHP ZipArchive
 *
 * Standard: PSR-2 (almost)
 *
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package    DUP_PRO
 * @subpackage classes/package
 * @copyright  (c) 2017, Snapcreek LLC
 * @license    https://opensource.org/licenses/GPL-3.0 GNU Public License
 * @since      1.0.0
 *
 * @notes: Trace process time
 *  $timer01 = GAMB_DUPLICA_WP_U::getMicrotime();
 *  GAMB_DUPLICA_WP_Log::trace("SCAN TIME-B = " . GAMB_DUPLICA_WP_U::elapsedTime(GAMB_DUPLICA_WP_U::getMicrotime(), $timer01));
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use gamb-duplica-wp\Libs\Index\FileIndexManager;
use gamb-duplica-wp\Libs\Snap\SnapIO;
use gamb-duplica-wp\Models\SystemGlobalEntity;
use gamb-duplica-wp\Package\Create\BuildProgress;
use gamb-duplica-wp\Utils\ZipArchiveExtended;

class GAMB_DUPLICA_WP_ZipArchive
{
    /** @var GAMB_DUPLICA_WP_Global_Entity */
    private $global;
    /** @var bool */
    private $optMaxBuildTimeOn = true;
    /** @var int */
    private $maxBuildTimeFileSize = 100000;
    /** @var int */
    private $throttleDelayInUs = 0;
    /** @var GAMB_DUPLICA_WP_Package */
    private $package;
    /** @var ZipArchiveExtended */
    private $zipArchive;

    /**
     * Class constructor
     *
     * @param GAMB_DUPLICA_WP_Package $package The Backup to create the zip file for
     */
    public function __construct(GAMB_DUPLICA_WP_Package $package)
    {
        $this->global            = GAMB_DUPLICA_WP_Global_Entity::getInstance();
        $this->optMaxBuildTimeOn = ($this->global->max_package_runtime_in_min > 0);
        $this->throttleDelayInUs = $this->global->getMicrosecLoadReduction();

        $this->package    = $package;
        $this->zipArchive = new ZipArchiveExtended($this->package->StorePath . '/' . $this->package->Archive->File);

        $password = $this->package->Archive->getArchivePassword();
        if (strlen($password) > 0) {
            $this->zipArchive->setEncrypt(true, $password);
        }
    }

    /**
     * Creates the zip file and adds the SQL file to the archive
     *
     * @param BuildProgress $build_progress A copy of the current build progress
     *
     * @return bool     Returns true if the process was successful
     */
    public function create(BuildProgress $build_progress)
    {
        try {
            if (!ZipArchiveExtended::isPhpZipAvailable()) {
                GAMB_DUPLICA_WP_Log::trace("Zip archive doesn't exist?");
                return false;
            }

            $this->package->safe_tmp_cleanup(true);
            if ($this->package->ziparchive_mode == GAMB_DUPLICA_WP_ZipArchive_Mode::SingleThread) {
                return $this->createSingleThreaded($build_progress);
            } else {
                return $this->createMultiThreaded($build_progress);
            }
        } catch (Exception $ex) {
            GAMB_DUPLICA_WP_Log::error("Runtime error in class-package-archive-zip.php.", "Exception: {$ex}");
            return false;
        }
    }

    /**
     * Creates the zip file using a single thread approach
     *
     * @param BuildProgress $build_progress A copy of the current build progress
     *
     * @return bool     Returns true if the process was successful
     */
    private function createSingleThreaded(BuildProgress $build_progress)
    {
        $countFiles  = 0;
        $compressDir = rtrim(SnapIO::safePath($this->package->Archive->PackDir), '/');
        $sqlPath     = $this->package->StorePath . '/' . $this->package->Database->File;
        $zipPath     = $this->package->StorePath . '/' . $this->package->Archive->File;
        $filterDirs  = empty($this->package->Archive->FilterDirs)  ? 'not set' : rtrim(str_replace(';', "\n\t", $this->package->Archive->FilterDirs));
        $filterFiles = empty($this->package->Archive->FilterFiles) ? 'not set' : rtrim(str_replace(';', "\n\t", $this->package->Archive->FilterFiles));
        $filterExts  = empty($this->package->Archive->FilterExts)  ? 'not set' : $this->package->Archive->FilterExts;
        $filterOn    = ($this->package->Archive->FilterOn) ? 'ON' : 'OFF';
        $validation  = ($this->global->ziparchive_validation) ? 'ON' : 'OFF';
        $compression = $build_progress->current_build_compression ? 'ON' : 'OFF';
        $targetRoot  = GAMB_DUPLICA_WP_Archive::getTargetRootPath();

        $this->zipArchive->setCompressed($build_progress->current_build_compression);
        //PREVENT RETRIES PAST 3:  Default is 10 (GAMB_DUPLICA_WP_Constants::MAX_BUILD_RETRIES)
        //since this is ST Mode no reason to keep trying like MT
        if ($build_progress->retries >= 3) {
            $err = __('Backup build appears stuck so marking Backup as failed. Is the PHP or Web Server timeouts too low?', 'gamb-duplica-wp');
            GAMB_DUPLICA_WP_Log::error(__('Build Failure', 'gamb-duplica-wp'), $err, false);
            GAMB_DUPLICA_WP_Log::trace($err);
            return $build_progress->failed = true;
        } else {
            if ($build_progress->retries > 0) {
                GAMB_DUPLICA_WP_Log::infoTrace("**NOTICE: Retry count at: {$build_progress->retries}");
            }
            $build_progress->retries++;
            $this->package->update();
        }

        //LOAD SCAN REPORT
        try {
            $scanReport = $this->package->getScanReportFromJson(gamb-duplica-wp_PRO_SSDIR_PATH_TMP . "/{$this->package->getNameHash()}_scan.json");
        } catch (GAMB_DUPLICA_WP_NoScanFileException $ex) {
            GAMB_DUPLICA_WP_Log::trace("**** scan file doesn't exist!!");
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

        //============================================
        //ST: START ZIP
        //============================================
        if ($build_progress->archive_started === false) {
            GAMB_DUPLICA_WP_Log::info("\n********************************************************************************");
            GAMB_DUPLICA_WP_Log::info("ARCHIVE ZipArchive Single-Threaded");
            GAMB_DUPLICA_WP_Log::info("********************************************************************************");
            GAMB_DUPLICA_WP_Log::info("ARCHIVE DIR:  " . $compressDir);
            GAMB_DUPLICA_WP_Log::info("ARCHIVE FILE: " . basename($zipPath));
            GAMB_DUPLICA_WP_Log::info("COMPRESSION: *{$compression}*");
            GAMB_DUPLICA_WP_Log::info("VALIDATION: *{$validation}*");
            GAMB_DUPLICA_WP_Log::info("FILTERS: *{$filterOn}*");
            GAMB_DUPLICA_WP_Log::info("DIRS:\t{$filterDirs}");
            GAMB_DUPLICA_WP_Log::info("EXTS:  {$filterExts}");
            GAMB_DUPLICA_WP_Log::info("FILES:  {$filterFiles}");
            GAMB_DUPLICA_WP_Log::info("----------------------------------------");
            GAMB_DUPLICA_WP_Log::info("COMPRESSING");
            GAMB_DUPLICA_WP_Log::info("SIZE:\t" . $scanReport->ARC->Size);
            GAMB_DUPLICA_WP_Log::info("STATS:\tDirs " . $scanReport->ARC->DirCount . " | Files " . $scanReport->ARC->FileCount . " | Total " . $scanReport->ARC->FullCount);
            if (($scanReport->ARC->DirCount == '') || ($scanReport->ARC->FileCount == '') || ($scanReport->ARC->FullCount == '')) {
                GAMB_DUPLICA_WP_Log::error('Invalid Scan Report Detected', 'Invalid Scan Report Detected', false);
                return $build_progress->failed = true;
            }
            $build_progress->archive_started    = true;
            $build_progress->archive_start_time = GAMB_DUPLICA_WP_U::getMicrotime();
        }

        //============================================
        //ST: ZIP DIRECTORIES
        //Keep this loop tight: ZipArchive can handle over 10k+ dir entries in under 0.01 seconds.
        //Its really fast without files so no need to do status pushes or other checks in loop
        //============================================
        $indexManager = $this->package->Archive->getIndexManager();
        if ($build_progress->next_archive_dir_index < $scanReport->ARC->DirCount) {
            if (!$this->zipArchive->open()) {
                GAMB_DUPLICA_WP_Log::error("Couldn't open $zipPath", '', false);
                return $build_progress->failed = true;
            }

            foreach ($indexManager->iteratePaths(FileIndexManager::LIST_TYPE_DIRS, $targetRoot) as $dir) {
                $emptyDir = $this->package->Archive->getLocalDirPath($dir);
                GAMB_DUPLICA_WP_Log::trace("ADD DIR TO ZIP: '{$emptyDir}'");
                if (!$this->zipArchive->addEmptyDir($emptyDir)) {
                    if (empty($compressDir) || strpos($dir, rtrim($compressDir, '/')) != 0) {
                        GAMB_DUPLICA_WP_Log::infoTrace("WARNING: Unable to zip directory: '{$dir}'");
                    }
                }
                $build_progress->next_archive_dir_index++;
            }

            if ($this->zipArchive->close()) {
                $this->package->update();
            } else {
                $err = 'ZipArchive close failure during directory add phase.';
                $this->setDupArchiveSwitchFix($err);
                return $build_progress->failed = true;
            }
        }

        //============================================
        //ST: ZIP FILES
        //============================================
        if ($build_progress->archive_built === false) {
            if ($this->zipArchive->open() === false) {
                GAMB_DUPLICA_WP_Log::error("Can not open zip file at: [{$zipPath}]", '', false);
                return $build_progress->failed = true;
            }

            // Since we have to estimate progress in Single Thread mode
            // set the status when we start archiving just like Shell Exec
            $this->package->set_status(GAMB_DUPLICA_WP_PackageStatus::ARCSTART);
            $total_file_size       = 0;
            $total_file_count_trip = ($scanReport->ARC->UFileCount + 1000);
            foreach ($indexManager->iteratePaths(FileIndexManager::LIST_TYPE_FILES, $targetRoot) as $file) {
                //NON-ASCII check
                if (preg_match('/[^\x20-\x7f]/', $file)) {
                    if (!$this->isUTF8FileSafe($file)) {
                        continue;
                    }
                }

                if ($this->global->ziparchive_validation) {
                    if (!is_readable($file)) {
                        GAMB_DUPLICA_WP_Log::infoTrace("NOTICE: File [{$file}] is unreadable!");
                        continue;
                    }
                }

                $local_name = $this->package->Archive->getLocalFilePath($file);
                if (!$this->zipArchive->addFile($file, $local_name)) {
                    // Assumption is that we continue?? for some things this would be fatal others it would be ok - leave up to user
                    GAMB_DUPLICA_WP_Log::info("WARNING: Unable to zip file: {$file}");
                    continue;
                }

                $total_file_size += filesize($file);

                //ST: SERVER THROTTLE
                if ($this->throttleDelayInUs !== 0) {
                    usleep($this->throttleDelayInUs);
                }

                //Prevent Overflow
                if ($countFiles++ > $total_file_count_trip) {
                    GAMB_DUPLICA_WP_Log::error("ZipArchive-ST: file loop overflow detected at {$countFiles}", '', false);
                    return $build_progress->failed = true;
                }
            }

            //START ARCHIVE CLOSE
            $total_file_size_easy = GAMB_DUPLICA_WP_U::byteSize($total_file_size);
            GAMB_DUPLICA_WP_Log::trace("Doing final zip close after adding $total_file_size_easy ({$total_file_size})");
            if ($this->zipArchive->close()) {
                GAMB_DUPLICA_WP_Log::trace("Final zip closed.");
                $build_progress->next_archive_file_index = $countFiles;
                $build_progress->archive_built           = true;
                $this->package->update();
            } else {
                if ($this->global->ziparchive_validation === false) {
                    $this->global->ziparchive_validation = true;
                    $this->global->save();
                    GAMB_DUPLICA_WP_Log::infoTrace("**NOTICE: ZipArchive: validation mode enabled");
                } else {
                    $err = 'ZipArchive close failure during file phase with file validation enabled';
                    $this->setDupArchiveSwitchFix($err);
                    return $build_progress->failed = true;
                }
            }
        }

        //============================================
        //ST: LOG FINAL RESULTS
        //============================================
        if ($build_progress->archive_built) {
            $timerAllEnd = GAMB_DUPLICA_WP_U::getMicrotime();
            $timerAllSum = GAMB_DUPLICA_WP_U::elapsedTime($timerAllEnd, $build_progress->archive_start_time);
            $zipFileSize = @filesize($zipPath);
            GAMB_DUPLICA_WP_Log::info("MEMORY STACK: " . GAMB_DUPLICA_WP_Server::getPHPMemory());
            GAMB_DUPLICA_WP_Log::info("FINAL SIZE: " . GAMB_DUPLICA_WP_U::byteSize($zipFileSize));
            GAMB_DUPLICA_WP_Log::info("ARCHIVE RUNTIME: {$timerAllSum}");

            if ($this->zipArchive->open()) {
                $this->package->Archive->file_count = $this->zipArchive->getNumFiles();
                $this->package->update();
                $this->zipArchive->close();
            } else {
                GAMB_DUPLICA_WP_Log::error("ZipArchive open failure.", "Encountered when retrieving final archive file count.", false);
                return $build_progress->failed = true;
            }
        }

        return true;
    }

    /**
     * Creates the zip file using a multi-thread approach
     *
     * @param BuildProgress $build_progress A copy of the current build progress
     *
     * @return bool Returns true if the process was successful
     */
    private function createMultiThreaded(BuildProgress $build_progress)
    {
        $timed_out   = false;
        $countFiles  = 0;
        $compressDir = rtrim(SnapIO::safePath($this->package->Archive->PackDir), '/');
        $sqlPath     = $this->package->StorePath . '/' . $this->package->Database->File;
        $zipPath     = $this->package->StorePath . '/' . $this->package->Archive->File;
        $filterDirs  = empty($this->package->Archive->FilterDirs)  ? 'not set' : rtrim(str_replace(';', "\n\t", $this->package->Archive->FilterDirs));
        $filterFiles = empty($this->package->Archive->FilterFiles) ? 'not set' : rtrim(str_replace(';', "\n\t", $this->package->Archive->FilterFiles));
        $filterExts  = empty($this->package->Archive->FilterExts) ? 'not set' : $this->package->Archive->FilterExts;
        $filterOn    = ($this->package->Archive->FilterOn) ? 'ON' : 'OFF';
        $compression = $build_progress->current_build_compression ? 'ON' : 'OFF';
        $this->zipArchive->setCompressed($build_progress->current_build_compression);
        $scanFilepath = gamb-duplica-wp_PRO_SSDIR_PATH_TMP . "/{$this->package->getNameHash()}_scan.json";
        $targetRoot   = GAMB_DUPLICA_WP_Archive::getTargetRootPath();

        //LOAD SCAN REPORT
        try {
            $scanReport = $this->package->getScanReportFromJson($scanFilepath);
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

        //============================================
        //MT: START ZIP & ADD SQL FILE
        //============================================
        if ($build_progress->archive_started === false) {
            GAMB_DUPLICA_WP_Log::info("\n********************************************************************************");
            GAMB_DUPLICA_WP_Log::info("ARCHIVE Mode:ZipArchive Multi-Threaded");
            GAMB_DUPLICA_WP_Log::info("********************************************************************************");
            GAMB_DUPLICA_WP_Log::info("ARCHIVE DIR:  " . $compressDir);
            GAMB_DUPLICA_WP_Log::info("ARCHIVE FILE: " . basename($zipPath));
            GAMB_DUPLICA_WP_Log::info("COMPRESSION: *{$compression}*");
            GAMB_DUPLICA_WP_Log::info("FILTERS: *{$filterOn}*");
            GAMB_DUPLICA_WP_Log::info("DIRS:  {$filterDirs}");
            GAMB_DUPLICA_WP_Log::info("EXTS:  {$filterExts}");
            GAMB_DUPLICA_WP_Log::info("FILES:  {$filterFiles}");
            GAMB_DUPLICA_WP_Log::info("----------------------------------------");
            GAMB_DUPLICA_WP_Log::info("COMPRESSING");
            GAMB_DUPLICA_WP_Log::info("SIZE:\t" . $scanReport->ARC->Size);
            GAMB_DUPLICA_WP_Log::info("STATS:\tDirs " . $scanReport->ARC->DirCount . " | Files " . $scanReport->ARC->FileCount . " | Total " . $scanReport->ARC->FullCount);
            if (($scanReport->ARC->DirCount == '') || ($scanReport->ARC->FileCount == '') || ($scanReport->ARC->FullCount == '')) {
                GAMB_DUPLICA_WP_Log::error('Invalid Scan Report Detected', 'Invalid Scan Report Detected', false);
                return $build_progress->failed = true;
            }
        }

        //============================================
        //MT: ZIP DIRECTORIES
        //Keep this loop tight: ZipArchive can handle over 10k dir entries in under 0.01 seconds.
        //Its really fast without files no need to do status pushes or other checks in loop
        //============================================
        $indexManager = $this->package->Archive->getIndexManager();
        if ($this->zipArchive->open()) {
            foreach ($indexManager->iteratePaths(FileIndexManager::LIST_TYPE_DIRS, $targetRoot) as $dir) {
                $emptyDir = $this->package->Archive->getLocalDirPath($dir);
                GAMB_DUPLICA_WP_Log::trace("ADD DIR TO ZIP: '{$emptyDir}'");
                if (!$this->zipArchive->addEmptyDir($emptyDir)) {
                    if (empty($compressDir) || strpos($dir, rtrim($compressDir, '/')) != 0) {
                        GAMB_DUPLICA_WP_Log::infoTrace("WARNING: Unable to zip directory: '{$dir}'");
                    }
                }
                $build_progress->next_archive_dir_index++;
            }

            $this->package->update();
            if ($build_progress->timedOut($this->global->php_max_worker_time_in_sec)) {
                $timed_out = true;
                $diff      = time() - $build_progress->thread_start_time;
                GAMB_DUPLICA_WP_Log::trace("Timed out after hitting thread time of $diff {$this->global->php_max_worker_time_in_sec} so quitting zipping early in the directory phase");
            }
        } else {
            GAMB_DUPLICA_WP_Log::error("Couldn't open $zipPath", '', false);
            return $build_progress->failed = true;
        }

        if ($this->zipArchive->close() === false) {
            $err = __('ZipArchive close failure during directory add phase.', 'gamb-duplica-wp');
            $this->setDupArchiveSwitchFix($err);
            return $build_progress->failed = true;
        }

        //============================================
        //MT: ZIP FILES
        //============================================
        if ($timed_out === false) {
            // PREVENT RETRIES (10x)
            if ($build_progress->retries > GAMB_DUPLICA_WP_Constants::MAX_BUILD_RETRIES) {
                $err = __('Zip build appears stuck.', 'gamb-duplica-wp');
                $this->setDupArchiveSwitchFix($err);

                $error_msg = __('Backup build appears stuck so marking Backup as failed. Recommend setting Settings > Backups > Archive Engine to DupArchive', 'gamb-duplica-wp');
                GAMB_DUPLICA_WP_Log::error(__('Build Failure', 'gamb-duplica-wp'), $error_msg, false);
                GAMB_DUPLICA_WP_Log::trace($error_msg);
                return $build_progress->failed = true;
            } else {
                $build_progress->retries++;
                $this->package->update();
            }

            $zip_is_open                    = false;
            $total_file_size                = 0;
            $incremental_file_size          = 0;
            $used_zip_file_descriptor_count = 0;
            $total_file_count               = empty($scanReport->ARC->UFileCount) ? 0 : $scanReport->ARC->UFileCount;
            foreach ($indexManager->iteratePaths(FileIndexManager::LIST_TYPE_FILES, $targetRoot) as $file) {
                if ($zip_is_open || ($countFiles == $build_progress->next_archive_file_index)) {
                    if ($zip_is_open === false) {
                        GAMB_DUPLICA_WP_Log::trace("resuming archive building at file # $countFiles");
                        if ($this->zipArchive->open() !== true) {
                            GAMB_DUPLICA_WP_Log::error("Couldn't open $zipPath", '', false);
                            $build_progress->failed = true;
                            return true;
                        }
                        $zip_is_open = true;
                    }

                    //NON-ASCII check
                    if (preg_match('/[^\x20-\x7f]/', $file)) {
                        if (!$this->isUTF8FileSafe($file)) {
                            continue;
                        }
                    } elseif (!file_exists($file)) {
                            GAMB_DUPLICA_WP_Log::trace("NOTICE: ASCII file [{$file}] does not exist!");
                            continue;
                    }

                    $local_name = $this->package->Archive->getLocalFilePath($file);
                    $file_size  = filesize($file);
                    $zip_status = $this->zipArchive->addFile($file, $local_name);

                    if ($zip_status) {
                        $total_file_size       += $file_size;
                        $incremental_file_size += $file_size;
                    } else {
                        // Assumption is that we continue?? for some things this would be fatal others it would be ok - leave up to user
                        GAMB_DUPLICA_WP_Log::info("WARNING: Unable to zip file: {$file}");
                    }

                    $countFiles++;
                    $chunk_size_in_bytes = $this->global->ziparchive_chunk_size_in_mb * 1000000;
                    if ($incremental_file_size > $chunk_size_in_bytes) {
                    // Only close because of chunk size and file descriptors when in legacy mode
                        GAMB_DUPLICA_WP_Log::trace("closing zip because ziparchive mode = {$this->global->ziparchive_mode} fd count = $used_zip_file_descriptor_count or incremental file size=$incremental_file_size and chunk size = $chunk_size_in_bytes");
                        $incremental_file_size          = 0;
                        $used_zip_file_descriptor_count = 0;
                        if ($this->zipArchive->close() == true) {
                            $adjusted_percent                        = floor(GAMB_DUPLICA_WP_PackageStatus::ARCSTART + ((GAMB_DUPLICA_WP_PackageStatus::ARCDONE - GAMB_DUPLICA_WP_PackageStatus::ARCSTART) * ($countFiles / (float) $total_file_count)));
                            $build_progress->next_archive_file_index = $countFiles;
                            $build_progress->retries                 = 0;
                            $this->package->Status                   = $adjusted_percent;
                            $this->package->update();
                            $zip_is_open = false;
                            GAMB_DUPLICA_WP_Log::trace("closed zip");
                        } else {
                            $err = 'ZipArchive close failure during file phase using multi-threaded setting.';
                            $this->setDupArchiveSwitchFix($err);
                            return $build_progress->failed = true;
                        }
                    }

                    //MT: SERVER THROTTLE
                    if ($this->throttleDelayInUs !== 0) {
                        usleep($this->throttleDelayInUs);
                    }

                    //MT: MAX WORKER TIME (SECS)
                    if ($build_progress->timedOut($this->global->php_max_worker_time_in_sec)) {
                        // Only close because of timeout
                        $timed_out = true;
                        $diff      = time() - $build_progress->thread_start_time;
                        GAMB_DUPLICA_WP_Log::trace("Timed out after hitting thread time of $diff so quitting zipping early in the file phase");
                        break;
                    }

                    //MT: MAX BUILD TIME (MINUTES)
                    //Only stop to check on larger files above 100K to avoid checking every single file
                    if ($file_size > $this->maxBuildTimeFileSize && $this->optMaxBuildTimeOn) {
                        $elapsed_sec     = time() - $this->package->timer_start;
                        $elapsed_minutes = $elapsed_sec / 60;
                        if ($elapsed_minutes > $this->global->max_package_runtime_in_min) {
                            GAMB_DUPLICA_WP_Log::trace("ZipArchive: Multi-thread max build time {$this->global->max_package_runtime_in_min} minutes reached killing process.");
                            return false;
                        }
                    }
                } else {
                    $countFiles++;
                }
            }

            GAMB_DUPLICA_WP_Log::trace("total file size added to zip = $total_file_size");
            if ($zip_is_open) {
                GAMB_DUPLICA_WP_Log::trace("Doing final zip close after adding $incremental_file_size");
                if ($this->zipArchive->close()) {
                    GAMB_DUPLICA_WP_Log::trace("Final zip closed.");
                    $build_progress->next_archive_file_index = $countFiles;
                    $build_progress->retries                 = 0;
                    $this->package->update();
                } else {
                    $err = __('ZipArchive close failure.', 'gamb-duplica-wp');
                    $this->setDupArchiveSwitchFix($err);
                    GAMB_DUPLICA_WP_Log::error($err);
                    return $build_progress->failed = true;
                }
            }
        }


        //============================================
        //MT: LOG FINAL RESULTS
        //============================================
        if ($timed_out === false) {
            $build_progress->archive_built = true;
            $build_progress->retries       = 0;
            $this->package->update();
            $timerAllEnd = GAMB_DUPLICA_WP_U::getMicrotime();
            $timerAllSum = GAMB_DUPLICA_WP_U::elapsedTime($timerAllEnd, $build_progress->archive_start_time);
            $zipFileSize = @filesize($zipPath);
            GAMB_DUPLICA_WP_Log::info("COMPRESSED SIZE: " . GAMB_DUPLICA_WP_U::byteSize($zipFileSize));
            GAMB_DUPLICA_WP_Log::info("ARCHIVE RUNTIME: {$timerAllSum}");
            GAMB_DUPLICA_WP_Log::info("MEMORY STACK: " . GAMB_DUPLICA_WP_Server::getPHPMemory());
            if ($this->zipArchive->open() === true) {
                $this->package->Archive->file_count = $this->zipArchive->getNumFiles();
                $this->package->update();
                $this->zipArchive->close();
            } else {
                GAMB_DUPLICA_WP_Log::error("ZipArchive open failure.", "Encountered when retrieving final archive file count.", false);
                return $build_progress->failed = true;
            }
        }

        return !$timed_out;
    }

    /**
     * Encodes a UTF8 file and then determines if it is safe to add to an archive
     *
     * @param string $file The file to test
     *
     * @return bool Returns true if the file is readable and safe to add to archive
     */
    private function isUTF8FileSafe($file)
    {
        $is_safe       = true;
        $original_file = $file;
        // Necessary for adfron type files
        if (GAMB_DUPLICA_WP_STR::hasUTF8($file)) {
            $file = mb_convert_encoding($file, 'ISO-8859-1', 'UTF-8');
        }

        if (file_exists($file) === false) {
            if (file_exists($original_file) === false) {
                GAMB_DUPLICA_WP_Log::trace("$file CAN'T BE READ!");
                GAMB_DUPLICA_WP_Log::info("WARNING: Unable to zip file: {$file}. Cannot be read");
                $is_safe = false;
            }
        }

        return $is_safe;
    }

    /**
     * Wrapper for switching to DupArchive quick fix
     *
     * @param string $message The error message
     *
     * @return void
     */
    private function setDupArchiveSwitchFix($message)
    {
        $fix_text = __('Click to switch archive engine to DupArchive.', 'gamb-duplica-wp');

        $this->setFix(
            $message,
            $fix_text,
            [
                'global' => [
                    'archive_build_mode' => GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive,
                ],
            ]
        );
    }

    /**
     * Sends an error to the trace and build logs and sets the UI message
     *
     * @param string  $message The error message
     * @param string  $fix     The details for how to fix the issue
     * @param mixed[] $option  The options to set
     *
     * @return void
     */
    private function setFix($message, $fix, $option)
    {
        GAMB_DUPLICA_WP_Log::trace($message);
        GAMB_DUPLICA_WP_Log::error("$message **FIX:  $fix.", '', false);
        $system_global = SystemGlobalEntity::getInstance();
        $system_global->addQuickFix($message, $fix, $option);
    }
}
