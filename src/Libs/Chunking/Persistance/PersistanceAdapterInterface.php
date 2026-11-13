<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Libs\Chunking\Persistance;

use gamb-duplica-wp\Libs\Chunking\Iterators\GenericSeekableIteratorInterface;

/**
 * Interface for the class that needs to maintain the presence of the chunk manger
 */
interface PersistanceAdapterInterface
{
    /**
     * Load data from previous iteration if exists
     *
     * @return mixed return iterator position
     */
    public function getPersistanceData();

    /**
     * Delete stored data if exists
     *
     * @return bool This function returns true on success, or FALSE on failure.
     */
    public function deletePersistanceData();

    /**
     * Save data for next step
     *
     * @param mixed                            $data data to save
     * @param GenericSeekableIteratorInterface $it   current iterator
     *
     * @return bool This function returns true on success, or FALSE on failure.
     */
    public function savePersistanceData($data, GenericSeekableIteratorInterface $it);
}
