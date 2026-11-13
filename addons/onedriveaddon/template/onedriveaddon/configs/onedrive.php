<?php

/**
 * gamb-duplica-wp messages sections
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Addons\OneDriveAddon\Models\OneDriveStorage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var OneDriveStorage $storage
 */
$storage = $tplData["storage"];
/** @var string */
$storageFolder = $tplData["storageFolder"];
/** @var int */
$maxPackages =  $tplData["maxPackages"];
/** @var bool */
$allFolderPers = $tplData["allFolderPers"];
/** @var false|object */
$accountInfo = $tplData["accountInfo"];
/** @var false|object */
$hasError = $tplData["hasError"];
/** @var string */
$externalRevokeUrl = $tplData["externalRevokeUrl"];

$tplMng->render('admin_pages/storages/parts/provider_head');
?>
<tr>
    <th scope="row"><label><?php esc_html_e("Authorization", 'gamb-duplica-wp'); ?></label></th>
    <td class="onedrive-authorize">
        <?php if (!$storage->isAuthorized()) : ?>
            <div class='onedrive-msgraph-authorization-state' id="onedrive-msgraph-state-unauthorized">
                <div id="dup-all-onedrive-allperms-wrapper" >
                    <?php esc_html_e('Are you using a business account?', 'gamb-duplica-wp'); ?>
                    <label class="switch">
                    <input
                        id="onedrive_msgraph_all_folders_read_write_perm"
                        name="onedrive_msgraph_all_folders_read_write_perm"
                        type="checkbox"
                        value="1"
                        <?php checked($allFolderPers); ?>
                    >
                        <span class="slider round"></span>
                    </label>
                    <div class="auth-code-popup-note" style="margin-top:1px; margin-left: 0;">
                        <?php
                        echo esc_html__('By default, we only request read/write permission for the "App Folder"', 'gamb-duplica-wp') . ' ' .
                        esc_html__('If you are planning to use a business account, we need read/write permission for all folders.', 'gamb-duplica-wp'); ?>
                    </div>
                </div>

                <!-- CONNECT -->
                <button
                    id="dpro-onedrive-msgraph-connect-btn"
                    type="button"
                    class="button secondary hollow margin-bottom-0"
                    onclick="DupPro.Storage.OneDrive.GetAuthUrl(); return false;"
                >
                    <i class="fa fa-plug"></i> <?php esc_html_e('Connect to OneDrive', 'gamb-duplica-wp'); ?>
                    <img
                        src="<?php echo esc_url(gamb-duplica-wp_PRO_IMG_URL . '/onedrive.svg'); ?>"
                        style='vertical-align: middle; margin:-2px 0 0 3px; height:18px; width:18px'
                    >
                </button>

                <div class='onedrive-msgraph-auth-container' style="display: none;">
                    <!-- STEP 1 -->
                    <div class="storage-auth-step">
                        <p>
                            <b><?php esc_html_e("Step 1:", 'gamb-duplica-wp'); ?></b>&nbsp;
                            <?php esc_html_e(' gamb-duplica-wp needs to authorize at OneDrive.', 'gamb-duplica-wp'); ?>
                        </p>
                        <div class="auth-code-popup-note">
                            <?php esc_html_e(
                                'Note: Clicking the button below will open a new tab/window. Please be sure your browser does not block popups.',
                                'gamb-duplica-wp'
                            ); ?>
                            <?php esc_html_e(
                                'If a new tab/window does not open check your browsers address bar to allow popups from this URL.',
                                'gamb-duplica-wp'
                            ); ?>
                        </div>
                        <button
                            id="dpro-onedrive-msgraph-auth-btn"
                            type="button"
                            class="button secondary hollow margin-bottom-0"
                            data-auth-url="<?php echo esc_attr($storage->getAuthorizationUrl()); ?>"
                        >
                            <i class="fa fa-user"></i> <?php esc_html_e('Authorize OneDrive', 'gamb-duplica-wp'); ?>
                        </button>
                    </div>
                    <!-- STEP 2 -->
                    <div class="storage-auth-step">
                        <p>
                            <b><?php esc_html_e('Step 2:', 'gamb-duplica-wp'); ?></b>
                            <?php esc_html_e("Paste code from OneDrive authorization page.", 'gamb-duplica-wp'); ?> <br/>
                        </p>
                        <input style="width:400px" id="onedrive-msgraph-auth-code" name="onedrive-msgraph-auth-code" type="text"/>
                    </div>
                    <!-- STEP 3 -->
                    <div class="storage-auth-step">
                        <p>
                            <b><?php esc_html_e("Step 3:", 'gamb-duplica-wp'); ?></b>&nbsp;
                            <?php esc_html_e('Finalize OneDrive validation by clicking the "Finalize Setup" button.', 'gamb-duplica-wp'); ?>
                        </p>
                        <button
                            type="button"
                            id="onedrive-msgraph-finalize-setup"
                            class="button secondary margin-bottom-0"
                        >
                            <i class="fa fa-check-square"></i> <?php esc_html_e('Finalize Setup', 'gamb-duplica-wp'); ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="onedrive-msgraph-authorization-state" id="onedrive-msgraph-state-authorized">
        <?php if ($storage->isAuthorized()) : ?>
                <h3>
                    <?php esc_html_e('OneDrive Account', 'gamb-duplica-wp'); ?><br/>
                    <i class="dpro-edit-info">
                        <?php esc_html_e('gamb-duplica-wp has been authorized to access this user\'s OneDrive account', 'gamb-duplica-wp'); ?>
                    </i>
                </h3>

                <?php
                if ($accountInfo !== false) {
                    ?>
                    <div id="onedrive-account-info">
                        <label><?php esc_html_e('Name', 'gamb-duplica-wp'); ?>:</label>
                        <?php echo esc_html($accountInfo->displayName); ?> <br/>
                    </div>
                </div>
                    <?php
                } elseif ($hasError) {
                    ?>
                    <div class="error-txt">
                        <?php
                        echo '<strong>';
                        esc_html_e('Please click on the "Cancel Authorization" button and reauthorize the OneDrive storage', 'gamb-duplica-wp');
                        echo '</strong>';
                        ?>
                    </div>
                    <?php
                }
                ?>
                <br/>
                <button type="button" class="button secondary hollow small" onclick='DupPro.Storage.OneDrive.CancelAuthorization();'>
                    <?php esc_html_e('Cancel Authorization', 'gamb-duplica-wp'); ?>
                </button><br/>
                <i class="dpro-edit-info">
                    <?php
                    esc_html_e(
                        'Disassociates storage provider with the OneDrive account. Will require re-authorization.',
                        'gamb-duplica-wp'
                    );
                    ?>
                </i>
        <?php endif; ?>
        </div>
    </td>
