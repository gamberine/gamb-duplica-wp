<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

use gamb-duplica-wp\Addons\ProBase\License\License;
use gamb-duplica-wp\Views\ViewHelper;

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

if (License::can(License::CAPABILITY_BRAND)) {
    return;
}
?>
<div class="width-xlarge" >
    <?php
    esc_html_e(
        "Create your own WordPress distribution by adding a custom name and logo to the installer! 
        Installer branding lets you create multiple brands for your installers and then choose 
        which one you want when the Backup is built (example shown below).",
        'gamb-duplica-wp'
    );
    ?>
    <br/><br/>
    <?php
        printf(
            wp_kses(
                __(
                    'This option isn\'t available at the <b>%1$s</b> license level.',
                    'gamb-duplica-wp'
                ),
                ViewHelper::GEN_KSES_TAGS
            ),
            esc_html(License::getLicenseToString())
        );
        ?>
    <b>
    <?php
        printf(
            esc_html_x(
                'To enable this option %1$supgrade%2$s the License.',
                '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                'gamb-duplica-wp'
            ),
            '<a href="' . esc_url(License::getUpsellURL()) . '" target="_blank">',
            '</a>'
        );
        ?>
    </b>
</div>

<div style="border:0px solid #999; padding: 5px; margin: 5px; border-radius: 5px; width:700px">
    <img src="<?php echo esc_attr(gamb-duplica-wp_PRO_IMG_URL . '/dpro-brand.png'); ?>" >
</div>
