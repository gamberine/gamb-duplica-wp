<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$nonce = wp_create_nonce('gamb-duplica-wp_pro_export_settings');
?>
<form id="dup-tools-form-export" method="post">
    <input type="hidden"  name="action" value="dpro-export">
    <div class="dup-settings-wrapper margin-bottom-1" >
        <h3 class="title">
            <?php esc_html_e("Export gamb-duplica-wp Settings", 'gamb-duplica-wp') ?>
        </h3>
        <hr size="1" />
        <p class="width-xxlarge" >
            <?php
            esc_html_e(
                'Exports all schedules, storage locations, templates and settings from this gamb-duplica-wp Pro instance into a downloadable export file.
                The export file can then be used to import data settings from this instance of gamb-duplica-wp Pro into another plugin instance of gamb-duplica-wp Pro.',
                'gamb-duplica-wp'
            );
            ?>
        </p>
        <label class="lbl-larger">
            <?php esc_html_e("Export Settings File", 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1" >
            <input 
                type="button" 
                class="button secondary small margin-0" 
                value="<?php esc_attr_e("Export Data", 'gamb-duplica-wp'); ?>" 
                onclick="return DupPro.Tools.ExportDialog();"
            >
        </div>
    </div>
</form>

<div id="modal-window-export" style="display:none;">
    <p>
        <?php esc_html_e("This process will:", 'gamb-duplica-wp') ?><br/>
        <i class="far fa-check-circle"></i> 
        <?php esc_html_e("Export schedules, storage and templates to a file for import into another gamb-duplica-wp instance.", 'gamb-duplica-wp'); ?> <br/>
        <span class="alert-color">
            <i class="fas fa-exclamation-triangle fa-sm"></i>
            <?php esc_html_e("For security purposes, restrict access to this file and delete after use.", 'gamb-duplica-wp'); ?>
        </span>
    </p>
    <div class="float-right" >
        <input 
            type="button" 
            class="button secondary hollow small" 
            value="<?php esc_attr_e("Cancel", 'gamb-duplica-wp') ?>" 
            onclick="tb_remove();" 
        >&nbsp;
        <input 
            type="button" 
            class="button primary small" 
            value="<?php esc_attr_e("Run Export", 'gamb-duplica-wp') ?>" 
            onclick="DupPro.Tools.ExportProcess();setTimeout(function() { tb_remove(); }, 4000);" 
            title="<?php esc_attr_e("Generate and Download the Export File.", 'gamb-duplica-wp') ?>"
        >
    </div>
</div>
<script>
    DupPro.Tools.ExportProcess = function () 
    {
        var actionLocation = ajaxurl + '?action=gamb-duplica-wp_pro_export_settings' + '&nonce=' + '<?php echo esc_js($nonce); ?>';
        location.href = actionLocation;
    }

    DupPro.Tools.ExportDialog = function () 
    {
        var url = "#TB_inline?width=610&height=250&inlineId=modal-window-export";
        tb_show("<?php esc_html_e("Export gamb-duplica-wp Pro Data ?", 'gamb-duplica-wp') ?>", url);
        jQuery('#TB_window').addClass(<?php echo json_encode(GAMB_DUPLICA_WP_UI_Dialog::TB_WINDOW_CLASS); ?>);
        return false;
    }
</script>
