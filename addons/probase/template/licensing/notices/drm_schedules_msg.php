<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Addons\ProBase\License\License;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */
$daysLeft = $tplData['schedule_disalbe_days_left'];

if ($daysLeft === false) {
    return;
}

if ($daysLeft >= 0) {
    ?>
    <u>
        <?php
        if (License::can(License::CAPABILITY_SCHEDULE)) {
            $message  = sprintf(
                _n(
                    'Scheduled Backups are going to be disabled <b><em>in %d day</em></b>.',
                    'Scheduled Backups are going to be disabled <b><em>in %d days</em></b>.',
                    $daysLeft,
                    'gamb-duplica-wp'
                ),
                $daysLeft
            );
            $message .= __(' Please renew your license to assure your backups are not interrupted.', 'gamb-duplica-wp');
            echo wp_kses(
                $message,
                [
                    'b'  => [],
                    'em' => [],
                ]
            );
        } else {
            esc_html_e(
                'All automatic backups have been disabeld. Please renew your license to re-enable them.',
                'gamb-duplica-wp'
            );
        }
        ?>
    </u>
    <?php
} else {
    esc_html_e(
        'Scheduled Backups.',
        'gamb-duplica-wp'
    );
}
