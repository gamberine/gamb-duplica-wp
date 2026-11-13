<?php

/**
 * @package gamb-duplica-wp\Installer
 */

namespace GambDuplicaWP\Installer\ViewHelpers;

use GambDuplicaWP\Installer\Core\Security;
use GambDuplicaWP\Installer\Core\InstState;

class Resources
{
    /**
     * Return assets base URL
     *
     * @return string
     */
    public static function getAssetsBaseUrl()
    {
        if (InstState::isBridgeInstall()) {
            return Security::getInstance()->getOriginalInstallerUrl();
        } else {
            return DUPX_INIT_URL;
        }
    }
}
