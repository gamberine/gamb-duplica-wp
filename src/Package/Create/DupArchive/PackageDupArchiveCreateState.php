<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Package\Create\DupArchive;

use GAMB_DUPLICA_WP_Global_Entity;
use GAMB_DUPLICA_WP_Log;
use GAMB_DUPLICA_WP_Package;
use gamb-duplica-wp\Libs\DupArchive\Headers\DupArchiveHeader;
use gamb-duplica-wp\Libs\DupArchive\Processors\DupArchiveProcessingFailure;
use gamb-duplica-wp\Libs\DupArchive\States\DupArchiveCreateState;
use Exception;

/**
 * Dup archvie Backup create state
 */
class PackageDupArchiveCreateState extends DupArchiveCreateState
{
    /** @var GAMB_DUPLICA_WP_Package */
    private $package;

    /**
     * Class constructor
     *
     * @param DupArchiveHeader $archiveHeader archive header
     * @param GAMB_DUPLICA_WP_Package  $package       package
     */
    public function __construct(DupArchiveHeader $archiveHeader, ?GAMB_DUPLICA_WP_Package $package = null)
    {
        if ($package == null) {
            throw new Exception('Package required');
        }
        $this->package = $package;
        parent::__construct($archiveHeader);
        $global                  = GAMB_DUPLICA_WP_Global_Entity::getInstance();
        $this->throttleDelayInUs = $global->getMicrosecLoadReduction();
    }

    /**
     * Filter props on json encode
     *
     * @return string[]
     */
    public function __sleep()
    {
        $props = array_keys(get_object_vars($this));
        return array_diff($props, ['package']);
    }

    /**
     * Set Backup
     *
     * @param GAMB_DUPLICA_WP_Package $package Backup descriptor
     *
     * @return void
     */
    public function setPackage(GAMB_DUPLICA_WP_Package $package)
    {
        $this->package = $package;
    }

    /**
     * Create new archive
     *
     * @param DupArchiveHeader $archiveHeader   archive header
     * @param GAMB_DUPLICA_WP_Package  $package         package descriptor
     * @param string           $archivePath     archive path
     * @param string           $basePath        base path
     * @param int<-1,max>      $timeSliceInSecs throttle
     *
     * @return PackageDupArchiveCreateState
     */
    public static function createNew(
        DupArchiveHeader $archiveHeader,
        GAMB_DUPLICA_WP_Package $package,
        $archivePath,
        $basePath,
        $timeSliceInSecs
    ) {
        GAMB_DUPLICA_WP_Log::info("CREATE ARCHIVE STATE FOR DUP ARCHIVE");

        $instance = new PackageDupArchiveCreateState($archiveHeader, $package);
        if (file_exists($archivePath)) {
            $instance->archiveOffset = (int) filesize($archivePath);
        } else {
            $instance->archiveOffset = 0;
        }

        $instance->archivePath     = $archivePath;
        $instance->basePath        = $basePath;
        $instance->timeSliceInSecs = $timeSliceInSecs;
        $instance->working         = true;
        $instance->startTimestamp  = time();
        $instance->save();
        return $instance;
    }

    /**
     * Add failure item
     *
     * @param int     $type        failure type enum
     * @param string  $subject     failure subject
     * @param string  $description failure description
     * @param boolean $isCritical  true if is critical
     *
     * @return DupArchiveProcessingFailure|false false if max filures is reachd
     */
    public function addFailure($type, $subject, $description, $isCritical = false)
    {
        $failure = parent::addFailure($type, $subject, $description, $isCritical);

        $buildProgress = $this->package->build_progress;
        if ($isCritical) {
            $buildProgress->failed = true;
        } elseif ($failure !== false) {
            $buildProgress->warnings[] = $this->getFailureString($failure);
        }

        return $failure;
    }

    /**
     * Save state functon
     *
     * @return void
     */
    public function save()
    {
        $this->package->build_progress->dupCreate               = $this;
        $this->package->build_progress->next_archive_dir_index  = $this->currentDirectoryIndex;
        $this->package->build_progress->next_archive_file_index = $this->currentFileIndex;
        $this->package->save();
    }
}
