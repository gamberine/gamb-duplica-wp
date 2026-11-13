<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */


if (!CapMng::can(CapMng::CAP_CREATE, false)) {
    return;
}

$confirm1             = new GAMB_DUPLICA_WP_UI_Dialog();
$confirm1->title      = __('Run Validator', 'gamb-duplica-wp');
$confirm1->message    = __('This will run the scan validation check.  This may take several minutes.  Do you want to Continue?', 'gamb-duplica-wp');
$confirm1->progressOn = false;
$confirm1->jsCallback = 'DupPro.Tools.runScanValidator()';
$confirm1->initConfirm();
?>

<label class="lbl-larger">
    <?php esc_html_e('Scan Validator', 'gamb-duplica-wp'); ?>
</label>
<div>
    <button
        id="scan-run-btn"
        type="button"
        class="button secondary small margin-bottom-0"
        onclick="DupPro.Tools.ConfirmScanValidator()"
    >
        <?php esc_html_e("Run Scan Integrity Validation", 'gamb-duplica-wp'); ?>
    </button>
    <p class="description">
        <?php esc_html_e('This utility identifies unreadable files and sys-links, potentially causing scanning issues.', 'gamb-duplica-wp'); ?>
    </p>
    <script id="hb-template" type="text/x-handlebars-template">
        <b><?php esc_html_e('Scan Paths:', 'gamb-duplica-wp'); ?></b><br/>
        {{#if scanData.scanPaths}}
            {{#each scanData.scanPaths}}
                &nbsp; &nbsp; {{@index}} : {{this}}<br/>
            {{/each}}
        {{else}}
            <i><?php esc_html_e('Empty scan path', 'gamb-duplica-wp'); ?></i> <br/>
        {{/if}}
        <br/>
        <b><?php esc_html_e('Scan Results', 'gamb-duplica-wp'); ?></b><br/>
        <table>
            <tr>
                <td><b><?php esc_html_e('Files:', 'gamb-duplica-wp'); ?></b></td>
                <td>{{scanData.fileCount}} </td>
                <td> &nbsp; </td>
                <td><b><?php esc_html_e('Dirs:', 'gamb-duplica-wp'); ?></b></td>
                <td>{{scanData.dirCount}} </td>
            </tr>
        </table>
        <br/>

        <b><?php esc_html_e('Unreadable Dirs/Files:', 'gamb-duplica-wp') ?></b> <br/>
        {{#if scanData.unreadable}}
            {{#each scanData.unreadable}}
                &nbsp; &nbsp; {{@index}} : {{this}}<br/>
            {{/each}}
        {{else}}
            <i><?php esc_html_e('No Unreadable items found', 'gamb-duplica-wp'); ?></i> <br/>
        {{/if}}
        <br/>

        <b><?php esc_html_e('Symbolic Links:', 'gamb-duplica-wp'); ?></b> <br/>
        {{#if scanData.symLinks}}
            {{#each scanData.symLinks}}
                &nbsp; &nbsp; {{@index}} : {{this}}<br/>
            {{/each}}
        {{else}}
            <i><?php esc_html_e('No Sym-links found', 'gamb-duplica-wp') ?></i> <br/>
            <small> <?php esc_html_e("Note: Symlinks are not discoverable on Windows OS with PHP", 'gamb-duplica-wp'); ?></small> <br/>
        {{/if}}
        <br/>

        <b><?php esc_html_e('Directory Name Checks:', 'gamb-duplica-wp') ?></b> <br/>
        {{#if scanData.nameTestDirs}}
            {{#each scanData.nameTestDirs}}
                &nbsp; &nbsp; {{@index}} : {{this}}<br/>
            {{/each}}
        {{else}}
            <i><?php esc_html_e('No name check warnings located for directory paths', 'gamb-duplica-wp'); ?></i> <br/>
        {{/if}}
        <br/>

        <b><?php esc_html_e('File Name Checks:', 'gamb-duplica-wp') ?></b> <br/>
        {{#if scanData.nameTestFiles}}
            {{#each scanData.nameTestFiles}}
                &nbsp; &nbsp; {{@index}} : {{this}}<br/>
            {{/each}}
        {{else}}
            <i><?php esc_html_e('No name check warnings located for directory paths', 'gamb-duplica-wp'); ?></i> <br/>
        {{/if}}

        <br/>
    </script>
    <div id="hb-result"></div>
</div>
<script>
jQuery(document).ready(function($)
{
    DupPro.Tools.ConfirmScanValidator = function()
    {
        <?php $confirm1->showConfirm(); ?>
    }


    //Run request to: admin-ajax.php?action=DUP_CTRL_Tools_runScanValidator
    DupPro.Tools.runScanValidator = function()
    {
        tb_remove();
        var data = {
            action : 'GAMB_DUPLICA_WP_CTRL_Tools_runScanValidator',
            nonce: '<?php echo wp_create_nonce('GAMB_DUPLICA_WP_CTRL_Tools_runScanValidator'); ?>',
            'scan-recursive': 1
        };

        $('#hb-result').html('<?php esc_html_e("Scanning Environment... This may take a few minutes.", 'gamb-duplica-wp'); ?>');
        $('#scan-run-btn').html('<i class="fas fa-circle-notch fa-spin fa-fw"></i> <?php echo esc_js(__('Running Please Wait...', 'gamb-duplica-wp')) ?>');

        $.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: "json",
            data: data,
            success: function(data) {
                DupPro.Tools.IntScanValidator(data);
            },
            error: function(data) {console.log(data)},
            done: function(data) {console.log(data)}
        });
    }

    //Process Ajax Template
    DupPro.Tools.IntScanValidator= function(data)
    {
        var template = $('#hb-template').html();
        var templateScript = Handlebars.compile(template);
        var html = templateScript(data);
        $('#hb-result').html(html);
        $('#scan-run-btn').html('<?php esc_html_e("Run Scan Integrity Validation", 'gamb-duplica-wp'); ?>');
    }
});
</script>

