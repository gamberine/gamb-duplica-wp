<?php

/**
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
<div class="wrap">
    <h1>
        <?php esc_html_e("Install Backup error", 'gamb-duplica-wp'); ?>
    </h1>
    <p>
        <?php esc_html_e("Error on Backup prepare, please go back and try again.", 'gamb-duplica-wp'); ?>
    </p>
</div>
