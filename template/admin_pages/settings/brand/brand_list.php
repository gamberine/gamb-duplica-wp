<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

use gamb-duplica-wp\Addons\ProBase\License\License;
use gamb-duplica-wp\Controllers\SettingsPageController;
use gamb-duplica-wp\Core\Controllers\ControllersManager;
use gamb-duplica-wp\Models\BrandEntity;

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$brand_list_url = ControllersManager::getCurrentLink([ControllersManager::QUERY_STRING_INNER_PAGE => SettingsPageController::BRAND_INNER_PAGE_LIST]);
$brand_edit_url = ControllersManager::getCurrentLink([ControllersManager::QUERY_STRING_INNER_PAGE => SettingsPageController::BRAND_INNER_PAGE_EDIT]);
$brands         = BrandEntity::getAllWithDefault();
$brand_count    = count($brands);
?>

<h3 class="title">
    <?php esc_html_e('Installer Branding', 'gamb-duplica-wp'); ?>
</h3>
<hr>

<?php
if (!License::can(License::CAPABILITY_BRAND)) {
    $tplMng->render('admin_pages/settings/brand/no_capability_list');
    return;
}
?>

<div class="dup-toolbar">
    <label for="bulk_action" class="screen-reader-text">Select bulk action</label>
    <select id="bulk_action" class="small" >
        <option value="-1" selected="selected">
            <?php esc_html_e('Bulk Actions', 'gamb-duplica-wp'); ?>
        </option>
        <option value="delete" title="<?php esc_attr_e('Delete selected brand endpoint(s)', 'gamb-duplica-wp'); ?>">
            <?php esc_html_e('Delete', 'gamb-duplica-wp'); ?>
        </option>
    </select>
    <input 
        type="button" 
        class="button hollow secondary small action" 
        value="<?php esc_html_e("Apply", 'gamb-duplica-wp') ?>" 
        onclick="DupPro.Settings.Brand.BulkAction()"
    >
    <span class="separator"></span>
    <?php $tplMng->render('admin_pages/settings/brand/brand_create_button'); ?>
</div>

