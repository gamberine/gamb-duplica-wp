<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Controllers\StoragePageController;
use gamb-duplica-wp\Core\CapMng;
use gamb-duplica-wp\Models\Storages\AbstractStorageEntity;
use gamb-duplica-wp\Models\Storages\StoragesUtil;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var bool $blur
 */

$blur = $tplData['blur'];
/** @var GAMB_DUPLICA_WP_Package */
$package      = $tplData['package'];
$storage_list = AbstractStorageEntity::getAll(0, 0, [StoragesUtil::class, 'sortByPriority']);

$newStorageEditUrl = StoragePageController::getEditUrl();

$transfer_occurring = (($package->Status >= GAMB_DUPLICA_WP_PackageStatus::STORAGE_PROCESSING) && ($package->Status < GAMB_DUPLICA_WP_PackageStatus::COMPLETE));

$view_state          = GAMB_DUPLICA_WP_UI_ViewState::getArray();
$ui_css_transfer_log = (isset($view_state['dup-transfer-transfer-log']) && $view_state['dup-transfer-transfer-log']) ? 'display:block' : 'display:none';

$installer_name = $package->Installer->getInstallerName();
$archive_name   = $package->Archive->File ?? __('Unable to locate file', 'gamb-duplica-wp');

?>
<div class="transfer-panel <?php echo ($blur ? 'dup-mock-blur' : ''); ?>">
    <div class="transfer-hdr">
        <h2 class="title">
            <i class="fas fa-exchange-alt"></i> <?php esc_html_e('Manual Transfer', 'gamb-duplica-wp'); ?>
            <button id="dup-trans-ovr" type="button" class="dup-btn-borderless"
                    title="<?php esc_html_e('Show file details', 'gamb-duplica-wp'); ?>"
                    onclick="DupPro.Pack.Transfer.toggleOverview()">
                <i class="fas fa-chevron-left fa-fw fa-sm"></i> <?php esc_html_e('Details', 'gamb-duplica-wp') ?>
            </button>
        </h2>
        <hr/>
    </div>

    <!-- ===================
    OVERVIEW -->
    <div id="step1-ovr">
        <h3><?php esc_html_e('File Overview', 'gamb-duplica-wp'); ?></h3>
        <small>
            <?php esc_html_e('These files will be transferred to the selected storage locations. Links are sensitive. Keep them safe!', 'gamb-duplica-wp'); ?>
        </small>
        <label>
            <i class="far fa-file-archive fa-fw"></i>
            <b><?php esc_html_e('Backup File', 'gamb-duplica-wp'); ?></b>
            <?php echo '&nbsp;(' . esc_html(GAMB_DUPLICA_WP_U::byteSize($package->Archive->Size)) . ')'; ?><br/>
            <div class="horizontal-input-row">
                <input class="margin-right-1" type="text" value="<?php echo esc_attr($archive_name) ?>" readonly="readonly" />
                <span onclick="jQuery(this).parent().find('input').select();">
                    <span class="copy-button" data-dup-copy-value="<?php echo esc_attr($archive_name); ?>">
                        <i class='far fa-copy dup-cursor-pointer'></i> <?php esc_html_e('Copy Name', 'gamb-duplica-wp'); ?>
                    </span>
                </span>
            </div>
        </label>

        <label>
            <i class="fa fa-bolt fa-fw"></i>
            <b><?php esc_html_e('Backup Installer', 'gamb-duplica-wp'); ?></b>
            <?php echo '&nbsp;(' . esc_html(GAMB_DUPLICA_WP_U::byteSize($package->Installer->Size)) . ')'; ?><br/>
            <div class="horizontal-input-row">
                <input class="margin-right-1" type="text" value="<?php echo esc_attr($installer_name) ?>" readonly="readonly" />
                <span onclick="jQuery(this).parent().find('input').select();">
                    <span class="copy-button" data-dup-copy-value="<?php echo esc_attr($installer_name); ?>">
                        <i class='far fa-copy dup-cursor-pointer'></i> <?php esc_html_e('Copy Name', 'gamb-duplica-wp'); ?>
                    </span>
                </span>
            </div>
        </label>
    </div>

    <!-- ===================
    STEP 1 -->
    <div id="step2-section">
        <div style="margin:0px 0 0px 0">
            <h3><?php esc_html_e('Step 1: Choose Location', 'gamb-duplica-wp') ?></h3>
            <input style="display:none" type="radio" name="location" id="location-storage" checked="checked" onclick="DupPro.Pack.Transfer.ToggleLocation()" />
            <label style="display:none" for="location-storage"><?php esc_html_e('Storage', 'gamb-duplica-wp'); ?></label>
            <input style="display:none" type="radio" name="location" id="location-quick" onclick="DupPro.Pack.Transfer.ToggleLocation()" />
            <label style="display:none" for="location-quick"><?php esc_html_e('Quick FTP Connect', 'gamb-duplica-wp'); ?></label>
        </div>

        <!-- STEP 1: STORAGE -->
        <div id="location-storage-opts">
            <?php $tplMng->render(
                'parts/storage/select_list',
                ['filteredStorageIds' => [StoragesUtil::getDefaultStorageId()]]
            ); ?>
        </div>
    </div>

    <!-- ===================
    STEP 2 -->
    <div id="step3-section">
        <h3>
            <?php esc_html_e('Step 2: Transfer Files', 'gamb-duplica-wp') ?>
            <button style="<?php echo ($transfer_occurring ? 'none' : 'default'); ?>"
                      id="dup-pro-transfer-btn" type="button"
                      class="button primary small"
                      onclick="DupPro.Pack.Transfer.StartTransfer();">
                <?php esc_attr_e('Start Transfer', 'gamb-duplica-wp') ?> &nbsp; <i class="fas fa-upload"></i>

            </button>
        </h3>
        
        <div style="width:700px; text-align: center; margin-left: auto; margin-right: auto" class="dpro-active-status-area">
            <div style="display:none; font-size:20px; font-weight:bold" id="dpro-progress-bar-percent"></div>
            <div style="font-size:14px" id="dpro-progress-bar-text"><?php esc_html_e('Processing', 'gamb-duplica-wp') ?></div>
            <div id="dpro-progress-bar-percent-help">
                <small><?php esc_html_e('Full Backup percentage shown on Backups screen', 'gamb-duplica-wp'); ?></small>
            </div>
        </div>

        <div class="dpro-progress-bar-container">
            <div id="dpro-progress-bar-area" class="dpro-active-status-area">
                <div class="dup-pro-meter-wrapper">
                    <div class="dup-pro-meter green dup-pro-fullsize">
                        <span></span>
                    </div>
                    <span class="text"></span>
                </div>
                <button disabled id="dup-pro-stop-transfer-btn" type="button" class="button primary dpro-btn-stop" value=""
                        onclick="DupPro.Pack.Transfer.StopBuild();">
                    <i class="fa fa-times fa-sm"></i> &nbsp; <?php esc_html_e('Stop Transfer', 'gamb-duplica-wp'); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- ===============================
    TRANSFER LOG -->
    <div class="dup-box">
        <div class="dup-box-title">
            <i class="fas fa-file-contract fa-fw fa-sm"></i>
            <?php esc_html_e('Transfer Log', 'gamb-duplica-wp') ?>
            <button class="dup-box-arrow">
                <span class="screen-reader-text">
                    <?php esc_html_e('Toggle panel:', 'gamb-duplica-wp') ?> <?php esc_html_e('Transfer Log', 'gamb-duplica-wp') ?>
                </span>
            </button>
        </div>
        <div class="dup-box-panel" id="dup-transfer-transfer-log" style="<?php echo esc_attr($ui_css_transfer_log) ?>">
            <table class="widefat package-tbl dup-table-list small">
                <thead>
                    <tr>
                        <th style='width:150px'><?php esc_html_e('Started', 'gamb-duplica-wp') ?></th>
                        <th style='width:150px'><?php esc_html_e('Stopped', 'gamb-duplica-wp') ?></th>
                        <th style="white-space: nowrap"><?php esc_html_e('Status', 'gamb-duplica-wp') ?></th>
                        <th style="white-space: nowrap"><?php esc_html_e('Type', 'gamb-duplica-wp') ?></th>
                        <th style="width: 60%; white-space: nowrap"><?php esc_html_e('Description', 'gamb-duplica-wp') ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" id="dup-pack-details-trans-log-count"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<?php
