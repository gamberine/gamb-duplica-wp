<?php

/**
 * @package gamb-duplica-wp\Installer
 */

namespace gamb-duplica-wp\Installer\ViewHelpers;

use gamb-duplica-wp\Installer\Core\Security;
use gamb-duplica-wp\Installer\Core\InstState;

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
