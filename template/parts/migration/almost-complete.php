<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Controllers\ToolsPageController;
use gamb-duplica-wp\Core\Controllers\ControllersManager;
use gamb-duplica-wp\Core\MigrationMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$safeMsg = MigrationMng::getSaveModeWarning();
$url     = $ctrlMng->getMenuLink(ControllersManager::TOOLS_SUBMENU_SLUG, ToolsPageController::L2_SLUG_GENERAL);
;

?>
<div class="notice notice-success dpro-admin-notice dpro-admin-notice dup-migration-pass-wrapper" >
    <p>
        <b><?php
        if (MigrationMng::getMigrationData()->restoreBackupMode) {
            esc_html_e('Restore Backup Almost Complete!', 'gamb-duplica-wp');
        } else {
            esc_html_e('Migration Almost Complete!', 'gamb-duplica-wp');
        }
        ?></b>
    </p>
    <p>
        <?php
        esc_html_e(
            'Reserved gamb-duplica-wp Pro installation files have been detected in the root directory. 
            Please delete these installation files to avoid security issues.',
            'gamb-duplica-wp'
        );
        ?>
        <br/>
        <?php esc_html_e('Go to: Tools > General > Data Cleanup and click the "Delete Installation Files" button', 'gamb-duplica-wp'); ?><br>
        <a id="dpro-notice-action-general-site-page" href="<?php echo esc_url($url); ?>">
            <?php esc_html_e('Take me there now!', 'gamb-duplica-wp'); ?>
        </a>
    </p>
    <?php if (strlen($safeMsg) > 0) { ?>
        <div class="notice-safemode">
            <?php echo esc_html($safeMsg); ?>
        </div>
    <?php } ?>
    <p class="sub-note">
        <i><?php
            esc_html_e(
                'If an archive.zip/daf file was intentially added to the root directory 
                to perform an overwrite install of this site then you can ignore this message.',
                'gamb-duplica-wp'
            );
            ?>
        </i>
    </p>

    <?php echo apply_filters(MigrationMng::HOOK_BOTTOM_MIGRATION_MESSAGE, ''); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
