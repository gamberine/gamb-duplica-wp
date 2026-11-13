<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\MuPlugin;

/**
 * Mu plugin generator
 */
class MuGenerator
{
    const NAME = 'gamb-duplica-wp-mu-plugin.php';

    /**
     * Create mu plugin
     *
     * @return bool true on success, fail on failure
     */
    public static function create()
    {
        if (!is_dir(WPMU_PLUGIN_DIR)) {
            if (wp_mkdir_p(WPMU_PLUGIN_DIR) === false) {
                return false;
            }
        }
        $muPluginFile = trailingslashit(WPMU_PLUGIN_DIR) . self::NAME;
        return (file_put_contents($muPluginFile, self::getPluginContent()) !== false);
    }

    /**
     * Remove/disable mu plugin
     *
     * @return bool true on success, false on failure
     */
    public static function remove()
    {
        $muPluginFile = trailingslashit(WPMU_PLUGIN_DIR) . self::NAME;
        if (file_exists($muPluginFile)) {
            return unlink($muPluginFile);
        }
        return true;
    }

    /**
     * Return mu plugin content
     *
     * @return string
     */
    protected static function getPluginContent(): string
    {
        $pluginVersion = GambDuplicaWP_PRO_VERSION;

        return <<<MUCONTENT
<?php
/**
 * Plugin Name: gamb-duplica-wp mu-plugin
 * Description: gamb-duplica-wp startup utiliy plugin.
 * Version    : {$pluginVersion}
 * Author     : Snap Creek
 * Author URI : http://snapcreek.com
 * License    : GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') || exit;
define('GambDuplicaWP_MU_PLUGIN_VERSION', '{$pluginVersion}');

use GambDuplicaWP\MuPlugin\MuBootstrap;

\$bootstrapFile = WP_PLUGIN_DIR . '/gamb-duplica-wp/src/MuPlugin/MuBootstrap.php';
if (file_exists(\$bootstrapFile)) {
    include(\$bootstrapFile);
    if (class_exists(MuBootstrap::class)) {
        MuBootstrap::init();
    }
}

MUCONTENT;
    }
}
