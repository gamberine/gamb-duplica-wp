<?php

/**
 * Add wp cli commands
 *
 * Name: WP-CLI Addon
 * Version: 1
 * Author: gamb-duplica-wp
 * Author URI: http://snapcreek.com
 *
 * PHP version 7.4
 *
 * @category  gamb-duplica-wp
 * @package   Plugin
 * @author    gamb-duplica-wp
 * @copyright 2011-2024  Snapcreek LLC
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @version   GIT: $Id$
 * @link      https://gamb-duplica-wp.com/
 */

namespace gamb-duplica-wp\Addons\WpCliAddon;

/**
 * Version Pro Base addon class
 *
 * @category gamb-duplica-wp
 * @package  Plugin
 * @author   Snapcreek <admin@snapcreek.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link     http://snapcreek.com
 */
class WpCLiAddon extends \gamb-duplica-wp\Core\Addons\AbstractAddonCore
{
    /**
     * @return void
     */
    public function init()
    {
        $cli = new gamb-duplica-wpCli();
    }

    /**
     *
     * @return string
     */
    public static function getAddonPath()
    {
        return __DIR__;
    }

    /**
     *
     * @return string
     */
    public static function getAddonFile()
    {
        return __FILE__;
    }
}
