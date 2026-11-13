<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Views\ViewHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<p>
    <?php
    echo wp_kses(
        __(
            '<b>CURL isn\'t enabled.</b> This module is far more reliable for remote communication.',
            'gamb-duplica-wp'
        ),
        ViewHelper::GEN_KSES_TAGS
    );
    ?>
    </br>
    <?php esc_html_e('A possible solution to the problem could be to activate it.', 'gamb-duplica-wp'); ?>
    </br>
    <?php
        printf(
            wp_kses(
                _x(
                    'For detailed steps on how to enable cURL please see <b>Solution 3, Issue A</b> in %1$sthis FAQ Entry%2$s.',
                    '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                    'gamb-duplica-wp'
                ),
                ViewHelper::GEN_KSES_TAGS
            ),
            '<a href="' . esc_url(gamb-duplica-wp_PRO_gamb-duplica-wp_DOCS_URL . 'how-to-resolve-license-activation-issues/') . '" target="_blank">',
            '</a>'
        );
        ?>
    <br/>
</p>
