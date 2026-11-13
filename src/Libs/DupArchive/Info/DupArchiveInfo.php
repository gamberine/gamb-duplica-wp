<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Libs\DupArchive\Info;

use GambDuplicaWP\Libs\DupArchive\Headers\DupArchiveDirectoryHeader;
use GambDuplicaWP\Libs\DupArchive\Headers\DupArchiveFileHeader;
use GambDuplicaWP\Libs\DupArchive\Headers\DupArchiveHeader;

class DupArchiveInfo
{
    /** @var ?DupArchiveHeader */
    public $archiveHeader;
    /** @var DupArchiveFileHeader[] */
    public $fileHeaders = [];
    /** @var DupArchiveDirectoryHeader[] */
    public $directoryHeaders = [];

    /**
     * Class constructor
     */
    public function __construct()
    {
    }
}
