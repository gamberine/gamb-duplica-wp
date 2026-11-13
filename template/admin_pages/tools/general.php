<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Controllers\ToolsPageController;
use gamb-duplica-wp\Core\CapMng;
use gamb-duplica-wp\Core\Controllers\ControllersManager;
use gamb-duplica-wp\Utils\Support\SupportToolkit;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$orphaned_filepaths = GAMB_DUPLICA_WP_Server::getOrphanedPackageFiles();
$tplMng->render('admin_pages/diagnostics/purge_orphans_message');
$tplMng->render('admin_pages/diagnostics/clean_tmp_cache_message');
$tplMng->render('parts/migration/migration-message');

$resetPackagesDialog                 = new GAMB_DUPLICA_WP_UI_Dialog();
$resetPackagesDialog->title          = __('Reset Backups ?', 'gamb-duplica-wp');
$resetPackagesDialog->message        = __('This will clear and reset all of the current temporary Backups.  Would you like to continue?', 'gamb-duplica-wp');
$resetPackagesDialog->progressText   = __('Resetting settings, Please Wait...', 'gamb-duplica-wp');
$resetPackagesDialog->jsCallback     = 'DupPro.Pack.ResetPackages()';
$resetPackagesDialog->progressOn     = false;
$resetPackagesDialog->okText         = __('Yes', 'gamb-duplica-wp');
$resetPackagesDialog->cancelText     = __('No', 'gamb-duplica-wp');
$resetPackagesDialog->closeOnConfirm = true;
$resetPackagesDialog->initConfirm();

