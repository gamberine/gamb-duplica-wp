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
?>
<!-- ================================================================
DATABASE
================================================================ -->
<div class="scan-header">
    <i class="fas fa-database fa-fw fa-sm"></i>
    <?php esc_html_e("Database", 'gamb-duplica-wp'); ?>
    <div class="scan-header-details">
        <small style="font-weight:normal; font-size:12px">
        <?php if ($package->Database->Compatible) { ?>
            <i style="color:maroon"><?php esc_html_e('Compatibility Mode Enabled', 'gamb-duplica-wp'); ?></i>
        <?php } ?>
        </small>
        <div class="dup-scan-filter-status">
            <?php if ($package->Database->FilterOn) { ?>
                <i class="fa fa-filter fa-sm"></i>
                <?php esc_html_e('Enabled', 'gamb-duplica-wp'); ?>
            <?php } ?>
        </div>
        <div id="data-db-size1"></div>
        <i class="fa fa-question-circle data-size-help"
            data-tooltip-title="<?php esc_attr_e("Database Size:", 'gamb-duplica-wp'); ?>"
            data-tooltip="<?php
                esc_html_e(
                    'The database size represents only the included tables. The process for gathering the size uses the query SHOW TABLE STATUS. 
                    The overall size of the database file can impact the final size of the Backup.',
                    'gamb-duplica-wp'
                ); ?>"></i>
        <div class="dup-data-size-uncompressed"><?php esc_html_e("uncompressed", 'gamb-duplica-wp'); ?></div>
    </div>
</div>
<div id="dup-scan-db">
    <?php if ($package->isDBExcluded()) {
        $tplMng->render('admin_pages/packages/scan/items/database/excluded');
    } else {
        $tplMng->render('admin_pages/packages/scan/items/database/tables');

        if (GAMB_DUPLICA_WP_DB::getBuildMode() == GAMB_DUPLICA_WP_DB::BUILD_MODE_MYSQLDUMP) {
            $tplMng->render('admin_pages/packages/scan/items/database/mysqldump');
        }

        if (count($tplData['procedures']) > 0 || count($tplData['functions']) > 0) {
            $tplMng->render('admin_pages/packages/scan/items/database/procedures');
        }

        if (count($tplData['triggers'])) {
            $tplMng->render('admin_pages/packages/scan/items/database/triggers');
        }
    } ?>
</div>