$alert1          = new GAMB_DUPLICA_WP_UI_Dialog();
$alert1->title   = __('Storage Warning!', 'gamb-duplica-wp');
$alert1->message = __('At least one storage location must be selected.', 'gamb-duplica-wp');
$alert1->initAlert();

$alert2          = new GAMB_DUPLICA_WP_UI_Dialog();
$alert2->title   = __('Transfer Failure!', 'gamb-duplica-wp');
$alert2->message = __('Transfer failure when calling gamb-duplica-wp_pro_manual_transfer_storage.', 'gamb-duplica-wp');
$alert2->initAlert();

$alert3          = new GAMB_DUPLICA_WP_UI_Dialog();
$alert3->title   = __('Build Error', 'gamb-duplica-wp');
$alert3->message = __('Failed to stop build', 'gamb-duplica-wp');
$alert3->initAlert();

$alert4          = new GAMB_DUPLICA_WP_UI_Dialog();
$alert4->title   = $alert3->title;
$alert4->message = __('Failed to stop build due to AJAX error.', 'gamb-duplica-wp');
$alert4->initAlert();

$alert5          = new GAMB_DUPLICA_WP_UI_Dialog();
$alert5->title   = __('INFO!', 'gamb-duplica-wp');
$alert5->message = '';  // javascript inserted message
$alert5->initAlert();