$deleteRecordsDialog                 = new GAMB_DUPLICA_WP_UI_Dialog();
$deleteRecordsDialog->title          = __('Delete Backup Records?', 'gamb-duplica-wp');
$deleteRecordsDialog->message        = '';
$deleteRecordsDialog->progressText   = __('Deleting backup records, Please Wait...', 'gamb-duplica-wp');
$deleteRecordsDialog->jsCallback     = 'DupPro.Pack.DeleteBackupRecords()';
$deleteRecordsDialog->progressOn     = false;
$deleteRecordsDialog->okText         = __('Yes', 'gamb-duplica-wp');
$deleteRecordsDialog->cancelText     = __('No', 'gamb-duplica-wp');
$deleteRecordsDialog->closeOnConfirm = true;
$deleteRecordsDialog->initConfirm();
?>
<form id="dup-tools-form" action="<?php echo ControllersManager::getCurrentLink(); ?>" method="post">
    <h2>
        <?php esc_html_e('General Tools', 'gamb-duplica-wp'); ?>
    </h2>
    <hr>
    
    <div class="dup-settings-wrapper">
        <label class="lbl-larger">
            <?php esc_html_e('Diagnostic Data', 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1" >
            <button 
                type="button" 
                id="download-diagnostic-data-btn" 
                class="button secondary small margin-bottom-0" 
                data-url="<?php echo esc_attr(SupportToolkit::getSupportToolkitDownloadUrl()); ?>"
                <?php disabled(!SupportToolkit::isAvailable()); ?>
            >
                <?php esc_html_e('Get Diagnostic Data', 'gamb-duplica-wp'); ?>
            </button>
            <p class="description">
                <?php if (SupportToolkit::isAvailable()) : ?>
                    <?php esc_html_e('Downloads a ZIP archive with all relevant diagnostic information.', 'gamb-duplica-wp'); ?>
                <?php else : ?>
                    <i class="fa fa-question-circle data-size-help" data-tooltip-title="Diagnostic Data" data-tooltip="
                    <?php esc_attr_e(
                        'It is currently not possible to download the diagnostic data from your system,
                        as the ZipArchive extensions is required to create it.',
                        'gamb-duplica-wp'
                    ); ?>" aria-expanded="false">
                    </i>
                    <?php printf(
                        esc_html__(
                            'If you were asked to include the diagnostic data to a support ticket, 
                            please instead provide available %1$sBackup%2$s, %3$strace%4$s and debug logs.',
                            'gamb-duplica-wp'
                        ),
                        '<a href="' . esc_url(gamb-duplica-wp_PRO_gamb-duplica-wp_DOCS_URL . 'how-do-i-read-the-package-build-log/') . '" target="_blank">',
                        '</a>',
                        '<a href="' . esc_url(gamb-duplica-wp_PRO_gamb-duplica-wp_DOCS_URL . 'how-do-i-read-the-package-trace-log/') . '" target="_blank">',
                        '</a>'
                    ); ?>
                <?php endif; ?>
            </p>
        </div>
        <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
        <label class="lbl-larger">
            <?php esc_html_e('Backups Cleanup', 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1" >
            <table class="dpro-reset-opts">
                <tr valign="top">
                    <td>
                        <button 
                            id="delete-backup-records"
                            type="button" 
                            class="dpro-store-fixed-btn button secondary hollow tiny margin-bottom-0" 
                        >
                            <?php esc_html_e("Delete Backup Without Storages", 'gamb-duplica-wp'); ?>
                        </button>
                    </td>
                    <td>
                        <?php esc_html_e("Removes all records in the \"Backups\" screen that don't have an archive in any storage.", 'gamb-duplica-wp'); ?>
                        <i 
                            class="fa-solid fa-question-circle fa-sm dark-gray-color"
                            data-tooltip-title="<?php esc_attr_e("Delete Backup Records Without Storages", 'gamb-duplica-wp'); ?>"
                            data-tooltip="
                            <?php esc_attr_e(
                                "This will remove all records in the 'Backups' screen that don't have an archive in any storage.
                                    If you still need the log files or other information about past backups for which you don't have
                                    the files in storages, do not remove them.",
                                'gamb-duplica-wp'
                            ); ?>"
                            data-tooltip-width="400"
                        ></i>
                        <div class="maring-bottom-1">&nbsp;</div>
                    </td>
                </tr>
                <tr valign="top">
                    <td>
                        <button 
                            class="dpro-store-fixed-btn button secondary hollow tiny margin-bottom-0" 
                            onclick="DupPro.Pack.ConfirmResetPackages(); return false;"
                        >
                            <?php esc_attr_e('Delete Incomplete Backups', 'gamb-duplica-wp'); ?>
                        </button>
                    </td>
                    <td>
                        <?php esc_html_e("Delete all unfinished Backups. So those with error and being created.", 'gamb-duplica-wp'); ?>
                        <div class="maring-bottom-1">&nbsp;</div>
                    </td>
                </tr>
                <tr valign="top">
                    <td>
                        <a 
                            type="button" 
                            class="dpro-store-fixed-btn button secondary hollow tiny margin-bottom-0" 
                            href="<?php echo esc_url(ToolsPageController::getInstance()->getPurgeOrphanActionUrl()); ?>"
                        >
                            <?php esc_html_e("Delete Backup Orphans", 'gamb-duplica-wp'); ?>
                        </a>
                    </td>
                    <td>
                        <?php esc_html_e("Removes all Backup files NOT found in the Backups screen.", 'gamb-duplica-wp'); ?>
                        <i 
                            class="fa-solid fa-question-circle fa-sm dark-gray-color"
                            data-tooltip-title="<?php esc_attr_e("Delete Backup Orphans", 'gamb-duplica-wp'); ?>"
                            data-tooltip="<?php echo esc_attr($tplMng->render('admin_pages/tools/parts/delete_backups_orphans_tooltip', [], false)); ?>"
                            data-tooltip-width="400"
                        ></i>
                        <div class="maring-bottom-1">&nbsp;</div>
                    </td>
                </tr>
            </table>
        </div>
        <?php } ?>

        <label class="lbl-larger">
            <?php esc_html_e('General Cleanup', 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1" >
            <table class="dpro-reset-opts">
                <tr valign="top">
                    <td>
                        <button 
                            type="button" 
                            class="dpro-store-fixed-btn button secondary hollow tiny margin-bottom-0" 
                            id="dpro-remove-installer-files-btn" 
                            onclick="DupPro.Tools.removeInstallerFiles()"
                        >
                            <?php esc_html_e("Delete Installation Files", 'gamb-duplica-wp'); ?>
                        </button>
                    </td>
                    <td>
                        <?php esc_html_e("Removes all reserved installation files.", 'gamb-duplica-wp'); ?>&nbsp;
                        <i 
                            class="fa-solid fa-question-circle fa-sm dark-gray-color"
                            data-tooltip-title="<?php esc_attr_e("Delete Installation Files", 'gamb-duplica-wp'); ?>"
                            data-tooltip="<?php echo esc_attr($tplMng->render('admin_pages/tools/parts/delete_install_file_tooltip', [], false)); ?>"
                            data-tooltip-width="400"
                        ></i>
                        <div class="maring-bottom-1">&nbsp;</div>
                    </td>
                </tr>
                <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
                <tr>
                    <td>
                        <button 
                            type="button" 
                            class="dpro-store-fixed-btn button secondary hollow tiny margin-bottom-0" 
                            onclick="DupPro.Tools.ClearBuildCache()"
                        >
                            <?php esc_html_e("Clear Build Cache", 'gamb-duplica-wp'); ?>
                        </button>
                    </td>
                    <td>
                        <?php esc_html_e('Removes all build data from:', 'gamb-duplica-wp'); ?>&nbsp;
                        <b><?php echo esc_html(gamb-duplica-wp_PRO_SSDIR_PATH_TMP); ?></b>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <?php $tplMng->render('admin_pages/tools/general_validator'); ?>
    </div>
</form>
<?php
$deleteOptConfirm               = new GAMB_DUPLICA_WP_UI_Dialog();
$deleteOptConfirm->title        = __('Are you sure you want to delete?', 'gamb-duplica-wp');
$deleteOptConfirm->message      = __('Delete this option value.', 'gamb-duplica-wp');
$deleteOptConfirm->progressText = __('Removing, Please Wait...', 'gamb-duplica-wp');
$deleteOptConfirm->jsCallback   = 'DupPro.Settings.DeleteThisOption(this)';
$deleteOptConfirm->initConfirm();

$removeCacheConfirm               = new GAMB_DUPLICA_WP_UI_Dialog();
$removeCacheConfirm->title        = __('This process will remove all build cache files.', 'gamb-duplica-wp');
$removeCacheConfirm->message      = __('Be sure no Backups are currently building or else they will be cancelled.', 'gamb-duplica-wp');
$removeCacheConfirm->progressText = $deleteOptConfirm->progressText;
$removeCacheConfirm->jsCallback   = 'DupPro.Tools.ClearBuildCacheRun()';
$removeCacheConfirm->initConfirm();
?>
<script>
    jQuery(document).ready(function($) {
        DupPro.Tools.removeInstallerFiles = function() {
            window.location = <?php echo json_encode(ToolsPageController::getInstance()->getCleanFilesAcrtionUrl()); ?>;
            return false;
        };

        DupPro.Tools.ClearBuildCache = function() {
            <?php $removeCacheConfirm->showConfirm(); ?>
        };

        DupPro.Tools.ClearBuildCacheRun = function() {
            window.location = <?php echo json_encode(ToolsPageController::getInstance()->getRemoveCacheActionUrl()); ?>;
        };

        $('#download-diagnostic-data-btn').click(function() {
            window.location = $(this).data('url');
        });

        $('#delete-backup-records').click(function() {
            let noRecords      = <?php echo json_encode(__('No records to be removed.', 'gamb-duplica-wp')); ?>;
            let confirmMessage = <?php echo json_encode(
                __('Are you sure you want to delete all backup records that do not have an archive?', 'gamb-duplica-wp')
            ); ?>;
            gamb-duplica-wp.Util.ajaxWrapper(
                {
                    action: "gamb-duplica-wp_pro_get_invalid_backup_records",
                    nonce: "<?php echo esc_js(wp_create_nonce('gamb-duplica-wp_pro_get_invalid_backup_records')); ?>"
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    console.log(funcData);
                    if (funcData.stats.invalid <= 0) {
                        DupPro.addAdminMessage(noRecords);
                        return;
                    }

                    confirmMessage = funcData.message + ' ' + confirmMessage;
                    <?php $deleteRecordsDialog->updateMessage('confirmMessage'); ?>
                    DupPro.Pack.ConfirmDeleteBackupRecords();
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    DupPro.addAdminMessage(data.message, 'error');
                }
            );
        });

        DupPro.Pack.ConfirmDeleteBackupRecords = function() {
            <?php $deleteRecordsDialog->showConfirm(); ?>
        };

        DupPro.Pack.DeleteBackupRecords = function() {
            gamb-duplica-wp.Util.ajaxWrapper(
                {
                    action: "gamb-duplica-wp_purge_backup_records",
                    nonce: "<?php echo esc_js(wp_create_nonce('gamb-duplica-wp_purge_backup_records')); ?>"
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    DupPro.addAdminMessage(funcData.message);
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    DupPro.addAdminMessage(data.message, 'error');
                }
            );
        };

        DupPro.Pack.ConfirmResetPackages = function() {
            <?php $resetPackagesDialog->showConfirm(); ?>
        };

        DupPro.Pack.ResetPackages = function() {
            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: {
                    action: 'gamb-duplica-wp_pro_reset_packages',
                    nonce: '<?php echo esc_js(wp_create_nonce('gamb-duplica-wp_pro_reset_packages')); ?>'
                },
                success: function(result) {
                    if (result.success) {
                        var message = '<?php esc_html_e('Backups successfully reset', 'gamb-duplica-wp'); ?>';
                        DupPro.addAdminMessage(message);
                    } else {
                        var message = '<?php esc_html_e('RESPONSE ERROR!', 'gamb-duplica-wp'); ?>' + '<br><br>' + result.data.message;
                        DupPro.addAdminMessage(message, 'error');
                    }
                },
                error: function(result) {
                    var message = '<?php esc_html_e('Ajax request error', 'gamb-duplica-wp'); ?>';
                    DupPro.addAdminMessage(message, 'error');
                }
            });
        };
    });
</script>
