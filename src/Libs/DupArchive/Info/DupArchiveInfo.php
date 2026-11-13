<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Libs\DupArchive\Info;

use gamb-duplica-wp\Libs\DupArchive\Headers\DupArchiveDirectoryHeader;
use gamb-duplica-wp\Libs\DupArchive\Headers\DupArchiveFileHeader;
use gamb-duplica-wp\Libs\DupArchive\Headers\DupArchiveHeader;

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
