<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<div class="scan-item ">
    <div class='title' onclick="DupPro.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Database only', 'gamb-duplica-wp'); ?></div>
        <div id="only-db-scan-status"><div class="badge badge-warn"><?php esc_html_e("Notice", 'gamb-duplica-wp'); ?></div></div>
    </div>
    <div class="info">
        <?php esc_html_e("Only the database and a copy of the installer.php will be included in the archive.zip file.", 'gamb-duplica-wp'); ?>
    </div>
</div>
