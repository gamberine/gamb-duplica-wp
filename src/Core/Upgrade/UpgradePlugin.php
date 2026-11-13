<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Core\Upgrade;

use GAMB_DUPLICA_WP_Log;
use gamb-duplica-wp\Core\CapMng;
use gamb-duplica-wp\Core\MigrationMng;

/**
 * Upgrade logic of plugin resides here
 *
 * GAMB_DUPLICA_WP_Plugin_Upgrade
 */
class UpgradePlugin
{
    const DUP_VERSION_OPT_KEY          = 'gamb-duplica-wp_pro_plugin_version';
    const DUP_INSTALL_INFO_OPT_KEY     = 'gamb-duplica-wp_pro_install_info';
    const GAMB_DUPLICA_WP_INSTALL_TIME_OLD_KEY = 'gamb-duplica-wp_pro_install_time';

    /**
     * Perform activation action.
     *
     * @return void
     */
    public static function onActivationAction()
    {
        // Init capabilities
        CapMng::getInstance();

        if (($oldDupVersion = get_option(self::DUP_VERSION_OPT_KEY, false)) === false) {
            self::newInstallation();
        } else {
            self::updateInstallation($oldDupVersion);
        }

        //Rename installer files if exists
        MigrationMng::renameInstallersPhpFiles();

        do_action('gamb-duplica-wp_pro_after_activation');
    }

    /**
     * Perform new installation.
     *
     * @return void
     */
    protected static function newInstallation()
    {
        UpgradeFunctions::performUpgrade(false, gamb-duplica-wp_PRO_VERSION);

        // WordPress Options Hooks
        self::updateOptionVersion();
        self::setInstallInfo();
    }

    /**
     * Perform update installation.
     *
     * @param string $oldVersion Old version.
     *
     * @return void
     */
    protected static function updateInstallation($oldVersion)
    {
        UpgradeFunctions::performUpgrade($oldVersion, gamb-duplica-wp_PRO_VERSION);

        //WordPress Options Hooks
        self::updateOptionVersion();
        self::setInstallInfo($oldVersion);
    }

        /**
     * Update install info.
     *
     * @param string $oldVersion The last/previous installed version, is empty for new installs
     *
     * @return array{version:string,time:int,updateTime:int}
     */
    public static function setInstallInfo($oldVersion = '')
    {
        if (empty($oldVersion) || ($installInfo = get_option(self::DUP_INSTALL_INFO_OPT_KEY, false)) === false) {
            // If is new installation or install info is not set generate new install info
            $installInfo = [
                'version'    => gamb-duplica-wp_PRO_VERSION,
                'time'       => time(),
                'updateTime' => time(),
            ];
        } else {
            $installInfo['updateTime'] = time();
        }

        if (($installTime = get_option(self::GAMB_DUPLICA_WP_INSTALL_TIME_OLD_KEY, false)) !== false) {
            // Migrate the previously used option to install info and remove old option if exists
            $installInfo['version'] = $oldVersion;
            $installInfo['time']    = $installTime;
            delete_option(self::GAMB_DUPLICA_WP_INSTALL_TIME_OLD_KEY);
        }

        delete_option(self::DUP_INSTALL_INFO_OPT_KEY);
        update_option(self::DUP_INSTALL_INFO_OPT_KEY, $installInfo, false);
        return $installInfo;
    }

    /**
     * Get install info.
     *
     * @return array{version:string,time:int,updateTime:int}
     */
    public static function getInstallInfo()
    {
        if (($installInfo = get_option(self::DUP_INSTALL_INFO_OPT_KEY, false)) === false) {
            $installInfo = self::setInstallInfo();
        }
        return $installInfo;
    }

    /**
     * Update option version.
     *
     * @return void
     */
    protected static function updateOptionVersion()
    {
        // WordPress Options Hooks
        if (update_option(self::DUP_VERSION_OPT_KEY, gamb-duplica-wp_PRO_VERSION, true) === false) {
            GAMB_DUPLICA_WP_Log::trace("Couldn't update gamb-duplica-wp_pro_plugin_version so deleting it.");

            delete_option(self::DUP_VERSION_OPT_KEY);

            if (update_option(self::DUP_VERSION_OPT_KEY, gamb-duplica-wp_PRO_VERSION, true) === false) { // @phpstan-ignore-line
                GAMB_DUPLICA_WP_Log::trace("Still couldn\'t update the option!");
            } else { // @phpstan-ignore-line
                GAMB_DUPLICA_WP_Log::trace("Option updated.");
            }
        }
    }
}
