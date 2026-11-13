<?php

/**
 * Logger for dup archive
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Installer\Core\Deploy\DupArchive;

use GambDuplicaWP\Installer\Utils\Log\Log;
use GambDuplicaWP\Libs\DupArchive\DupArchiveLoggerBase;
use Exception;

/**
 * Logger for dup archive
 */
class DawsLogger extends DupArchiveLoggerBase
{
    /**
     * Init logger
     *
     * @return void
     */
    public static function init()
    {
        set_error_handler([self::class, "terminateMissingVariables"], E_ERROR);
    }

    /**
     * Log function
     *
     * @param string    $s                       string to log
     * @param boolean   $flush                   if true flish log
     * @param ?callable $callingFunctionOverride call back function
     *
     * @return void
     */
    public function log($s, $flush = false, $callingFunctionOverride = null)
    {
        Log::info($s, Log::LV_DEFAULT, $flush);
    }

    /**
     * Throw exception on php error
     *
     * @param int    $errno   errno
     * @param string $errstr  error message
     * @param string $errfile file
     * @param int    $errline line
     *
     * @return bool
     */
    public static function terminateMissingVariables($errno, $errstr, $errfile, $errline): bool
    {
        Log::info("ERROR $errno, $errstr, {$errfile}:{$errline}");
        /**
         * INTERCEPT ON processRequest AND RETURN JSON STATUS
         */
        throw new Exception("ERROR:{$errfile}:{$errline} | " . $errstr, $errno);

        return true; // @phpstan-ignore-line
    }
}
