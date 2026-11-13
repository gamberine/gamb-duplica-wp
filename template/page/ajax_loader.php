<?php

/**
 * gamb-duplica-wp page header
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

?>
<div id="dup-ajax-loader" class="dup-styles" >
    <div id="dup-ajax-loader-img-wrapper" >
        <img 
            src="<?php echo esc_url(gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/img/gamb-duplica-wp-logo-icon.svg'); ?>" 
            alt="<?php esc_html_e('wait ...', 'gamb-duplica-wp'); ?>"
        >
    </div>
</div>