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

$package = $tplData['package'];
$global  = GAMB_DUPLICA_WP_Global_Entity::getInstance();
?>

<!-- ================================================================
ARCHIVE
================================================================ -->
<div class="details-title">
    <i class="far fa-file-archive fa-sm fa-fw"></i>&nbsp;<?php esc_html_e('Archive', 'gamb-duplica-wp'); ?>
    <sup class="dup-small-ext-type">
        <?php if ($package->Installer->isSecure()) : ?>
        <i class="fas fa-lock fa-fw fa-sm" title="<?php esc_html_e('Requires Password to Extract', 'gamb-duplica-wp'); ?>"></i>&nbsp;
        <?php endif; ?>
        <?php echo esc_html($global->get_archive_extension_type()); ?>
    </sup>
</div>
<div class="scan-header scan-item-first">
    <i class="fas fa-folder-open fa-sm"></i>
    <?php esc_html_e("Files", 'gamb-duplica-wp'); ?>
    <div class="scan-header-details">
        <div class="dup-scan-filter-status">
            <?php if ($package->isDbOnly()) { ?>
                <i class="fa fa-filter fa-sm"></i> <?php esc_html_e('Database Only', 'gamb-duplica-wp'); ?>
            <?php } elseif ($package->Archive->FilterOn) { ?>
                <i class="fa fa-filter fa-sm"></i> <?php esc_html_e('Enabled', 'gamb-duplica-wp'); ?>
            <?php } ?>
        </div>

        <div id="data-arc-size1"></div>
        <i class="fa fa-question-circle data-size-help"
            data-tooltip-title="<?php esc_attr_e("File Size:", 'gamb-duplica-wp'); ?>"
            data-tooltip="<?php
                esc_html_e(
                    'The files size represents only the included files before compression is applied. 
                    It does not include the size of the database script and in most cases the Backup size 
                    once completed will be smaller than this number unless shell execution zip with no compression is enabled.',
                    'gamb-duplica-wp'
                ); ?>"></i>
        <div class="dup-data-size-uncompressed"><?php esc_html_e("uncompressed", 'gamb-duplica-wp'); ?></div>
    </div>
</div>
<?php if ($package->isDBOnly()) {
    $tplMng->render('admin_pages/packages/scan/items/archive/files_db_only');
} elseif ($global->skip_archive_scan) {
    $tplMng->render('admin_pages/packages/scan/items/archive/files_skip_scan');
} else {
    // SIZE CHECKS
    $tplMng->render('admin_pages/packages/scan/items/archive/files');
    // ADDON SITES
    $tplMng->render('admin_pages/packages/scan/items/archive/addons');
    // UNREADABLE FILES
    $tplMng->render('admin_pages/packages/scan/items/archive/unreadable');
} ?>
<?php if (is_multisite()) {
    $tplMng->render('admin_pages/packages/scan/items/archive/multisite');
} ?>