<form id="dup-brand-form" action="<?php echo esc_attr($brand_list_url); ?>" method="post">
    <?php $tplData['actions'][SettingsPageController::ACTION_BRAND_DELETE]->getActionNonceFileds(); ?>
    <table class="widefat brand-tbl dup-table-list valign-top">
        <thead>
            <tr>
                <th style='width:10px;'>
                    <input type="checkbox" id="dpro-chk-all" title="Select all brand endpoints" onclick="DupPro.Settings.Brand.SetAll(this)">
                </th>
                <th style='width:100%;'><?php esc_html_e('Name', 'gamb-duplica-wp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr id='main-view-<?php echo (int) $brands[0]->getId(); ?>' class="brand-row row">
                <td>
                    <input type="checkbox" disabled="disabled" />
                </td>
                <td>
                    <a href="javascript:void(0);" onclick="DupPro.Settings.Brand.Edit(0)"><b><?php esc_html_e('Default', 'gamb-duplica-wp'); ?></b></a>
                    <div class="sub-menu">
                        <a href="javascript:void(0);" onclick="DupPro.Settings.Brand.Edit(0)"><?php esc_html_e('View', 'gamb-duplica-wp'); ?></a> |
                        <a href="javascript:void(0);" onclick="DupPro.Settings.Brand.View('<?php echo (int) $brands[0]->getId(); ?>');">
                            <?php esc_html_e('Quick View', 'gamb-duplica-wp'); ?>
                        </a>
                    </div>
                </td>
            </tr>
            <tr id="quick-view-<?php echo (int) $brands[0]->getId() ?>" class="brand-detail row-details">
                <td colspan="3">
                    <b><?php esc_html_e('QUICK VIEW', 'gamb-duplica-wp') ?></b> <br/>
                    <div>
                        <label><?php esc_html_e('Name', 'gamb-duplica-wp') ?>:</label>
                        <?php echo esc_html($brands[0]->name) ?>
                    </div>
                    <div>
                        <label><?php esc_html_e('Notes', 'gamb-duplica-wp') ?>:</label>
                        <?php echo (strlen($brands[0]->notes)) ? esc_html($brands[0]->notes) : esc_html__('(no notes)', 'gamb-duplica-wp'); ?>
                    </div>
                    <div>
                        <label><?php esc_html_e('Logo', 'gamb-duplica-wp') ?>:</label>
                        <?php
                        echo $brands[0]->logo  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                    </div>
                    <button 
                        type="button" 
                        class="button hollow secondary small" 
                        onclick="DupPro.Settings.Brand.View('<?php echo (int) $brands[0]->getId(); ?>');"
                    >
                        <?php esc_html_e('Close', 'gamb-duplica-wp') ?>
                    </button>
                </td>
            </tr>
            <?php
            $i = 0;
            foreach ($brands as $x => $brand) :
                if ($x === 0) {
                    continue; // remove default item in list because is defined out of loop below
                }
                $i++;

                //$brand_type = $brand->getModeText();
                ?>
                <tr id='main-view-<?php echo (int) $brand->getId() ?>' class="row brand-row<?php echo ($i % 2) ? ' alternate' : ''; ?>">
                    <td>
                        <?php if ($brand->editable) : ?>
                            <input name="selected_id[]" type="checkbox" value="<?php echo (int) $brand->getId(); ?>" class="item-chk" />
                        <?php else : ?>
                            <input type="checkbox" disabled="disabled" />
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="javascript:void(0);" onclick="DupPro.Settings.Brand.Edit('<?php echo (int) $brand->getId(); ?>')">
                            <b><?php echo esc_html($brand->name); ?></b>
                        </a>
                        <?php if ($brand->editable) : ?>
                            <div class="sub-menu">
                                <a href="javascript:void(0);" onclick="DupPro.Settings.Brand.Edit('<?php echo (int) $brand->getId(); ?>')">
                                    <?php esc_html_e('Edit', 'gamb-duplica-wp') ?>
                                </a> |
                                <a href="javascript:void(0);" onclick="DupPro.Settings.Brand.View('<?php echo (int) $brand->getId(); ?>');">
                                    <?php esc_html_e('Quick View', 'gamb-duplica-wp') ?>
                                </a> |
                                <a href="javascript:void(0);" onclick="DupPro.Settings.Brand.Delete('<?php echo (int) $brand->getId(); ?>');">
                                    <?php esc_html_e('Delete', 'gamb-duplica-wp') ?>
                                </a>
                            </div>
                        <?php else : ?>
                            <div class="sub-menu">
                                <a href="javascript:void(0);" onclick="DupPro.Settings.Brand.Edit(0)">
                                    <?php esc_html_e('View', 'gamb-duplica-wp') ?>
                                </a> |
                                <a href="javascript:void(0);" onclick="DupPro.Settings.Brand.View('<?php echo (int) $brand->getId(); ?>');">
                                    <?php esc_html_e('Quick View', 'gamb-duplica-wp') ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr id='quick-view-<?php echo (int) $brand->getId() ?>' class='<?php echo ($i % 2) ? 'alternate ' : ''; ?>brand-detail row-details'>
                    <td colspan="3">
                        <b><?php esc_html_e('QUICK VIEW', 'gamb-duplica-wp') ?></b> <br/>
                        <div>
                            <label><?php esc_html_e('Name', 'gamb-duplica-wp') ?>:</label>
                            <?php echo esc_html($brand->name); ?>
                        </div>
                        <div>
                            <label><?php esc_html_e('Notes', 'gamb-duplica-wp') ?>:</label>
                            <?php echo (strlen($brand->notes)) ? esc_html($brand->notes) : esc_html__('(no notes)', 'gamb-duplica-wp'); ?>
                        </div>
                        <div>
                            <label><?php esc_html_e('Logo', 'gamb-duplica-wp') ?>:</label>
                            <?php
                            echo $brand->logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            ?>
                        </div>
                        <button 
                            type="button" 
                            class="button hollow secondary small" 
                            onclick="DupPro.Settings.Brand.View('<?php echo (int) $brand->getId(); ?>');">
                            <?php esc_html_e('Close', 'gamb-duplica-wp') ?>
                        </button>
                    </td>
                </tr>
                <?php
            endforeach;
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" style="text-align:right; font-size:12px">
                    <?php echo esc_html__('Total', 'gamb-duplica-wp') . ': ' . (int) $brand_count; ?>
                </th>
            </tr>
        </tfoot>
    </table>
</form>

<!-- ==========================================
THICK-BOX DIALOGS: -->
<?php
$alert1          = new GAMB_DUPLICA_WP_UI_Dialog();
$alert1->title   = __('Bulk Action Required', 'gamb-duplica-wp');
$alert1->message = __('Please select an action from the "Bulk Actions" drop down menu!', 'gamb-duplica-wp');
$alert1->initAlert();

$alert2          = new GAMB_DUPLICA_WP_UI_Dialog();
$alert2->title   = __('Selection Required', 'gamb-duplica-wp');
$alert2->message = __('Please select at least one brand to delete!', 'gamb-duplica-wp');
$alert2->initAlert();

$confirm1               = new GAMB_DUPLICA_WP_UI_Dialog();
$confirm1->title        = __('Delete Brand?', 'gamb-duplica-wp');
$confirm1->message      = __('Are you sure you want to delete the selected brand(s)?', 'gamb-duplica-wp');
$confirm1->message     .= '<br/>';
$confirm1->message     .= '<small><i>' . __('Note: This action removes all brands.', 'gamb-duplica-wp') . '</i></small>';
$confirm1->progressText = __('Removing Brands, Please Wait...', 'gamb-duplica-wp');
$confirm1->jsCallback   = 'DupPro.Settings.Brand.BulkDelete()';
$confirm1->initConfirm();

$confirm2               = new GAMB_DUPLICA_WP_UI_Dialog();
$confirm2->title        = __('Delete Brand?', 'gamb-duplica-wp');
$confirm2->message      = __('Are you sure you want to delete the selected brand(s)?', 'gamb-duplica-wp');
$confirm2->progressText = __('Removing Brands, Please Wait...', 'gamb-duplica-wp');
$confirm2->jsCallback   = 'DupPro.Settings.Brand.DeleteThis(this)';
$confirm2->initConfirm();

$delete_nonce = wp_create_nonce('gamb-duplica-wp_pro_brand_delete');
?>
<script>
    jQuery(document).ready(function ($) {

        //Shows detail view
        DupPro.Settings.Brand.AddNew = function ()
        {
            document.location.href = <?php echo wp_json_encode("{$brand_edit_url}&action=new"); ?>;
        }

        DupPro.Settings.Brand.Edit = function (id)
        {
            if (id == 0) {
                document.location.href = <?php echo wp_json_encode("{$brand_edit_url}&action=default&id="); ?> + id;
            } else {
                document.location.href = <?php echo wp_json_encode("{$brand_edit_url}&action=edit&id="); ?> + id;
            }
        }

        //Shows detail view
        DupPro.Settings.Brand.View = function (id)
        {
            $('#quick-view-' + id).toggle();
            $('#main-view-' + id).toggle();
        }

        //Delets a single record
        DupPro.Settings.Brand.Delete = function (id)
        {
            <?php $confirm2->showConfirm(); ?>
            $("#<?php echo esc_js($confirm2->getID()); ?>-confirm").attr('data-id', id);
        }

        DupPro.Settings.Brand.DeleteThis = function (e)
        {
            var id = $(e).attr('data-id');
            $("input[name^='selected_id[]'][value='" + id + "']").prop('checked', true);
            $("#dup-brand-form").submit()
        }

        //  Creats a comma seperate list of all selected Backup ids
        DupPro.Settings.Brand.DeleteList = function ()
        {
            var arr = [];

            $("input[name^='selected_id[]']").each(function (i, index) {
                var $this = $(index);

                if ($this.is(':checked') == true) {
                    arr[i] = $this.val();
                }
            });

            return arr;
        }

        // Bulk delete
        DupPro.Settings.Brand.BulkDelete = function ()
        {
            var list = DupPro.Settings.Brand.DeleteList();
            var pageCount = $('#current-page-selector').val();
            var pageItems = $("input[name^='selected_id[]']");

            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: {
                    action: 'gamb-duplica-wp_pro_brand_delete',
                    brand_ids: list,
                    nonce: <?php echo wp_json_encode($delete_nonce); ?>
                },
            }).done(function (data) {
                $('#dup-brand-form').submit();
            });
        }

        // Confirm bulk action
        DupPro.Settings.Brand.BulkAction = function ()
        {
            var list = DupPro.Settings.Brand.DeleteList();

            if (list.length == 0) {
                <?php $alert2->showAlert(); ?>
                return;
            }

            var action = $('#bulk_action').val();
            var checked = ($('.item-chk:checked').length > 0);

            if (action != "delete") {
                <?php $alert1->showAlert(); ?>
                return;
            }

            if (checked) {
                switch (action) {
                    default:
                        <?php $alert2->showAlert(); ?>
                        break;
                    case 'delete':
                        <?php $confirm1->showConfirm(); ?>
                        break;
                }
            }
        }

        //Sets all for deletion
        DupPro.Settings.Brand.SetAll = function (chkbox) {
            $('.item-chk').each(function () {
                this.checked = chkbox.checked;
            });
        }
    });
</script>
