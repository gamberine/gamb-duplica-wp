<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Views\ViewHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<ul class="dup-status-icons-list no-bullet" >
    <li>
        <span class="icon-wrapper" ><i class="fa-solid fa-hand" ></i></span>
        <?php esc_html_e('Manual Backup', 'gamb-duplica-wp'); ?>
    </li>
    <li>
        <span class="icon-wrapper" ><i class="fa-solid fa-clock" ></i></span>
        <?php esc_attr_e('Schedule Backup', 'gamb-duplica-wp') ?>
    </li>
    <li>
        <span class="icon-wrapper" ><i class="fa-solid fa-hard-drive"></i></span>
        <?php
        echo wp_kses(
            __('The Backup is in a Local Storage <b>[clickable]</b>', 'gamb-duplica-wp'),
            ViewHelper::GEN_KSES_TAGS
        );
        ?>
    </li>
    <li>
        <span class="icon-wrapper" ><i class="fa-solid fa-cloud"></i></span>
        <?php
        echo wp_kses(
            __('The Backup is in a Remote Storage <b>[clickable]</b>', 'gamb-duplica-wp'),
            ViewHelper::GEN_KSES_TAGS
        );
        ?>
    </li>
    <li>
        <span class="icon-wrapper" ><i class="fa-solid fa-database"></i></span>
    <?php esc_attr_e('Database Only Backup', 'gamb-duplica-wp') ?>
    </li>
    <li>
        <span class="icon-wrapper" ><i class="fa-solid fa-images"></i></span>
        <?php esc_attr_e('Media Only Backup', 'gamb-duplica-wp') ?>
    </li>
    <li>
        <span class="icon-wrapper" ><?php ViewHelper::disasterIcon(true, 'link-style no-decoration'); ?></span>
        <?php
        echo wp_kses(
            __('This Backup is available for Disaster Recovery <b>[clickable]</b>', 'gamb-duplica-wp'),
            ViewHelper::GEN_KSES_TAGS
        );
        ?>
    </li>
    <li>
        <span class="icon-wrapper" ><?php ViewHelper::disasterIcon(true, 'green'); ?></span>
        <?php
        echo wp_kses(
            __('Disaster Recovery URL is set on this Backup <b>[clickable]</b>', 'gamb-duplica-wp'),
            ViewHelper::GEN_KSES_TAGS
        );
        ?>
    </li>
    <li>
        <span class="icon-wrapper" ><i class="fa-solid fa-clock-rotate-left maroon"></i></span>
        <?php esc_attr_e('This Backup is created after the Last Restored Backup', 'gamb-duplica-wp') ?>
    </li>
</ul>