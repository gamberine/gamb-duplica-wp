<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Addons\GDriveAddon\Models;

use GambDuplicaWP\Models\Storages\StoragePathInfo;
use VendorGambDuplicaWP\Google\Service\Drive\DriveFile;

class GDriveStoragePathInfo extends StoragePathInfo
{
    /** @var string */
    public $id = '';

    /** @var string */
    public $name = '';

    /** @var string */
    public $mimeType = '';

    /** @var string */
    public $webUrl = '';

    /** @var string */
    public $md5Checksum = '';

    /** @var DriveFile */
    public $file;
}
