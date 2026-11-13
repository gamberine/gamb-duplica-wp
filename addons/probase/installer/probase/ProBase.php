<?php

/**
 * Version Pro Base Installer addon class
 *
 * Name: gamb-duplica-wp PRO base
 * Version: 1
 * Author: Snap Creek
 * Author URI: http://snapcreek.com
 *
 * @category  gamb-duplica-wp
 * @package   Installer
 * @author    Snapcreek <admin@snapcreek.com>
 * @copyright 2011-2021  Snapcreek LLC
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @version   GIT: $Id$
 * @link      http://snapcreek.com
 */

namespace GambDuplicaWP\Installer\Addons\ProBase;

use GambDuplicaWP\Installer\Core\Hooks\HooksMng;
use GambDuplicaWP\Installer\Core\Params\Items\ParamItem;

/**
 * Version Pro Base Installer addon class
 *
 * @category gamb-duplica-wp
 * @package  Installer
 * @author   Snapcreek <admin@snapcreek.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link     http://snapcreek.com
 */
class ProBase extends \GambDuplicaWP\Installer\Core\Addons\InstAbstractAddonCore
{
    /**
     * Main init addon
     *
     * @return void
     */
    public function init()
    {
        HooksMng::getInstance()->addFilter(
            'dupx_main_header',
            fn($value): string => 'gamb-duplica-wp PRO'
        );

        HooksMng::getInstance()->addFilter('installer_get_init_params', [self::class, 'getInitParams']);
        HooksMng::getInstance()->addAction(
            'after_params_overwrite',
            [
                AdvancedParams::class,
                'updateParamsAfterOverwrite',
            ]
        );
    }


    /**
     * getInitParams
     *
     * @param ParamItem[] $params params list
     *
     * @return ParamItem[]
     */
    public static function getInitParams($params)
    {
        $advParams = [];
        AdvancedParams::init($advParams);
        return array_merge($params, $advParams);
    }

    /**
     * Get addon main folder
     *
     * @return string
     */
    public static function getAddonPath()
    {
        return __DIR__;
    }

    /**
     * Get addon main file
     *
     * @return string
     */
    public static function getAddonFile()
    {
        return __FILE__;
    }
}
