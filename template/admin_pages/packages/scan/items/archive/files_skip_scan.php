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
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('File checks skipped', 'gamb-duplica-wp'); ?></div>
        <div id="skip-archive-scan-status"><div class="badge badge-warn"><?php esc_html_e("Notice", 'gamb-duplica-wp'); ?></div></div>
    </div>
    <div class="info">
        <?php esc_html_e("All file checks are skipped. This could cause problems during extraction if problematic files are included.", 'gamb-duplica-wp'); ?>
        <br><br>
        <b><?php esc_html_e("To enable, uncheck Backups > Advanced Settings > Scan File Checks > \"Skip\" to enable.", 'gamb-duplica-wp'); ?></b>

    </div>
</div>
