<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Core\MigrationMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

if (!isset($tplData['isMigrationSuccessNotice']) || !$tplData['isMigrationSuccessNotice']) {
    $tplMng->render('parts/migration/tool-cleanup-installer-files');
    return;
}

$safeMsg       = MigrationMng::getSaveModeWarning();
$cleanupReport = MigrationMng::getCleanupReport();

?>
<div class="notice notice-success dpro-admin-notice dpro-admin-notice dup-migration-pass-wrapper">
    <div class="dup-migration-pass-title">
        <i class="fa fa-check-circle"></i> <?php
        if (MigrationMng::getMigrationData()->restoreBackupMode) {
            esc_html_e('This site has been successfully restored!', 'gamb-duplica-wp');
        } else {
            esc_html_e('This site has been successfully migrated!', 'gamb-duplica-wp');
        }
        ?>
    </div>
    <p>
        <?php
        printf(
            esc_html__(
                'The following installation files are stored in the folder %s',
                'gamb-duplica-wp'
            ),
            '<b>' . esc_html(GambDuplicaWP_PRO_SSDIR_PATH_INSTALLER) . '</b>'
        );
        ?>
    </p>
    <ul class="dup-stored-minstallation-files">
        <?php foreach (MigrationMng::getStoredMigrationLists() as $path => $label) { ?>
            <li>
                <?php echo esc_html($label); ?>
            </li>
        <?php } ?>
    </ul>

    <?php
    if (isset($tplData['isInstallerCleanup']) && $tplData['isInstallerCleanup']) {
        $tplMng->render('parts/migration/clean-installation-files');
    } else {
        if (count($cleanupReport['instFile']) > 0) { ?>
            <p>
                <?php esc_html_e('Security actions:', 'gamb-duplica-wp'); ?>
            </p>
            <ul class="dup-stored-minstallation-files">
                <?php
                foreach ($cleanupReport['instFile'] as $html) { ?>
                    <li>
                        <?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
        <p>
            <b><?php esc_html_e('Final step:', 'gamb-duplica-wp'); ?></b><br>
            <span id="dpro-notice-action-remove-installer-files" class="link-style" onclick="DupPro.Tools.removeInstallerFiles();">
                <?php esc_html_e('Remove Installation Files Now!', 'gamb-duplica-wp'); ?>
            </span>
        </p>
        <?php if (strlen($safeMsg) > 0) { ?>
            <div class="notice-safemode">
                <?php echo esc_html($safeMsg); ?>
            </div>
        <?php } ?>

        <p class="sub-note">
            <i><?php
                esc_html_e(
                    'Note: This message will be removed after all installer files are removed.
                    Installer files must be removed to maintain a secure site.
                    Click the link above to remove all installer files and complete the migration.',
                    'gamb-duplica-wp'
                );
                ?><br>
                <i class="fas fa-info-circle"></i>
                <?php
                esc_html_e(
                    'If an archive.zip/daf file was intentially added to the root directory to perform an overwrite install 
                    of this site then you can ignore this message.',
                    'gamb-duplica-wp'
                )
                ?>
            </i>
        </p>
        <?php
    }

    echo apply_filters(MigrationMng::HOOK_BOTTOM_MIGRATION_MESSAGE, ''); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    ?>
</div>
