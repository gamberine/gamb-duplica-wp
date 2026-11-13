<?php

/**
 * cloudways custom hosting class
 *
 * Standard: PSR-2
 *
 * @package SC\DUPX\HOST
 * @link    http://www.php-fig.org/psr/psr-2/
 */

use GambDuplicaWP\Libs\Snap\SnapUtil;

class GAMB_DUPLICA_WP_Cloudways_Host implements GAMB_DUPLICA_WP_Host_interface
{
    public static function getIdentifier(): string
    {
        return GAMB_DUPLICA_WP_Custom_Host_Manager::HOST_CLOUDWAYS;
    }

    public function isHosting()
    {
        ob_start();
        SnapUtil::phpinfo();
        $serverinfo = ob_get_clean();
        return (strpos($serverinfo, "cloudwaysapps") !== false);
    }

    public function init()
    {
    }
}
