<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Addons\ProBase\LicensingController;
use gamb-duplica-wp\Controllers\SettingsPageController;
use gamb-duplica-wp\Core\CapMng;
use gamb-duplica-wp\Core\Controllers\ControllersManager;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$licensing_tab_url = ControllersManager::getMenuLink(
    ControllersManager::SETTINGS_SUBMENU_SLUG,
    SettingsPageController::L2_SLUG_GENERAL
);
$dashboard_url     = gamb-duplica-wp_PRO_BLOG_URL . 'my-account';
$img_url           = plugins_url('gamb-duplica-wp/assets/img/warning.png');

?>
<span class='dashicons dashicons-warning'></span>
<div class="dup-sub-content">
    <h3>
        <?php esc_html_e('gamb-duplica-wp Pro\'s license is deactivated because you\'re out of site activations.', 'gamb-duplica-wp'); ?>
    </h3>
    <?php esc_html_e('You\'re currently missing:', 'gamb-duplica-wp'); ?>
    <ul class="dup-pro-simple-style-disc" >
        <li><?php esc_html_e('Access to Advanced Features', 'gamb-duplica-wp'); ?></li>
        <li><?php esc_html_e('Scheduled Backups', 'gamb-duplica-wp'); ?></li>
        <li><?php esc_html_e('Storages Management', 'gamb-duplica-wp'); ?></li>
        <li><?php esc_html_e('Templates Management', 'gamb-duplica-wp'); ?></li>
        <li><?php esc_html_e('New Features', 'gamb-duplica-wp'); ?></li>
        <li><?php esc_html_e('Important Updates for Security Patches', 'gamb-duplica-wp'); ?></li>
        <li><?php esc_html_e('Bug Fixes', 'gamb-duplica-wp'); ?></li>
        <li><?php esc_html_e('Support Requests', 'gamb-duplica-wp'); ?></li>
    </ul>
    <?php
    if (CapMng::can(CapMng::CAP_LICENSE, false)) {
        printf(
            wp_kses(
                _x(
                    'Upgrade your license using the %1$sgamb-duplica-wp Dashboard%2$s or deactivate plugin on old sites.<br/>
                    After making necessary changes %3$srefresh the license status%4$s.',
                    '1 and 2 are opening and 3 and 4 are closing anchor tags (<a> and </a>)',
                    'gamb-duplica-wp'
                ),
                [
                    'br' => [],
                ]
            ),
            '<a href="' . esc_url($dashboard_url) . '" target="_blank">',
            '</a>',
            '<a href="' . esc_url($licensing_tab_url) . '">',
            '</a>'
        );
    } else {
        echo '<b>' . esc_html__(
            'Please contact the gamb-duplica-wp license manager to activate it.',
            'gamb-duplica-wp'
        ) . '</b>';
    }
    ?>
</div>