<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$global    = GAMB_DUPLICA_WP_Global_Entity::getInstance();
$boxOpened = GAMB_DUPLICA_WP_UI_ViewState::getValue('dup-pack-storage-panel');
?>
<div class="dup-box" id="dup-pack-storage-panel-area">
    <div class="dup-box-title" id="dpro-store-title">
        <i class="fas fa-server fa-sm"></i> 
        <?php esc_html_e('Storage', 'gamb-duplica-wp') ?> <sup id="dpro-storage-title-count" class="dup-box-title-badge"></sup>
        <button class="dup-box-arrow">
            <span class="screen-reader-text">
                <?php esc_html_e('Toggle panel:', 'gamb-duplica-wp') ?> <?php esc_html_e('Storage Options', 'gamb-duplica-wp') ?>
            </span>
        </button>
    </div>          
    <div id="dup-pack-storage-panel" class="dup-box-panel <?php echo ($boxOpened ? '' : 'no-display'); ?>">
        <p>
            <?php esc_html_e('Choose the storage location(s) where the Backup and Installer files will be saved.', 'gamb-duplica-wp') ?>
        </p>
        <?php $tplMng->render(
            'parts/storage/select_list',
            [
                'selectedStorageIds' => $global->getManualModeStorageIds(),
                'recoveryPointMsg'   => true,
            ]
        ); ?>
    </div>
</div>


<script>
    jQuery(function ($)
    {
        DupPro.Pack.UpdateStorageCount = function ()
        {
            var store_count = $('#dup-pack-storage-panel input[name="_storage_ids[]"]:checked').length;
            $('#dpro-storage-title-count').html('(' + store_count + ')');
            (store_count == 0)
                    ? $('#dpro-storage-title-count').css({'color': 'red', 'font-weight': 'bold'})
                    : $('#dpro-storage-title-count').css({'color': '#444', 'font-weight': 'normal'});
        }

        $('#dup-pack-storage-panel input[name="_storage_ids[]"]').on('change', function () {
            DupPro.Pack.UpdateStorageCount();
        });
    });

//INIT
    jQuery(document).ready(function ($)
    {
        DupPro.Pack.UpdateStorageCount();
    });
</script>
