<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Utils\ZipArchiveExtended;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$global                = GAMB_DUPLICA_WP_Global_Entity::getInstance();
$isZipArchiveAvailable = ZipArchiveExtended::isPhpZipAvailable();
$isShellZipAvailable   = (GAMB_DUPLICA_WP_Zip_U::getShellExecZipPath() != null);
?>

<h3 class="title">
    <?php esc_html_e("Archive", 'gamb-duplica-wp') ?>
</h3>
<hr size="1" />

<label class="lbl-larger" >
    <?php esc_html_e("Compression", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <input 
        type="radio" 
        name="archive_compression" 
        id="archive_compression_off" 
        value="0"
        class="margin-0"
        <?php checked($global->archive_compression, false); ?> 
    >
    <label for="archive_compression_off">
        <?php esc_html_e("Off", 'gamb-duplica-wp'); ?>
    </label> &nbsp;
    <input 
        type="radio" 
        name="archive_compression"  
        id="archive_compression_on" 
        value="1"
        class="margin-0"
        <?php checked($global->archive_compression); ?>  
    >
    <label for="archive_compression_on">
        <?php esc_html_e("On", 'gamb-duplica-wp'); ?>
    </label>
    <?php $tipContent = __(
        'This setting controls archive compression. The setting apply to all Archive Engine formats.
        For ZipArchive this setting only works on PHP 7.0 or higher.',
        'gamb-duplica-wp'
    ); ?>&nbsp;
    <i style="margin-right:7px;" class="fa-solid fa-question-circle fa-sm dark-gray-color"
        data-tooltip-title="<?php esc_attr_e("Archive Compression", 'gamb-duplica-wp'); ?>"
        data-tooltip="<?php echo esc_attr($tipContent); ?>">
    </i>
</div>

<label class="lbl-larger" >
    <?php esc_html_e("Archive Engine", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
<div class="engine-radio">
    <input
        onclick="DupPro.UI.SetArchiveOptionStates();"
        type="radio"
        name="archive_build_mode" id="archive_build_mode3"
        class="margin-0"
        value="<?php echo (int) GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive; ?>"
        <?php checked($global->getBuildMode() == GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive); ?>
        <?php disabled(!$global->isBuildModeAvailable(GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive)) ?>
    >
    <label for="archive_build_mode3"><?php esc_html_e("DupArchive", 'gamb-duplica-wp'); ?></label> &nbsp; &nbsp;
    </div>
    <div class="engine-radio <?php echo ($isShellZipAvailable) ? '' : 'engine-radio-disabled'; ?>">
    <input
        onclick="DupPro.UI.SetArchiveOptionStates();"
        type="radio"
        name="archive_build_mode"
        id="archive_build_mode1"
        class="margin-0"
        value="<?php echo (int) GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec; ?>"
        <?php checked($global->getBuildMode() == GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec); ?>
        <?php disabled(!$global->isBuildModeAvailable(GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec)) ?>
    >
    <label for="archive_build_mode1"><?php esc_html_e("Shell Zip", 'gamb-duplica-wp'); ?></label>
    </div>
    <div class="engine-radio">
    <input
        onclick="DupPro.UI.SetArchiveOptionStates();"
        type="radio"
        name="archive_build_mode"
        id="archive_build_mode2"
        class="margin-0"
        value="<?php echo (int) GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive; ?>"
        <?php checked($global->getBuildMode() == GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive); ?>
        <?php disabled(!$global->isBuildModeAvailable(GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive)) ?>
    >
    <label for="archive_build_mode2"><?php esc_html_e("ZipArchive", 'gamb-duplica-wp'); ?></label>
    </div>

    <br style="clear:both"/>

    <!-- DUPARCHIVE -->
    <div class="engine-sub-opts" id="engine-details-3" style="display:none">
    <?php
    esc_html_e('This option creates a custom gamb-duplica-wp Archive Format (.daf) archive file.', 'gamb-duplica-wp');
    echo '<br/>  ';
    esc_html_e('This option is fully multi-threaded and recommended for large sites or throttled servers.', 'gamb-duplica-wp');
    echo '<br/>  ';
    printf(
        '%s <a href="' . esc_url(GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . 'how-to-work-with-daf-files-and-the-duparchive-extraction-tool')
        . '" target="_blank">%s</a> ',
        esc_html__('For details on how to use and manually extract the DAF format please see the ', 'gamb-duplica-wp'),
        esc_html__('online documentation.', 'gamb-duplica-wp')
    );
    ?>
    </div>

    <!-- SHELL EXEC  -->
    <div class="engine-sub-opts" id="engine-details-1" style="display:none">
    <?php
    $tplMng->render(
        'parts/settings/shellZipMessage',
        ['hasShellZip' => $isShellZipAvailable]
    );
    ?>
    </div>

    <!-- ZIP ARCHIVE -->
    <div class="engine-sub-opts" id="engine-details-2" style="display:none;">
    <div class="margin-bottom-1" >
        <span><?php esc_html_e("Process Mode", 'gamb-duplica-wp'); ?></span>&nbsp;
        <select  name="ziparchive_mode" id="ziparchive_mode"  onchange="DupPro.UI.setZipArchiveMode();" class="inline-display width-medium margin-0" >
            <option <?php selected($global->ziparchive_mode, GAMB_DUPLICA_WP_ZipArchive_Mode::Multithreaded); ?>
                value="<?php echo (int) GAMB_DUPLICA_WP_ZipArchive_Mode::Multithreaded ?>">
                <?php esc_html_e("Multi-Threaded", 'gamb-duplica-wp'); ?>
            </option>
            <option <?php selected($global->ziparchive_mode == GAMB_DUPLICA_WP_ZipArchive_Mode::SingleThread); ?>
                value="<?php echo (int) GAMB_DUPLICA_WP_ZipArchive_Mode::SingleThread ?>">
                <?php esc_html_e("Single-Threaded", 'gamb-duplica-wp'); ?>
            </option>
        </select>&nbsp;
        <i style="margin-right:7px;" class="fa-solid fa-question-circle fa-sm dark-gray-color"
            data-tooltip-title="<?php esc_attr_e("PHP ZipArchive Mode", 'gamb-duplica-wp'); ?>"
            data-tooltip="<?php
            esc_attr_e(
                'Single-Threaded mode attempts to create the entire archive in one request.  
                Multi-Threaded mode allows the archive to be chunked over multiple requests. 
                Multi-Threaded mode is typically slower but much more reliable especially for larger sites.',
                'gamb-duplica-wp'
            );
            ?>"></i>
    </div>

    <div id="dpro-ziparchive-mode-st">
        <input type="checkbox" id="ziparchive_validation" name="ziparchive_validation" class="margin-0"
            <?php checked($global->ziparchive_validation); ?>
        >
        <label for="ziparchive_validation">Enable file validation</label>
    </div>

    <div id="dpro-ziparchive-mode-mt">
        <span><?php esc_html_e("Buffer Size", 'gamb-duplica-wp'); ?></span>&nbsp;
        <input 
            maxlength="4"
            class="inline-display width-small margin-0"
            data-parsley-required data-parsley-errors-container="#ziparchive_chunk_size_error_container"
            data-parsley-min="5" data-parsley-type="number"
            type="text" name="ziparchive_chunk_size_in_mb" id="ziparchive_chunk_size_in_mb"
            value="<?php echo (int) $global->ziparchive_chunk_size_in_mb; ?>"
        >
        <?php esc_html_e('MB', 'gamb-duplica-wp'); ?>
        <?php
        $toolTipContent = __(
            'Buffer size only applies to multi-threaded requests and indicates how large an archive will get before a close is registered. 
            Higher values are faster but can be more unstable based on the hosts max_execution_time.',
            'gamb-duplica-wp'
        );
        ?>
        <i style="margin-right:7px" class="fa-solid fa-question-circle fa-sm dark-gray-color"
            data-tooltip-title="<?php esc_attr_e("PHP ZipArchive Buffer", 'gamb-duplica-wp'); ?>"
            data-tooltip="<?php echo esc_attr($toolTipContent); ?>"
        >
        </i>
        <div id="ziparchive_chunk_size_error_container" class="gamb-duplica-wp-error-container"></div>
    </div>
    </div>
</div>