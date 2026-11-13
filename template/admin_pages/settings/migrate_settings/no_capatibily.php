<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

use GambDuplicaWP\Addons\ProBase\License\License;
use GambDuplicaWP\Views\ViewHelper;

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<div class="width-large" >
    <p>
        <?php esc_html_e(
            "The migrate settings screen allows you to import or export gamb-duplica-wp Pro settings from one site to another.",
            'gamb-duplica-wp'
        ); ?>
    </p>
    <p>
        <?php esc_html_e(
            "For example, if you have several storage locations that you use on multiple WordPress sites such as Google Drive or 
            Dropbox and you simply want to copy the profiles from this instance of gamb-duplica-wp Pro to another instance then simply 
            export the data here and import it on the other instance of gamb-duplica-wp Pro.",
            'gamb-duplica-wp'
        ); ?>
    </p>
        <p>
        <?php
        echo wp_kses(
            sprintf(
                __(
                    'This option isn\'t available at the <b>%1$s</b> license level.',
                    'gamb-duplica-wp'
                ),
                esc_html(License::getLicenseToString())
            ),
            ViewHelper::GEN_KSES_TAGS
        );
        ?>
        <b>
        <?php
        echo wp_kses(
            sprintf(
                _x(
                    'To enable this option %1$supgrade%2$s the License.',
                    '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                    'gamb-duplica-wp'
                ),
                '<a href="' . esc_url(License::getUpsellURL()) . '" target="_blank">',
                '</a>'
            ),
            ViewHelper::GEN_KSES_TAGS
        );
        ?>
        </b>
    </p>
</div>