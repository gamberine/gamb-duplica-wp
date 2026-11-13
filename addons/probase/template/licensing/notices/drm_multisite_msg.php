<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Addons\ProBase\License\License;
use GambDuplicaWP\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$upgradeUrl = License::getUpsellURL();
?>
<span class='dashicons dashicons-warning'></span>
<div class="dup-sub-content">
    <h3>
        <?php esc_html_e('Your license does not support multisite functionality', 'gamb-duplica-wp'); ?>
    </h3>
    <p>
        <?php echo esc_html(
            sprintf(
                _x(
                    'By upgrading to the %1$s or %2$s plans you will unlock the ability to create 
                    backups and do advanced migrations on multi-site installations!',
                    '1: name of pro plan, 2: name of elite plan',
                    'gamb-duplica-wp'
                ),
                License::getLicenseToString(License::TYPE_PRO),
                License::getLicenseToString(License::TYPE_ELITE)
            )
        ); ?>
    </p>
    <br>
    <?php if (CapMng::can(CapMng::CAP_LICENSE, false)) { ?>
        <a class="button primary small margin-bottom-0" target="_blank" href="<?php echo esc_url($upgradeUrl); ?>">
            <?php esc_html_e('Upgrade Now!', 'gamb-duplica-wp'); ?>
        </a>
    <?php } else { ?>
        <?php
        echo '<b>' . esc_html__(
            'Please contact the gamb-duplica-wp license manager to update it.',
            'gamb-duplica-wp'
        ) . '</b>';
        ?>
    <?php } ?>
</div>