<?php

/**
 * Auloader calsses
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Utils;

/**
 * Autoloader calss, dont user gamb-duplica-wp library here
 */
final class Autoloader extends AbstractAutoloader
{
    const VENDOR_PATH = gamb-duplica-wp____PATH . '/vendor-prefixed/';

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
            self::ROOT_INSTALLER_NAMESPACE => gamb-duplica-wp____PATH . '/installer/dup-installer/src/',
            self::ROOT_NAMESPACE           => gamb-duplica-wp____PATH . '/src/',
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
            'dup_pro_u'                       => gamb-duplica-wp____PATH . '/classes/utilities/class.u.php',
            'dup_pro_str'                     => gamb-duplica-wp____PATH . '/classes/utilities/class.u.string.php',
            'dup_pro_date'                    => gamb-duplica-wp____PATH . '/classes/utilities/class.u.date.php',
            'dup_pro_zip_u'                   => gamb-duplica-wp____PATH . '/classes/utilities/class.u.zip.php',
            'dup_pro_validator'               => gamb-duplica-wp____PATH . '/classes/utilities/class.u.validator.php',
            'dup_pro_tree_files'              => gamb-duplica-wp____PATH . '/classes/utilities/class.u.tree.files.php',
            'dup_pro_mu'                      => gamb-duplica-wp____PATH . '/classes/utilities/class.u.multisite.php',
            'dup_pro_global_entity'           => gamb-duplica-wp____PATH . '/classes/entities/class.global.entity.php',
            'dup_pro_package_template_entity' => gamb-duplica-wp____PATH . '/classes/entities/class.package.template.entity.php',
            'dup_pro_schedule_entity'         => gamb-duplica-wp____PATH . '/classes/entities/class.schedule.entity.php',
            'dup_pro_schedule_repeat_types'   => gamb-duplica-wp____PATH . '/classes/entities/class.schedule.entity.php',
            'dup_pro_schedule_days'           => gamb-duplica-wp____PATH . '/classes/entities/class.schedule.entity.php',
            'dup_pro_secure_global_entity'    => gamb-duplica-wp____PATH . '/classes/entities/class.secure.global.entity.php',
            'dup_pro_storage_entity'          => gamb-duplica-wp____PATH . '/classes/entities/class.storage.entity.php',
            'dup_pro_package_runner'          => gamb-duplica-wp____PATH . '/classes/package/class.pack.runner.php',
            'dup_pro_package'                 => gamb-duplica-wp____PATH . '/classes/package/class.pack.php',
            'dup_pro_packagetype'             => gamb-duplica-wp____PATH . '/classes/package/class.pack.php',
            'dup_pro_archive'                 => gamb-duplica-wp____PATH . '/classes/package/class.pack.archive.php',
            'dup_pro_database'                => gamb-duplica-wp____PATH . '/classes/package/class.pack.database.php',
            'dup_pro_installer'               => gamb-duplica-wp____PATH . '/classes/package/class.pack.installer.php',
            'dup_pro_custom_host_manager'     => gamb-duplica-wp____PATH . '/classes/host/class.custom.host.manager.php',
            'dup_pro_ui_viewstate'            => gamb-duplica-wp____PATH . '/classes/ui/class.ui.viewstate.php',
            'dup_pro_ui_dialog'               => gamb-duplica-wp____PATH . '/classes/ui/class.ui.dialog.php',
            'dup_pro_ui_messages'             => gamb-duplica-wp____PATH . '/classes/ui/class.ui.messages.php',
            'dup_pro_php_log'                 => gamb-duplica-wp____PATH . '/classes/class.php.log.php',
            'dup_pro_constants'               => gamb-duplica-wp____PATH . '/classes/class.constants.php',
            'dup_pro_db'                      => gamb-duplica-wp____PATH . '/classes/class.db.php',
            'dup_pro_log'                     => gamb-duplica-wp____PATH . '/classes/class.logging.php',
            'dup_pro_handler'                 => gamb-duplica-wp____PATH . '/classes/class.logging.php',
            'dup_pro_server'                  => gamb-duplica-wp____PATH . '/classes/class.server.php',
            'dup_pro_package_pagination'      => gamb-duplica-wp____PATH . '/classes/class.package.pagination.php',
            'dup_pro_scanvalidator'           => gamb-duplica-wp____PATH . '/classes/class.scan.check.php',
        ];
    }
}
