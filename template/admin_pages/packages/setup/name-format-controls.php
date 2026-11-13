<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Package\NameFormat;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$nameFormat = $tplData['nameFormat'];
$notes      = $tplData['notes'];

$helpContent = $tplMng->render('admin_pages/packages/setup/name-format-help', [], false);
?>

<div>
    <label for="package-name-format" class="lbl-larger large">
        <?php esc_html_e('Backup Name Format', 'gamb-duplica-wp') ?>:
    </label>&nbsp;
    <i 
        class="fa-solid fa-question-circle fa-sm dark-gray-color"
        data-tooltip-title="<?php esc_attr_e("Backup name format", 'gamb-duplica-wp'); ?>"
        data-tooltip="<?php echo esc_attr($helpContent); ?>" 
        data-tooltip-width="400"
    ></i>

    <div class="dup-notes-add">
        <button 
            type="button" 
            onClick="jQuery('#dup-notes-area').toggle()" 
            class="clear button gray xtiny margin-bottom-0"  
            title="<?php esc_attr_e('Add Notes', 'gamb-duplica-wp') ?>"
        >
            <i class="far fa-edit"></i>
        </button>
    </div>
</div>

<div class="display-flex" >
    <input 
        type="text" 
        id="package-name-format" 
        name="package_name_format" 
        class="margin-0"
        data-parsley-errors-container="#template_package_name_error_container"
        data-parsley-required="true" 
        value="<?php echo esc_attr($nameFormat); ?>" 
        autocomplete="off"
    >
    <select class="dup-format-name-tags width-medium margin-left-1 margin-0 secondary-color secondary-border-color" >
        <option value="" selected >
            <?php esc_html_e('Dynamic Tags', 'gamb-duplica-wp') ?>
        </option>
        <?php foreach (NameFormat::FORMATS as $format) { ?>
            <option value="%<?php echo esc_attr($format); ?>%">
                %<?php echo esc_html($format); ?>%
            </option>
        <?php } ?>
    </select>
</div>
<div id="template_package_name_error_container" class="gamb-duplica-wp-error-container"></div>

<div id="dup-notes-area">
    <label class="lbl-larger large">
        <?php esc_html_e('Notes', 'gamb-duplica-wp') ?>:
    </label><br/>
    <textarea 
        id="package-notes" 
        name="package-notes" 
        maxlength="300" 
    ><?php echo esc_html($notes); ?></textarea>
</div>


<script>
    jQuery(document).ready(function($) {
        $('.dup-format-name-tags').change(function(e) {
            e.stopPropagation();

            if ($(this).val() === '') {
                return;
            }

            let input = $('#package-name-format');
            let currentValue = input.val();
            let newValue = currentValue + $(this).val();
            input.val(newValue);

            $(this).val('');
        });        
    })
</script>