<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Utils\Help\Help;
use gamb-duplica-wp\Libs\Snap\SnapJson;

defined("ABSPATH") || exit;

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$helpPageUrl = SnapJson::jsonEncode(Help::getHelpPageUrl());
?>
<div id="dup-meta-screen" class="dup-styles"></div>
<div class="dup-header dup-styles">
    <img src="<?php echo esc_url(gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/img/gamb-duplica-wp-header-logo.svg'); ?>" alt="gamb-duplica-wp Logo" >
    <a class="dup-global-help" >
        <i class="fa fa-question-circle  fa-sm"></i> <?php esc_html_e('Help', 'gamb-duplica-wp'); ?>
    </a>
</div>
<script>
    jQuery(document).ready(function ($) {
        $('.dup-global-help').click(function () { 
            if (gamb-duplica-wp.Help.isDataLoaded()) {
                gamb-duplica-wp.Help.Display();
            } else {
                gamb-duplica-wp.Help.Load('<?php echo esc_url_raw($helpPageUrl); ?>');
            }
        });
    });
</script>