</tr>
<tr>
    <th scope="row"><label for="_onedrive_msgraph_storage_folder"><?php esc_html_e("Storage Folder", 'gamb-duplica-wp'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <b>//OneDrive/Apps/gamb-duplica-wp Pro/</b>
            <input
                id="_onedrive_msgraph_storage_folder"
                name="_onedrive_msgraph_storage_folder"
                type="text"
                value="<?php echo esc_attr($storageFolder); ?>"
                class="dpro-storeage-folder-path" data-parsley-pattern='^((?![\"*\:<>?\\|]).)*[^\.\:]$'
                data-parsley-errors-container="#onedrive_msgraph_storage_folder_error_container"
                data-parsley-pattern-message="<?php
                    echo esc_attr__(
                        'The folder path shouldn\'t include the special characters " * : < > ? / \ | or shouldn\'t end with a dot(".").',
                        'gamb-duplica-wp'
                    ); ?>"
            >
        </div>
        <p>
            <i>
                <?php
                esc_html_e(
                    "Folder where backups will be stored. This should be unique for each web-site using gamb-duplica-wp.",
                    'gamb-duplica-wp'
                ); ?>
            </i>
        </p>
        <div id="onedrive_msgraph_storage_folder_error_container" class="gamb-duplica-wp-error-container"></div>
    </td>
</tr>
<tr>
    <th scope="row"><label for=""><?php esc_html_e("Max Backups", 'gamb-duplica-wp'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <input
                data-parsley-errors-container="#onedrive_msgraph_max_files_error_container"
                id="onedrive_msgraph_max_files"
                name="onedrive_msgraph_max_files"
                type="text"
                value="<?php echo absint($maxPackages); ?>" maxlength="4"
            >
            <label for="onedrive_msgraph_max_files">
                <?php esc_html_e("Number of Backups to keep in folder.", 'gamb-duplica-wp'); ?>
            </label>
        </div>
        <?php $tplMng->render('admin_pages/storages/parts/max_backups_description'); ?>
        <div id="onedrive_msgraph_max_files_error_container" class="gamb-duplica-wp-error-container"></div>
    </td>
</tr>
<?php $tplMng->render('admin_pages/storages/parts/provider_foot');

// Alerts for OneDrive
$alertConnStatus          = new GAMB_DUPLICA_WP_UI_Dialog();
$alertConnStatus->title   = __('OneDrive Connection Status', 'gamb-duplica-wp');
$alertConnStatus->message = ''; // javascript inserted message
$alertConnStatus->initAlert();
?>
<script>
    jQuery(document).ready(function ($) {
        let storageId = <?php echo (int) $storage->getId(); ?>;

        let additionalScopes = [];

        DupPro.Storage.OneDrive.GetAuthUrl = function ()
        {
            $("#dpro-onedrive-msgraph-connect-btn").hide();
            $(".onedrive-msgraph-auth-container").show();
        };

        $('#dpro-onedrive-msgraph-auth-btn').click(function() {
            let authUrl = $(this).data('auth-url');
            if (additionalScopes.length > 0) {
                authUrl += '?' + $.param({additionalScopes});
            }
            window.open(authUrl, '_blank');
        });

        $('#onedrive_msgraph_all_folders_read_write_perm').change(function() {
            let allFolderPermission = $(this).is(':checked');
            if (allFolderPermission) {
                additionalScopes = ['Files.ReadWrite'];
            } else {
                additionalScopes = [];
            }
        });

        DupPro.Storage.OneDrive.CancelAuthorization = function ()
        {
            window.open(<?php echo json_encode($externalRevokeUrl); ?>, '_blank');
            DupPro.Storage.RevokeAuth(storageId);
        }

        DupPro.Storage.OneDrive.FinalizeSetup = function () {
            if ($('#onedrive-msgraph-auth-code').val().length > 5) {
                $("#dup-storage-form").submit();
            } else {
                <?php $alertConnStatus->showAlert(); ?>
                let alertMsg = "<i class='fas fa-exclamation-triangle'></i> " +
                    "<?php esc_html_e('Please enter your OneDrive authorization code!', 'gamb-duplica-wp'); ?>";
                <?php $alertConnStatus->updateMessage("alertMsg"); ?>
            }
        }

        $('#onedrive-msgraph-finalize-setup').click(function (event) {
            event.stopPropagation();

            if ($('#onedrive-msgraph-auth-code').val().length > 5) {
                DupPro.Storage.PrepareForSubmit();

                //$("#dup-storage-form").submit();

                DupPro.Storage.Authorize(
                    <?php echo (int) $storage->getId(); ?>,
                    <?php echo (int) $storage->getSType(); ?>,
                    {
                        'name': $('#name').val(),
                        'notes': $('#notes').val(),
                        'storage_folder': $('#_onedrive_msgraph_storage_folder').val(),
                        'max_packages': $('#onedrive_msgraph_max_files').val(),
                        'auth_code' : $('#onedrive-msgraph-auth-code').val()
                    }
                );
            } else {
                <?php $alertConnStatus->showAlert(); ?>
                let alertMsg = "<i class='fas fa-exclamation-triangle'></i> " +
                    "<?php esc_html_e('Please enter your OneDrive authorization code!', 'gamb-duplica-wp'); ?>";
                <?php $alertConnStatus->updateMessage("alertMsg"); ?>
            }

            return false;
        });
    });
</script>
