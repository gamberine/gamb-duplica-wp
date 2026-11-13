<?php

/**
 * gamb-duplica-wp Pro remote download scripts
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2024, Snap Creek LLC
 */

use GambDuplicaWP\Controllers\PackagesPageController;
use GambDuplicaWP\Models\Storages\AbstractStorageEntity;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$afterDownloadAction      = $tplData['afterDownloadAction'] ?? '';
$remoteDownloadPackageId  = isset($tplData['remoteDownloadPackageId']) ? (int) $tplData['remoteDownloadPackageId'] : -1;
$downloadStarted          = $remoteDownloadPackageId > 0;
$afterDownloadActionNonce = strlen($afterDownloadAction) > 0 ? PackagesPageController::getInstance()->getActionByKey($afterDownloadAction)->getNonce() : '';

$alreadyDownloadDlg              = new GAMB_DUPLICA_WP_UI_Dialog();
$alreadyDownloadDlg->width       = 550;
$alreadyDownloadDlg->height      = 200;
$alreadyDownloadDlg->showButtons = true;
$alreadyDownloadDlg->title       = __('Backup is already being downloaded', 'gamb-duplica-wp');
$alreadyDownloadDlg->message     = __('The backup is currently being downloaded. Please wait for the download to finish.', 'gamb-duplica-wp');
$alreadyDownloadDlg->boxClass    = 'duplication-remote-download-options-dlg';
$alreadyDownloadDlg->initAlert();

$remoteDownloadOptionsDlg              = new GAMB_DUPLICA_WP_UI_Dialog();
$remoteDownloadOptionsDlg->width       = 750;
$remoteDownloadOptionsDlg->height      = 395;
$remoteDownloadOptionsDlg->showButtons = false;
$remoteDownloadOptionsDlg->title       = __('Download From Remote Storage', 'gamb-duplica-wp');
$remoteDownloadOptionsDlg->message     = __('Loading Please Wait...', 'gamb-duplica-wp');
$remoteDownloadOptionsDlg->boxClass    = 'duplicatior-remote-download-options-dlg';
$remoteDownloadOptionsDlg->initAlert();

$downloadProgressDlg               = new GAMB_DUPLICA_WP_UI_Dialog();
$downloadProgressDlg->height       = 475;
$downloadProgressDlg->width        = 750;
$downloadProgressDlg->showButtons  = false;
$downloadProgressDlg->title        = __('Downloading Backup...', 'gamb-duplica-wp');
$downloadProgressDlg->templatePath = 'admin_pages/packages/remote_download/download_progress';
$downloadProgressDlg->boxClass     = 'gamb-duplica-wp-download-progress-dlg';
$downloadProgressDlg->initAlert();

$removeBackupRecordConfirm                 = new GAMB_DUPLICA_WP_UI_Dialog();
$removeBackupRecordConfirm->title          = __('Remove Backup Record?', 'gamb-duplica-wp');
$removeBackupRecordConfirm->message        = __('The backup does not exist in any storage. Would you like to remove the backup record?', 'gamb-duplica-wp');
$removeBackupRecordConfirm->progressText   = __('Removing Backup Record, Please Wait...', 'gamb-duplica-wp');
$removeBackupRecordConfirm->jsCallback     = 'DupPro.Pack.DeleteBackupRecord(this)';
$removeBackupRecordConfirm->progressOn     = false;
$removeBackupRecordConfirm->okText         = __('Yes', 'gamb-duplica-wp');
$removeBackupRecordConfirm->cancelText     = __('No', 'gamb-duplica-wp');
$removeBackupRecordConfirm->closeOnConfirm = true;
$removeBackupRecordConfirm->initConfirm();
?>

<script>
jQuery(document).ready(function ($) {
    let remoteDownloadModal       = $('.<?php echo esc_html($remoteDownloadOptionsDlg->boxClass); ?>');
    let remoteDownloadInProgress  = <?php echo wp_json_encode($downloadStarted); ?>;
    let remoteDownloadModalOpen   = false;

    if (remoteDownloadInProgress) {
        setTimeout(function() {
            <?php $downloadProgressDlg->showAlert(); ?>
            remoteDownloadModalOpen = true;
        }, 500);
    }

    $(document).on('thickbox:removed', function() {
        if (!remoteDownloadInProgress && !remoteDownloadModalOpen) {
            return;
        }

        remoteDownloadModalOpen = false;
    });

    DupPro.Pack.DeleteBackupRecord = function(e) {
        var id = $(e).attr('data-id');
        $("tr[data-package-id=" + id + "] input[type=checkbox]").prop('checked', true);
        DupPro.Pack.Delete()
    }

    DupPro.Pack.IsRemoteDownloadModalOpen = function() {
        return remoteDownloadModalOpen;
    }

    DupPro.Pack.afterRemoteDownloadAction = function() {
        let packageId = <?php echo wp_json_encode($remoteDownloadPackageId); ?>;
        let action    = <?php echo wp_json_encode($afterDownloadAction); ?>;
        let nonceVal  = <?php echo wp_json_encode($afterDownloadActionNonce); ?>;

        if (action.length === 0 || nonceVal.length === 0 || packageId <= 0) {
            location.reload();
        } else {
            GambDuplicaWP.Util.dynamicFormSubmit('', 'post', {
                packageId: packageId,
                action: action,
                _wpnonce: nonceVal
            });
        }
    }

    /**
     * Show remote download options in modal window
     * 
     * @param {number} packageId
     * @param {string} remoteAction
     * 
     * @return {boolean}
     */
    DupPro.Pack.ShowRemoteDownloadOptions = function(
        packageId,
        remoteAction,
    ) {
        GambDuplicaWP.Util.ajaxWrapper(
            {
                action: 'gamb-duplica-wp_get_remote_restore_download_options',
                packageId: packageId,
                remoteAction: remoteAction,
                nonce: "<?php echo esc_js(wp_create_nonce('gamb-duplica-wp_get_remote_restore_download_options')); ?>"
            },
            function (result, data, funcData, textStatus, jqXHR) {
                if (funcData.alreadyInUse) {
                    <?php $alreadyDownloadDlg->showAlert(); ?>
                } else if (!funcData.packageExists) {
                    <?php if (GAMB_DUPLICA_WP_Global_Entity::getInstance()->getPurgeBackupRecords() === AbstractStorageEntity::BACKUP_RECORDS_REMOVE_ALL) {
                        $removeBackupRecordConfirm->showConfirm();
                    } else { ?>
                        DupPro.addAdminMessage(funcData.message, 'error');
                    <?php } ?>
                    $("#<?php echo esc_js($removeBackupRecordConfirm->getID()); ?>-confirm").attr('data-id', packageId);
                    $("tr[data-package-id='" + packageId + "'] button[data-needs-download]").prop('disabled', true);
                    $("#dup-row-pack-id-" + packageId + " .remote-storage-flag").remove();
                } else {
                    <?php $remoteDownloadOptionsDlg->showAlert(); ?>
                    remoteDownloadModal.html(data.funcData.content);
                }
                return '';        
            },
            function(result, data, funcData, textStatus, jqXHR) {
                DupPro.addAdminMessage(data.message, 'error');
                console.log(data);
                return '';
            },
            { timeout: 300000 } //Fetching validity of multiple storages can take a while
        );
        
        return false;
    }
});
</script>