$alert6          = new GAMB_DUPLICA_WP_UI_Dialog();
$alert6->title   = __('INFO!', 'gamb-duplica-wp');
$alert6->message = '';  // javascript inserted message
$alert6->initAlert();
?>
<script>
    DupPro.Pack.Transfer = {};
    jQuery(document).ready(function ($) {

        var transferRequestedTimestamp = 0;
        var activePackageId = -1;

        DupPro.Pack.Transfer.toggleOverview = function () {
            $('div#step1-ovr').toggle();
            var $i = $('#dup-trans-ovr i');

            if ($($i).hasClass('fa-chevron-left')) {
                $($i).removeClass('fa-chevron-left').addClass('fa-chevron-down');
            } else {
                $($i).removeClass('fa-chevron-down').addClass('fa-chevron-left');
            }
        }

        DupPro.Pack.Transfer.GetTimeStamp = function () {
            return Math.floor(Date.now() / 1000);
        }

        /*  METHOD: Starts the data transfer */
        DupPro.Pack.Transfer.StartTransfer = function () {

            if (jQuery('#location-storage-opts input[type=checkbox]:checked').length == 0) {
                <?php $alert1->showAlert(); ?>
            } else {
                $(".dpro-active-status-area").show(500);
                var selected_storage_ids = $.map($(':checkbox[name=_storage_ids\\[\\]]:checked'), function (n, i) {
                    return n.value;
                });

                console.log("sending to selected storages " + selected_storage_ids);

                transferRequestedTimestamp = DupPro.Pack.Transfer.GetTimeStamp();

                $("#dpro-progress-bar-text").text("<?php echo esc_html__('Initiating transfer. Please wait.', 'gamb-duplica-wp') ?>");
                $("#dpro-progress-bar-percent").text('');
                DupPro.Pack.Transfer.SetUIState(true);

                var data = {
                    action: 'gamb-duplica-wp_pro_manual_transfer_storage',
                    package_id: <?php echo (int) $package->ID; ?>,
                    storage_ids: selected_storage_ids,
                    nonce: '<?php echo esc_js(wp_create_nonce('gamb-duplica-wp_pro_manual_transfer_storage')); ?>'
                }

                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    cache: false,
                    timeout: 10000000,
                    data: data,
                    success: function (parsedData) {
                        if (!parsedData.success)
                        {
                            if (parsedData.message != '') {
                                <?php $alert5->showAlert(); ?>
                                $("#<?php echo esc_js($alert5->getID()); ?>_message").html(parsedData.message);
                            }
                            transferRequestedTimestamp = 0;
                            DupPro.Pack.Transfer.SetUIState(false);
                            DupPro.Pack.Transfer.GetPackageState();
                        }
                    },
                    error: function (respData) {
                        <?php $alert2->showAlert(); ?>
                        transferRequestedTimestamp = 0;
                        DupPro.Pack.Transfer.SetUIState(false);
                        console.log(respData);
                    }
                });
            }
        };

        /*  METHOD: Starts the data transfer */
        DupPro.Pack.Transfer.StopBuild = function () {
            $("#dup-pro-stop-transfer-btn").prop("disabled", true);
            
            gamb-duplica-wp.Util.ajaxWrapper(
                {
                    action: 'gamb-duplica-wp_pro_package_stop_build',
                    package_id: activePackageId,
                    nonce: '<?php echo esc_js(wp_create_nonce("gamb-duplica-wp_pro_package_stop_build")); ?>'
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    if (!funcData.success) {
                        <?php $alert3->showAlert(); ?>
                        $("#dup-pro-stop-transfer-btn").prop("disabled", false);
                    }
                    DupPro.addAdminMessage(
                        "<?php esc_html_e('Backups transfer cancellation was initiated.', 'gamb-duplica-wp'); ?>",
                        'notice',
                        {hideDelay: 3000}
                    );
                    console.log(funcData.message);
                    return '';
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    <?php $alert4->showAlert(); ?>
                    $("#dup-pro-stop-transfer-btn").prop("disabled", false);
                    return '';
                }
            );
        };

        /*  METHOD: Progress bar display state*/
        DupPro.Pack.Transfer.SetUIState = function (activeProcessing) {
            if (activeProcessing)
            {
                $(".dpro-active-status-area").show(500);
                $("#dup-pro-transfer-btn").hide();
                $("#location-storage input").prop("disabled", true);
                //$("#location-storage-opts input").prop("disabled", true);
            } else {
                $("#dup-pro-stop-transfer-btn").prop("disabled", true);
                // Only allow to revert after enough time has past since the last transfer request
                currentTimestamp = DupPro.Pack.Transfer.GetTimeStamp();
                if ((currentTimestamp - transferRequestedTimestamp) > 10)
                {
                    $("#location-storage input").prop("disabled", false);
                    //$("#location-storage-opts input").prop("disabled", false);
                    $("#dup-pro-transfer-btn").show();
                    $(".dpro-active-status-area").hide();
                }
            }
        }

        /*  METHOD: Retreive Backup state */
        DupPro.Pack.Transfer.GetPackageState = function () {
            var package_id = <?php echo (int) $package->ID; ?>;
            gamb-duplica-wp.Util.ajaxWrapper(
                {
                    action: 'gamb-duplica-wp_pro_packages_details_transfer_get_package_vm',
                    package_id: package_id,
                    nonce: '<?php echo esc_js(wp_create_nonce('gamb-duplica-wp_pro_packages_details_transfer_get_package_vm')); ?>'
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    var vm = funcData.vm;

                    // vm - view model for this screen
                    // vm.active_package_id: Active Backup id (-1 for none)
                    // vm.percent_text: Percent through the current transfer
                    // vm.text: Text to display
                    // vm.transfer_logs: array of transfer request vms (start, stop, status, message)

                    if (activePackageId != vm.active_package_id)
                    {
                        // Once we have an active Backup ID allow the stop button to be clicked
                        $("#dup-pro-stop-transfer-btn").prop("disabled", false);
                    }

                    activePackageId = vm.active_package_id;
                    if (vm.active_package_id == -1)
                    {
                        // No backups are running
                        DupPro.Pack.Transfer.SetUIState(false);

                    } else if (vm.active_package_id == package_id) {

                        // This Backup is running
                        if (vm.percent_text != '')
                        {
                            $("#dpro-progress-bar-percent").text(vm.percent_text);
                        } else
                        {
                            $("#dpro-progress-bar-percent").text('');
                        }

                        $("#dpro-progress-bar-text").html(vm.text);
                        DupPro.Pack.Transfer.SetUIState(true);
                    } else {

                        // A package other than this one is running
                        $("#dpro-progress-bar-text").html(vm.text);
                        DupPro.Pack.Transfer.SetUIState(true);
                    }
                    DupPro.Pack.Transfer.UpdateTransferLog(vm);
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    if (data.message != '') {
                        <?php $alert6->showAlert(); ?>
                        $("#<?php echo esc_js($alert6->getID()); ?>_message").html(data.message);
                    }
                    DupPro.Pack.Transfer.SetUIState(false);
                    console.log(data);
                },
                {
                    showProgress: false
                }
            );
        };

        /*  METHOD: Updates the transfer log with the information from the view model */
        DupPro.Pack.Transfer.UpdateTransferLog = function (vm) {
            $("#dup-transfer-transfer-log table tbody").empty();
            var row_style, row_html;
            for (var i = 0; i < vm.transfer_logs.length; i++) {

                var transfer_log = vm.transfer_logs[i];
                console.log(transfer_log);
                
                row_style = (i % 2) ? ' alternate' : '';
                switch(transfer_log.status_text) {
                    case 'Pending':     row_style += ' status-pending';   break;
                    case 'Running':     row_style += ' status-running';   break;
                    case 'Failed':      row_style += ' status-failed';    break;
                    default:            row_style += ' status-normal';    break;
                }
                
                row_html =
                `<tr class="package-row ${row_style}">
                    <td>${transfer_log.started}</td>
                    <td>${transfer_log.stopped}</td>
                    <td>${transfer_log.status_text}</td>
                    <td>${transfer_log.storage_type_text}</td>
                    <td>${transfer_log.message}</td>
                </tr>`;

                $("#dup-transfer-transfer-log table tbody").append(row_html);
                $('#dup-pack-details-trans-log-count').html('<?php esc_html_e('Log Items:', 'gamb-duplica-wp') ?> ' + (i + 1) );
            }

            if (i == 0)
            {
                var row_html = '<tr><td colspan="5" style="text-align:center">' + 
                    '<?php esc_html_e('- No transactions found for this Backup -', 'gamb-duplica-wp'); ?></td></tr>';
                $("#dup-transfer-transfer-log table tbody").append(row_html);
            }
        };

        //INIT
        DupPro.Pack.Transfer.GetPackageState();
        setInterval(DupPro.Pack.Transfer.GetPackageState, 8000);
    });
</script>
