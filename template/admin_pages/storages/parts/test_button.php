<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Models\Storages\AbstractStorageEntity;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var AbstractStorageEntity $storage
 */
$storage = $tplData["storage"];

$buttonDisabled = ($storage->getId() < 0 || $storage->isValid() == false);
?>
<table class="form-table">    
    <tr>
        <th scope="row">
            <label for=""><?php esc_html_e("Validation", 'gamb-duplica-wp'); ?></label>
        </th>
        <td>
            <button 
                id="button_file_test" 
                class="button secondary hollow small button_file_test margin-bottom-0"
                type="button" 
                onclick="DupPro.Storage.Test(); return false;"
                <?php disabled($buttonDisabled); ?>
            >
                <i class="fas fa-cloud-upload-alt fa-sm"></i> <?php esc_html_e('Test Storage', 'gamb-duplica-wp'); ?>
            </button>
            <p>
                <i><?php esc_html_e("Test creating and deleting a small file on storage.", 'gamb-duplica-wp'); ?></i>
            </p>
        </td>
    </tr>
</table>
<?php

$alertStorageStatus          = new GAMB_DUPLICA_WP_UI_Dialog();
$alertStorageStatus->title   = __('Storage Status', 'gamb-duplica-wp');
$alertStorageStatus->height  = 185;
$alertStorageStatus->message = 'testings'; // javascript inserted message
$alertStorageStatus->initAlert();

$alertStorageStatusLong               = new GAMB_DUPLICA_WP_UI_Dialog();
$alertStorageStatusLong->title        = __('Storage Status', 'gamb-duplica-wp');
$alertStorageStatusLong->width        = 800;
$alertStorageStatusLong->height       = 520;
$alertStorageStatusLong->showTextArea = true;
$alertStorageStatusLong->textAreaRows = 15;
$alertStorageStatusLong->textAreaCols = 100;
$alertStorageStatusLong->message      = ''; // javascript inserted message
$alertStorageStatusLong->initAlert();

?>

<script>
    jQuery(document).ready(function ($) {

        DupPro.Storage.Test = function ()
        {
            var $test_button = $('#button_file_test');
            $test_button.html('<i class="fas fa-circle-notch fa-spin"></i> <?php esc_html_e('Attempting to test storage', 'gamb-duplica-wp'); ?>');

            gamb-duplica-wp.Util.ajaxWrapper(
                {
                    action: 'gamb-duplica-wp_pro_storage_test',
                    storage_id: <?php echo (int) $storage->getId(); ?>,
                    nonce: '<?php echo esc_js(wp_create_nonce('gamb-duplica-wp_pro_storage_test')); ?>'
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    console.log('Func data',funcData);
                    if (funcData.success) {
                        if (funcData.status_msgs.length==0) {
                            <?php $alertStorageStatus->showAlert(); ?>
                            let alertMsg = "<span style='color:green'><b><input type='checkbox' class='checkbox' checked disabled='disabled'>" + 
                                funcData.message+"</b></span>";
                            <?php $alertStorageStatus->updateMessage("alertMsg"); ?>
                        } else {
                            <?php $alertStorageStatusLong->showAlert(); ?>
                            <?php $alertStorageStatusLong->updateTextareaMessage("funcData.status_msgs"); ?>
                            let alertMsg = "<span style='color:green'><b><input type='checkbox' class='checkbox' checked disabled='disabled'>" + 
                                funcData.message+"</b></span>";
                            <?php $alertStorageStatusLong->updateMessage("alertMsg"); ?>
                        }
                    } else {
                        if (funcData.status_msgs.length==0) {
                            <?php $alertStorageStatus->showAlert(); ?>
                            let alertMsg = "<i class='fas fa-exclamation-triangle'></i> "+funcData.message;
                            <?php $alertStorageStatus->updateMessage("alertMsg"); ?>
                        } else {
                            <?php $alertStorageStatusLong->showAlert(); ?>
                            <?php $alertStorageStatusLong->updateTextareaMessage("funcData.status_msgs"); ?>
                            let alertMsg = "<i class='fas fa-exclamation-triangle'></i> "+funcData.message;
                            <?php $alertStorageStatusLong->updateMessage("alertMsg"); ?>
                        }
                    }
                    $test_button.html('<i class="fas fa-cloud-upload-alt fa-sm"></i> <?php esc_html_e('Test Storage', 'gamb-duplica-wp'); ?>');
                    return '';
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    $test_button.html('<i class="fas fa-cloud-upload-alt fa-sm"></i> <?php esc_html_e('Test Storage', 'gamb-duplica-wp'); ?>');
                    <?php $alertStorageStatus->showAlert(); ?>
                    let alertMsg = "<i class='fas fa-exclamation-triangle'></i> <?php esc_html_e('AJAX error while testing storage', 'gamb-duplica-wp'); ?>";
                    <?php $alertStorageStatus->updateMessage("alertMsg"); ?>
                    console.log(parsedData);
                    return '';
                }
            );
        }
    });
</script>
