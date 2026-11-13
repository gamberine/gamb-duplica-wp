<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Libs\DupArchive\Processors;

/**
 *Failure class
 */
class DupArchiveProcessingFailure
{
    const TYPE_UNKNOWN   = 0;
    const TYPE_FILE      = 1;
    const TYPE_DIRECTORY = 2;

    /** @var int<0,2> */
    public $type = self::TYPE_UNKNOWN;
    /** @var string */
    public $description = '';
    /** @var string */
    public $subject = '';
    /** @var bool */
    public $isCritical = false;
}
