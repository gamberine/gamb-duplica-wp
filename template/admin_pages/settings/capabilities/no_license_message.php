<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Addons\ProBase\License\License;
use GambDuplicaWP\Addons\ProBase\Models\LicenseData;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

if (License::can(License::CAPABILITY_CAPABILITIES_MNG)) {
    return;
}
?>
<p id="dup-no-license-message" class="dup-border-left-red-notice" >
    <b>
        <?php
        esc_html_e(
            'The current license does not allow to manage the capabilities. ',
            'gamb-duplica-wp'
        );
        ?>
    </b>
    <br>
    <?php
    if (License::getLicenseStatus() === LicenseData::STATUS_VALID) {
        printf(
            esc_html_x(
                'If you want to manage the capabilities please %1$supgrade your license%2$s.',
                '1: <a> tag, 2: </a> tag',
                'gamb-duplica-wp'
            ),
            '<a href="' . esc_url(License::getUpsellURL()) . '" target="_blank">',
            '</a>'
        );
    } else {
        printf(
            esc_html_x(
                'If you want to manage the capabilities please %1$srenew your license%2$s.',
                '1: <a> tag, 2: </a> tag',
                'gamb-duplica-wp'
            ),
            '<a href="' . esc_url(License::getUpsellURL()) . '" target="_blank">',
            '</a>'
        );
    }
    ?>
    <br><br>
    <?php
    esc_html_e(
        'It\'s possible to reset the capabilities to the default values with "Reset to default" button.',
        'gamb-duplica-wp'
    );
    ?>

</p>