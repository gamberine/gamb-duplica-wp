<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Controllers\SettingsPageController;
use GambDuplicaWP\Core\CapMng;
use GambDuplicaWP\Core\Controllers\ControllersManager;
use GambDuplicaWP\Views\ViewHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$img_url           = plugins_url('gamb-duplica-wp/assets/img/warning.png');
$problem_text      = $tplData['problem'];
$licensing_tab_url = ControllersManager::getMenuLink(
    ControllersManager::SETTINGS_SUBMENU_SLUG,
    SettingsPageController::L2_SLUG_GENERAL
);
?>
<span class='dashicons dashicons-warning'></span>
<div class="dup-sub-content">
    <h3>
        <?php
        printf(
            esc_html_x('Your gamb-duplica-wp Pro license key is %1$s ...', '%1$s represent the license status', 'gamb-duplica-wp'),
            esc_html($tplData['problem'])
        );
        ?>
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
                    '<b>Please %1$sActivate Your License%2$s</b>. If you do not have a license key go to %3$sgamb-duplica-wp.com%4$s to get it.',
                    '1 and 2 are opening and 3 and 4 are closing anchor tags (<a> and </a>)',
                    'gamb-duplica-wp'
                ),
                ViewHelper::GEN_KSES_TAGS
            ),
            '<a href="' . esc_url($licensing_tab_url) . '">',
            '</a>',
            '<a target="_blank" href="' . esc_url(GambDuplicaWP_PRO_BLOG_URL . 'my-account') . '">',
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
