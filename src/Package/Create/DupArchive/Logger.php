<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Package\Create\DupArchive;

use GAMB_DUPLICA_WP_Log;
use gamb-duplica-wp\Libs\DupArchive\DupArchiveLoggerBase;

/**
 * Dup archive logger
 */
class Logger extends DupArchiveLoggerBase
{
    /**
     * Log function
     *
     * @param string  $s                       string to log
     * @param boolean $flush                   if true flish log
     * @param ?string $callingFunctionOverride calling function
     *
     * @return void
     */
    public function log($s, $flush = false, $callingFunctionOverride = null)
    {
        // rsr todo ignoring flush for now
        GAMB_DUPLICA_WP_Log::trace($s, true, $callingFunctionOverride);
    }
}
