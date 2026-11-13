<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var GAMB_DUPLICA_WP_Package $package
 */
$package      = $tplData['package'];
$global       = GAMB_DUPLICA_WP_Global_Entity::getInstance();
$totalSizeMax = ($global->getBuildMode() == GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive)
        ? gamb-duplica-wp_PRO_SCAN_SITE_ZIP_ARCHIVE_WARNING_SIZE
        : gamb-duplica-wp_PRO_SCAN_SITE_WARNING_SIZE;
?>
<div class="scan-item">
    <div class='title' onclick="DupPro.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Size Checks', 'gamb-duplica-wp');?></div>
        <div id="data-arc-status-size"></div>
    </div>
    <div class="info" id="scan-item-file-size">
        <b><?php esc_html_e('Size', 'gamb-duplica-wp'); ?>:</b> <span id="data-arc-size2"></span>  &nbsp; | &nbsp;
        <b><?php esc_html_e('Files', 'gamb-duplica-wp'); ?>:</b> <span id="data-arc-files"></span>  &nbsp; | &nbsp;
        <b><?php esc_html_e('Directories ', 'gamb-duplica-wp'); ?>:</b> <span id="data-arc-dirs"></span>   &nbsp; | &nbsp;
        <b><?php esc_html_e('Total', 'gamb-duplica-wp'); ?>:</b> <span id="data-arc-fullcount"></span>
        <br/>
        <?php echo wp_kses(
            __('Compressing larger sites on <i>some budget hosts</i> may cause timeouts.', 'gamb-duplica-wp'),
            ['i' => []]
        ); ?>
        <i>
            <a href="javascipt:void(0)" onclick="jQuery('#size-more-details').toggle(100); return false;">
                [<?php esc_html_e('more details...', 'gamb-duplica-wp'); ?>]
            </a>
        </i>
        <div id="size-more-details">
            <b><?php esc_html_e('Overview', 'gamb-duplica-wp'); ?>:</b><br/>
            <?php echo wp_kses(
                sprintf(
                    __(
                        'This notice is triggered at <b>%s</b> and can be ignored on most hosts. 
                        If the build process hangs or is unable to complete then this host has strict processing limits.  
                        Below are some options you can take to overcome constraints setup on this host.',
                        'gamb-duplica-wp'
                    ),
                    GAMB_DUPLICA_WP_U::byteSize($totalSizeMax)
                ),
                ['b' => []]
            ); ?>
            <br/><br/>
            <b><?php esc_html_e('Timeout Options', 'gamb-duplica-wp'); ?>: </b><br/>
            <ul>
                <li><?php esc_html_e('Apply the "Quick Filters" below or click the back button to apply on previous page.', 'gamb-duplica-wp'); ?> </li>
                <li>
                    <?php esc_html_e('See the FAQ link to adjust this hosts timeout limits: ', 'gamb-duplica-wp'); ?>
                    &nbsp;<a href="<?php echo esc_html(gamb-duplica-wp_PRO_gamb-duplica-wp_DOCS_URL); ?>how-to-handle-server-timeout-issues" target="_blank">
                        <?php esc_html_e('What can I try for Timeout Issues?', 'gamb-duplica-wp'); ?>
                    </a>
                </li>
            </ul>
        </div>
        <div id="hb-files-large-result" class="dup-tree-section hb-files-style">
            <div class="container">
                <div class="hdrs">
                    <span style="font-weight:bold">
                        <?php esc_html_e('Quick Filters', 'gamb-duplica-wp'); ?>
                        <sup>
                            <i 
                                class="fa-solid fa-question-circle fa-sm dark-gray-color"
                                data-tooltip-title="<?php esc_attr_e("Large Files", 'gamb-duplica-wp'); ?>"
                                data-tooltip="<?php
                                echo wp_kses(
                                    sprintf(
                                        __(
                                            'Files over %1$s are listed below. Larger files such as movies or zipped content 
                                            can cause timeout issues on some budget hosts. If you are having issues creating 
                                            a Backup try excluding the directory paths below or go back to Step 1 and add them.
                                            <br><br><b>Right click on tree node to open the bulk actions menu</b>',
                                            'gamb-duplica-wp'
                                        ),
                                        GAMB_DUPLICA_WP_U::byteSize(gamb-duplica-wp_PRO_SCAN_WARN_FILE_SIZE)
                                    ),
                                    [
                                        'br' => [],
                                        'b'  => [],
                                    ]
                                ); ?>">
                            </i>
                        </sup>
                    </span>
                    <div class='hdrs-up-down'>
                        <i
                            class="fa fa-caret-up fa-lg dup-nav-toggle"
                            onclick="DupPro.Pack.toggleAllDirPath(this, 'hide')"
                            title="<?php esc_attr_e("Hide All", 'gamb-duplica-wp'); ?>">
                        </i>
                        <i
                            class="fa fa-caret-down fa-lg dup-nav-toggle"
                            onclick="DupPro.Pack.toggleAllDirPath(this, 'show')"
                            title="<?php esc_attr_e("Show All", 'gamb-duplica-wp'); ?>">
                        </i>
                    </div>
                </div>
                <div class="tree-nav-bar" >
                    <div class="container">
                    <button 
                        type="button" 
                        id="hb-files-large-tree-full-load" 
                        class="tree-full-load-button dup-tree-show-all button gray hollow small margin-bottom-0" 
                    >
                        <?php esc_html_e('Show All', 'gamb-duplica-wp') ?>
                    </button>
                    <span class="size"><?php esc_html_e('Size', 'gamb-duplica-wp') ?></span> 
                    <span class="nodes"><?php esc_html_e('Nodes', 'gamb-duplica-wp') ?></span>
                    </div>
                </div>
                <div class="data">
                    <div id="hb-files-large-jstree" class="dup-tree-main-wrapper"></div>
                </div>
            </div>
            <div class="apply-btn">
                <div class="apply-warn">
                     <?php esc_html_e('*Checking a directory will exclude all items in that path recursively.', 'gamb-duplica-wp'); ?>
                </div>
                <button 
                    type="button" 
                    class="button gray hollow tiny gamb-duplica-wp-quick-filter-btn" 
                    disabled="disabled" onclick="DupPro.Pack.applyFilters(this, 'large')"
                >
                    <i class="fa fa-filter fa-sm"></i> <?php esc_html_e('Add Filters &amp; Rescan', 'gamb-duplica-wp');?>
                </button>
                <button 
                    type="button" 
                    class="button gray hollow tiny" 
                    onclick="DupPro.Pack.showPathsDlg('large')" 
                    title="<?php esc_attr_e('Copy Paths to Clipboard', 'gamb-duplica-wp');?>"
                >
                    <i class="fa far fa-clipboard" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- =======================
