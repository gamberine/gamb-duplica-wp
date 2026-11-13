<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Installer\Package\ArchiveDescriptor;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var bool $isTemplateEdit
 */
$isTemplateEdit = $tplData['isTemplateEdit'];
/** @var ?GAMB_DUPLICA_WP_Package_Template_Entity */
$template = ($tplData['template'] ?? null);

$archive_format = (GAMB_DUPLICA_WP_Global_Entity::getInstance()->getBuildMode() == GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive ? 'daf' : 'zip');
?>
<div class="dup-box dup-archive-filters-wrapper">
    <div class="dup-box-title" >
        <i class="far fa-file-archive fa-sm"></i> <?php esc_html_e('Backup', 'gamb-duplica-wp') ?> 
        <?php if (!$isTemplateEdit) { ?>
            <sup class="dup-box-title-badge">
                <?php echo esc_html($archive_format); ?>
            </sup>
        <?php } ?>
        &nbsp;&nbsp;
        <?php $tplMng->render('parts/packages/filters/section_filters_incons'); ?>
        <button class="dup-box-arrow">
            <span class="screen-reader-text">
                <?php esc_html_e('Toggle panel:', 'gamb-duplica-wp') ?> <?php esc_html_e('Archive Settings', 'gamb-duplica-wp') ?>
            </span>
        </button>
    </div>
    
    <div
        id="dup-pack-archive-panel" 
        class="dup-box-panel <?php echo GAMB_DUPLICA_WP_UI_ViewState::getValue('dup-pack-archive-panel') ? '' : 'no-display'; ?>" 
    >
        <div data-dpro-tabs="true">
            <ul>
                <li class="filter-files-tab">
                    <?php esc_html_e('Filters', 'gamb-duplica-wp') ?>
                </li>
                <?php if (is_multisite()) { ?>
                    <li class="filter-mu-tab" >
                        <?php esc_html_e('Multisite', 'gamb-duplica-wp') ?>
                    </li>
                <?php } ?>
                <li class="archive-setup-tab">
                    <?php esc_html_e('Security', 'gamb-duplica-wp') ?>
                </li>
            </ul>

            <?php
            $tplMng->render('parts/packages/filters/section_filters_subtab_filters_files');

            if (is_multisite()) {
                $tplMng->render('admin_pages/packages/setup/archive-filter-mu-tab');
            }

            if ($isTemplateEdit && $template != null) {
                $setupParams = [
                    'secureOn'   => $template->installer_opts_secure_on,
                    'securePass' => $template->installerPassowrd,
                ];
            } else {
                $setupParams = [
                    'secureOn'   => ArchiveDescriptor::SECURE_MODE_NONE,
                    'securePass' => '',
                ];
            }
            $tplMng->render('parts/packages/filters/section_filters_subtab_setup', $setupParams);
            ?>
        </div>
    </div>
</div>

<div class="gamb-duplica-wp-error-container"></div>
<?php
    $alert1          = new GAMB_DUPLICA_WP_UI_Dialog();
    $alert1->title   = __('ERROR!', 'gamb-duplica-wp');
    $alert1->message = __('You can\'t exclude all sites.', 'gamb-duplica-wp');
    $alert1->initAlert();
?>
<script>
//INIT
jQuery(document).ready(function($) 
{
    //MU-Transfer buttons
    $('#mu-include-btn').click(function() {
        return !$('#mu-exclude option:selected').remove().appendTo('#mu-include');  
    });

    $('#mu-exclude-btn').click(function() {
        var include_all_count = $('#mu-include option').length;
        var include_selected_count = $('#mu-include option:selected').length;

        if(include_all_count > include_selected_count) {
            return !$('#mu-include option:selected').remove().appendTo('#mu-exclude');
        } else {
            <?php $alert1->showAlert(); ?>
        }
    });

});
</script>
