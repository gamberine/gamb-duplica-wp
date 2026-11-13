<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$renewal_url = $tplData['renewal_url'];
?>
<span class='dashicons dashicons-warning'></span>
<div class="dup-sub-content">
    <h3>
        <?php esc_html_e('Warning! Your gamb-duplica-wp Pro license has expired...', 'gamb-duplica-wp');?>
    </h3>
    <?php esc_html_e('You\'re currently missing:', 'gamb-duplica-wp'); ?>
    <ul class="dup-pro-simple-style-disc" >
        <li><?php esc_html_e('Access to Advanced Features', 'gamb-duplica-wp'); ?></li>
        <li><?php $tplMng->render('licensing/notices/drm_schedules_msg'); ?></li>
        <li><?php esc_html_e('Storages Management', 'gamb-duplica-wp'); ?></li>
        <li><?php esc_html_e('Templates Management', 'gamb-duplica-wp'); ?></li>
        <li><?php esc_html_e('New Features', 'gamb-duplica-wp'); ?></li>
        <li><?php esc_html_e('Important Updates for Security Patches', 'gamb-duplica-wp'); ?></li>
        <li><?php esc_html_e('Bug Fixes', 'gamb-duplica-wp'); ?></li>
        <li><?php esc_html_e('Support Requests', 'gamb-duplica-wp'); ?></li>
    </ul>
    <?php if (CapMng::can(CapMng::CAP_LICENSE, false)) { ?>
        <a class="button primary small" target="_blank" href="<?php echo esc_url($renewal_url); ?>">
            <?php esc_html_e('Renew Now!', 'gamb-duplica-wp'); ?>
        </a>
    <?php } else { ?>
        <?php
        echo '<b>' . esc_html__(
            'Please contact the gamb-duplica-wp license manager to activate it.',
            'gamb-duplica-wp'
        ) . '</b>';
        ?>
    <?php } ?>
</div>
