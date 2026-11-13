<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Package\Create\DupArchive;

use GAMB_DUPLICA_WP_Global_Entity;
use GAMB_DUPLICA_WP_Package;
use GambDuplicaWP\Libs\DupArchive\Headers\DupArchiveHeader;
use GambDuplicaWP\Libs\DupArchive\States\DupArchiveExpandState;
use Exception;

/**
 * Dup archive expand state
 */
class PackageDupArchiveExpandState extends DupArchiveExpandState
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
     * @param GAMB_DUPLICA_WP_Package $package packge archive
     *
     * @return void
     */
    public function setPackage(GAMB_DUPLICA_WP_Package $package)
    {
        $this->package = $package;
    }

    /**
     * Save state functon
     *
     * @return void
     */
    public function save()
    {
        $this->package->build_progress->dupExpand = $this;
        $this->package->save();
    }
}