DIALOG: PATHS COPY & PASTE -->
<div id="dup-archive-paths" style="display:none">
    <b><i class="fa fa-folder"></i> <?php esc_html_e('Directories', 'gamb-duplica-wp');?></b>
    <div class="copy-butto float-right">
        <button type="button" class="button secondary hollow tiny" onclick="DupPro.Pack.copyText(this, '.arc-paths-dlg textarea.path-dirs')">
            <i class="fa far fa-clipboard"></i> <?php esc_html_e('Click to Copy', 'gamb-duplica-wp');?>
        </button>
    </div>
    <textarea class="path-dirs"></textarea>
    <b><i class="fa fa-files fa-sm"></i> <?php esc_html_e('Files', 'gamb-duplica-wp');?></b>
    <div class="copy-button float-right">
        <button type="button" class="button secondary hollow tiny" onclick="DupPro.Pack.copyText(this, '.arc-paths-dlg textarea.path-files')">
            <i class="fa far fa-clipboard"></i> <?php esc_html_e('Click to Copy', 'gamb-duplica-wp');?>
        </button>
    </div>
    <textarea class="path-files"></textarea>
    <small><?php esc_html_e('Copy the paths above and apply them as needed on Step 1 &gt; Archive &gt; Files section.', 'gamb-duplica-wp');?></small>
</div>
