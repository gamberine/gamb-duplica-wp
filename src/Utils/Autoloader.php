<?php

/**
 * Auloader calsses
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Utils;

/**
 * Autoloader calss, dont user gamb-duplica-wp library here
 */
final class Autoloader extends AbstractAutoloader
{
    const VENDOR_PATH = GambDuplicaWP____PATH . '/vendor-prefixed/';

    /**
     * Register autoloader function
     *
     * @return void
     */
    public static function register()
    {
        spl_autoload_register([self::class, 'load']);
        self::loadFiles();
    }

    /**
     * Load class
     *
     * @param string $className class name
     *
     * @return void
     */
    public static function load($className)
    {
        if (strpos($className, self::ROOT_NAMESPACE) === 0) {
            if (($filepath = self::getAddonFile($className)) === false) {
                foreach (self::getNamespacesMapping() as $namespace => $mappedPath) {
                    if (strpos($className, (string) $namespace) !== 0) {
                        continue;
                    }

                    $filepath = self::getFilenameFromClass($className, $namespace, $mappedPath);
                    if (file_exists($filepath)) {
                        include $filepath;
                        return;
                    }
                }
            } else {
                if (file_exists($filepath)) {
                    include $filepath;
                    return;
                }
            }
        } elseif (strpos($className, self::ROOT_VENDOR) === 0) {
            foreach (self::getNamespacesVendorMapping() as $namespace => $mappedPath) {
                if (strpos($className, (string) $namespace) !== 0) {
                    continue;
                }

                $filepath = self::getFilenameFromClass($className, $namespace, $mappedPath);
                if (file_exists($filepath)) {
                    include $filepath;
                    return;
                }
            }
        } else {
            // @todo remove legacy logic in autoloading when gamb-duplica-wp is fully converted.
            $legacyMappging = self::customLegacyMapping();
            $legacyClass    = strtolower(ltrim($className, '\\'));
            if (array_key_exists($legacyClass, $legacyMappging)) {
                if (file_exists($legacyMappging[$legacyClass])) {
                    include $legacyMappging[$legacyClass];
                    return;
                }
            }
        }
    }

    /**
     * Load necessary files
     *
     * @return void
     */
    private static function loadFiles()
    {
        foreach (
            [
                '/ralouphie/getallheaders/src/getallheaders.php',
                '/symfony/polyfill-mbstring/bootstrap.php',
                '/symfony/polyfill-php80/bootstrap.php',
                '/guzzlehttp/guzzle/src/functions_include.php',
            ] as $file
        ) {
            require_once self::VENDOR_PATH . $file;
        }
    }

    /**
     * Return namespace mapping
     *
     * @return string[]
     */
    protected static function getNamespacesMapping()
    {
        // the order is important, it is necessary to insert the longest namespaces first
        return [
            self::ROOT_INSTALLER_NAMESPACE => GambDuplicaWP____PATH . '/installer/dup-installer/src/',
            self::ROOT_NAMESPACE           => GambDuplicaWP____PATH . '/src/',
        ];
    }

    /**
     * Return namespace mapping
     *
     * @return string[]
     */
    protected static function getNamespacesVendorMapping()
    {
        return [
            self::ROOT_VENDOR . 'Cron'                        => self::VENDOR_PATH . 'dragonmantank/cron-expression/src/Cron',
            self::ROOT_VENDOR . 'WpOrg\\Requests'             => self::VENDOR_PATH . 'rmccue/requests/src',
            self::ROOT_VENDOR . 'Amk\\JsonSerialize'          => self::VENDOR_PATH . 'andreamk/jsonserialize/src/',
            self::ROOT_VENDOR . 'ParagonIE\\ConstantTime'     => self::VENDOR_PATH . 'paragonie/constant_time_encoding/src/',
            self::ROOT_VENDOR . 'phpseclib3'                  => self::VENDOR_PATH . 'phpseclib/phpseclib/phpseclib/',
            self::ROOT_VENDOR . 'ForceUTF8'                   => self::VENDOR_PATH . 'neitanod/forceutf8/src/ForceUTF8/',
            self::ROOT_VENDOR . 'Symfony\\Polyfill\\Mbstring' => self::VENDOR_PATH . 'symfony/polyfill-mbstring',
            self::ROOT_VENDOR . 'Symfony\\Polyfill\\Php80'    => self::VENDOR_PATH . 'symfony/polyfill-php80',
            self::ROOT_VENDOR . 'Psr\\Http\\Message'          => self::VENDOR_PATH . 'psr/http-message/src',
            self::ROOT_VENDOR . 'Psr\\Http\\Client'           => self::VENDOR_PATH . 'psr/http-client/src',
            self::ROOT_VENDOR . 'Psr\\Log'                    => self::VENDOR_PATH . 'psr/log/Psr/Log',
            self::ROOT_VENDOR . 'Psr\\Cache'                  => self::VENDOR_PATH . 'psr/cache/src',
            self::ROOT_VENDOR . 'GuzzleHttp\\Promise'         => self::VENDOR_PATH . 'guzzlehttp/promises/src',
            self::ROOT_VENDOR . 'GuzzleHttp\\Psr7'            => self::VENDOR_PATH . 'guzzlehttp/psr7/src',
            self::ROOT_VENDOR . 'GuzzleHttp'                  => self::VENDOR_PATH . 'guzzlehttp/guzzle/src',
        ];
    }

