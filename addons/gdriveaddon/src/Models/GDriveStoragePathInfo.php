<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Addons\GDriveAddon\Models;

use gamb-duplica-wp\Models\Storages\StoragePathInfo;
use Vendorgamb-duplica-wp\Google\Service\Drive\DriveFile;

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
