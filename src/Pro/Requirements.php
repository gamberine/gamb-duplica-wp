<?php

/**
 * Class that collects the functions of initial checks on the requirements to run the plugin
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Pro;

class Requirements implements \gamb-duplica-wp\Core\RequirementsInterface
{
    const DUP_LITE_PLUGIN_KEY = 'gamb-duplica-wp/gamb-duplica-wp.php';

    /**
     *
     * @var string // curent plugin file full path
     */
    protected static $pluginFile = '';

    /**
     *
     * @var string // message on deactivation
     */
    protected static $deactivationMessage = '';

    /**
     * This function checks the requirements to run gamb-duplica-wp.
     * At this point WordPress is not yet completely initialized so functionality is limited.
     * It need to hook into "admin_init" to get the full functionality of WordPress.
     *
     * @param string $pluginFile main plugin file path
     *
     * @return boolean           true if plugin can be exectued
     */
    public static function canRun($pluginFile)
    {
        $result           = true;
        self::$pluginFile = $pluginFile;

        if (self::isPluginActive(self::DUP_LITE_PLUGIN_KEY)) {
            add_action('admin_init', [self::class, 'addLiteEnableNotice']);
            $pluginUrl                 = (is_multisite() ? network_admin_url('plugins.php') : admin_url('plugins.php'));
            self::$deactivationMessage = sprintf(
                _x(
                    'Sorry, you cannot activate the gamb-duplica-wp PRO plugin while the gamb-duplica-wp LITE version is active. <br>
                    Please deactivate the gamb-duplica-wp LITE plugin, then reactivate the gamb-duplica-wp PRO Plugin from the %1$splugins page%2$s.',
                    '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                    'gamb-duplica-wp'
                ),
                '<a href="' . esc_url($pluginUrl) . '">',
                '</a>'
            );
            $result                    = false;
        }

        if ($result === false) {
            register_activation_hook($pluginFile, [self::class, 'deactivateOnActivation']);
        }

        return $result;
    }

    /**
     * Return plugin hash
     *
     * @return string
     */
    public static function getAddsHash(): string
    {
        //return '7b2272223a5b2250726f42617365222c224f74686572506c7567225d2c226664223a5b224c69746542617365225d7d';
        return '7b2272223a5b2250726f42617365225d2c226664223a5b224c69746542617365225d7d';
    }

    /**
     *
     * @param string $plugin plugin key
     *
     * @return boolean // return strue if plugin key is active and plugin file exists
     */
    protected static function isPluginActive($plugin)
    {
        $isActive = false;
        if (in_array($plugin, (array) get_option('active_plugins', []))) {
            $isActive = true;
        }

        if (is_multisite()) {
            $plugins = get_site_option('active_sitewide_plugins');
            if (isset($plugins[$plugin])) {
                $isActive = true;
            }
        }

        return ($isActive && file_exists(WP_PLUGIN_DIR . '/' . $plugin));
    }

    /**
     * Display admin notice only if user can manage plugins.
     *
     * @return void
     */
    public static function addLiteEnableNotice()
    {
        if (current_user_can('activate_plugins')) {
            add_action('admin_notices', [self::class, 'liteEnabledNotice']);
        }
    }

    /**
     * deactivate current plugin on activation
     *
     * @return void
     */
    public static function deactivateOnActivation()
    {
        deactivate_plugins(plugin_basename(self::$pluginFile));
        wp_die(
            wp_kses(
                self::$deactivationMessage,
                [
                    'br' => [],
                    'a'  => ['href' => []],
                ]
            )
        );
    }

    /**
     * diplay admin notice if gamb-duplica-wp pro is enabled
     *
     * @return void
     */
    public static function liteEnabledNotice()
    {
        $pluginUrl = (is_multisite() ? network_admin_url('plugins.php') : admin_url('plugins.php'));
        ?>
        <div class="error notice">
            <p>
                <span class="dashicons dashicons-warning"></span>
                <b><?php esc_html_e('gamb-duplica-wp Pro Notice:', 'gamb-duplica-wp'); ?></b>
                <?php esc_html_e('The "gamb-duplica-wp Lite" and "gamb-duplica-wp Pro" plugins cannot both be active at the same time.  ', 'gamb-duplica-wp'); ?>
            </p>
            <p>
                <?php
                    printf(
                        esc_html_x(
                            'To use "gamb-duplica-wp PRO" please deactivate "gamb-duplica-wp LITE" from the %1$splugins page%2$s.',
                            '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                            'gamb-duplica-wp'
                        ),
                        '<a href="' . esc_url($pluginUrl) . '">',
                        '</a>'
                    );
                ?>
            </p>
        </div>
        <?php
    }
}
