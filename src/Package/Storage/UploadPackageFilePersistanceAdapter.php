<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Package\Storage;

use GAMB_DUPLICA_WP_Package;
use GAMB_DUPLICA_WP_Package_Upload_Info;
use GambDuplicaWP\Libs\Chunking\Persistance\PersistanceAdapterInterface;
use GambDuplicaWP\Libs\Chunking\Iterators\GenericSeekableIteratorInterface;

class UploadPackageFilePersistanceAdapter implements PersistanceAdapterInterface
{
    /** @var GAMB_DUPLICA_WP_Package_Upload_Info */
    protected $uploadInfo;
    /** @var GAMB_DUPLICA_WP_Package */
    protected $package;

    /**
     * @param GAMB_DUPLICA_WP_Package_Upload_Info $uploadInfo upload info object
     * @param GAMB_DUPLICA_WP_Package             $package    package object
     */
    public function __construct(GAMB_DUPLICA_WP_Package_Upload_Info $uploadInfo, GAMB_DUPLICA_WP_Package $package)
    {
        $this->uploadInfo = $uploadInfo;
        $this->package    = $package;
    }

    /**
     * Load data from previous iteration if exists
     *
     * @return mixed
     */
    public function getPersistanceData()
    {
        return empty($this->uploadInfo->chunkPosition) ? null : $this->uploadInfo->chunkPosition;
    }

    /**
     * Save data for next step
     *
     * @param mixed                            $data data to save
     * @param GenericSeekableIteratorInterface $it   current iterator
     *
     * @return bool This function returns true on success, or FALSE on failure.
     */
    public function savePersistanceData($data, GenericSeekableIteratorInterface $it)
    {
        $this->uploadInfo->progress      = $it->getProgressPerc();
        $this->uploadInfo->chunkPosition = $data;

        return $this->package->update();
    }

    /**
     * delete data
     *
     * @return bool
     */
    public function deletePersistanceData()
    {
        $this->uploadInfo->chunkPosition = [];
        return $this->package->update();
    }
}
