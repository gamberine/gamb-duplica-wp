<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Controllers\SettingsPageController;

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

$dbbuild_mode       = GAMB_DUPLICA_WP_DB::getBuildMode();
$settingsPackageUrl = SettingsPageController::getInstance()->getMenuLink(SettingsPageController::L2_SLUG_PACKAGE);

if ($isTemplateEdit && $template != null) {
    $tableList             = explode(',', $template->database_filter_tables);
    $tableListFilterParams = [
        'dbFilterOn'        => $template->database_filter_on,
        'dbPrefixFilter'    => $template->databasePrefixFilter,
        'dbPrefixSubFilter' => $template->databasePrefixSubFilter,
        'tablesSlected'     => $tableList,
    ];
} else {
    $tableListFilterParams = [
        'dbFilterOn'        => false,
        'dbPrefixFilter'    => '',
        'dbPrefixSubFilter' => '',
        'tablesSlected'     => [],
    ];
}

?>
<div class="filter-db-tab-content">
    <hr>
    <div class="db-only-message margin-bottom-1">
        <label class="lbl-larger" >
            <?php esc_html_e("Database Only", 'gamb-duplica-wp') ?>
        </label>
        <div class="input">
            <?php
            esc_html_e(
                'This advanced option excludes all files from the archive. 
                Only the database and a copy of the installer.php will be included in the Backup file.',
                'gamb-duplica-wp'
            );
            ?><br>
            <?php
            esc_html_e(
                'The option can be used for backing up and moving only the database.',
                'gamb-duplica-wp'
            );
            ?><br>
            <?php
            printf(
                esc_html_x(
                    'When installing a database only backup please visit the %1$sdatabase only quick start%2$s',
                    '%1$s and %2$s are opening and closing anchor tags',
                    'gamb-duplica-wp'
                ),
                '<a href="' . esc_url(GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . 'database-install') . '" target="_blank">',
                '</a>'
            );
            ?>
        </div>
    </div>
    <div class="margin-bottom-1" >
        <?php $tplMng->render('parts/packages/filters/tables_list_filter', $tableListFilterParams); ?>
    </div>
    <div class="dup-form-item">
        <label class="lbl-larger" >
            <?php esc_html_e("SQL Mode", 'gamb-duplica-wp') ?>
        </label>
        <span class="input">
            <a href="<?php echo esc_url($settingsPackageUrl); ?>" target="settings">
                <?php echo esc_html($dbbuild_mode); ?>
            </a>
        </span>
    </div>
    <?php $tplMng->render('parts/packages/filters/mysqldump_compatibility_mode'); ?>
</div>