    /**
     * Mappgin of some legacy classes
     *
     * @return array<string, string>
     */
    protected static function customLegacyMapping()
    {
        return [
            'gamb_duplica_wp_u'                       => GambDuplicaWP____PATH . '/classes/utilities/class.u.php',
            'gamb_duplica_wp_str'                     => GambDuplicaWP____PATH . '/classes/utilities/class.u.string.php',
            'gamb_duplica_wp_date'                    => GambDuplicaWP____PATH . '/classes/utilities/class.u.date.php',
            'gamb_duplica_wp_zip_u'                   => GambDuplicaWP____PATH . '/classes/utilities/class.u.zip.php',
            'gamb_duplica_wp_validator'               => GambDuplicaWP____PATH . '/classes/utilities/class.u.validator.php',
            'gamb_duplica_wp_tree_files'              => GambDuplicaWP____PATH . '/classes/utilities/class.u.tree.files.php',
            'gamb_duplica_wp_mu'                      => GambDuplicaWP____PATH . '/classes/utilities/class.u.multisite.php',
            'gamb_duplica_wp_global_entity'           => GambDuplicaWP____PATH . '/classes/entities/class.global.entity.php',
            'gamb_duplica_wp_package_template_entity' => GambDuplicaWP____PATH . '/classes/entities/class.package.template.entity.php',
            'gamb_duplica_wp_schedule_entity'         => GambDuplicaWP____PATH . '/classes/entities/class.schedule.entity.php',
            'gamb_duplica_wp_schedule_repeat_types'   => GambDuplicaWP____PATH . '/classes/entities/class.schedule.entity.php',
            'gamb_duplica_wp_schedule_days'           => GambDuplicaWP____PATH . '/classes/entities/class.schedule.entity.php',
            'gamb_duplica_wp_secure_global_entity'    => GambDuplicaWP____PATH . '/classes/entities/class.secure.global.entity.php',
            'gamb_duplica_wp_storage_entity'          => GambDuplicaWP____PATH . '/classes/entities/class.storage.entity.php',
            'gamb_duplica_wp_package_runner'          => GambDuplicaWP____PATH . '/classes/package/class.pack.runner.php',
            'gamb_duplica_wp_package'                 => GambDuplicaWP____PATH . '/classes/package/class.pack.php',
            'gamb_duplica_wp_packagetype'             => GambDuplicaWP____PATH . '/classes/package/class.pack.php',
            'gamb_duplica_wp_archive'                 => GambDuplicaWP____PATH . '/classes/package/class.pack.archive.php',
            'gamb_duplica_wp_database'                => GambDuplicaWP____PATH . '/classes/package/class.pack.database.php',
            'gamb_duplica_wp_installer'               => GambDuplicaWP____PATH . '/classes/package/class.pack.installer.php',
            'gamb_duplica_wp_custom_host_manager'     => GambDuplicaWP____PATH . '/classes/host/class.custom.host.manager.php',
            'gamb_duplica_wp_ui_viewstate'            => GambDuplicaWP____PATH . '/classes/ui/class.ui.viewstate.php',
            'gamb_duplica_wp_ui_dialog'               => GambDuplicaWP____PATH . '/classes/ui/class.ui.dialog.php',
            'gamb_duplica_wp_ui_messages'             => GambDuplicaWP____PATH . '/classes/ui/class.ui.messages.php',
            'gamb_duplica_wp_php_log'                 => GambDuplicaWP____PATH . '/classes/class.php.log.php',
            'gamb_duplica_wp_constants'               => GambDuplicaWP____PATH . '/classes/class.constants.php',
            'gamb_duplica_wp_db'                      => GambDuplicaWP____PATH . '/classes/class.db.php',
            'gamb_duplica_wp_log'                     => GambDuplicaWP____PATH . '/classes/class.logging.php',
            'gamb_duplica_wp_handler'                 => GambDuplicaWP____PATH . '/classes/class.logging.php',
            'gamb_duplica_wp_server'                  => GambDuplicaWP____PATH . '/classes/class.server.php',
            'gamb_duplica_wp_package_pagination'      => GambDuplicaWP____PATH . '/classes/class.package.pagination.php',
            'gamb_duplica_wp_scanvalidator'           => GambDuplicaWP____PATH . '/classes/class.scan.check.php',
        ];
    }
}
