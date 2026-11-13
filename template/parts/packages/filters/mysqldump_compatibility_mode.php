<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

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

$dbbuild_mode = GAMB_DUPLICA_WP_DB::getBuildMode();

if ($dbbuild_mode != 'MYSQLDUMP') {
    return;
}
?>
<div class="dup-form-item margin-top-1">
    <label class="lbl-larger" >
        <?php esc_html_e("Compatibility Mode", 'gamb-duplica-wp') ?>:
        <?php
        $tipCont = __(
            'This is an advanced database backwards compatibility feature that should ONLY be used if having problems installing Backups. 
            If the database server version is lower than the version where the Backup was built then these options may help generate 
            a script that is more compliant with the older database server. It is recommended to try each option separately starting with mysql40.',
            'gamb-duplica-wp'
        );
        ?>
        <i class="fa-solid fa-question-circle fa-sm dark-gray-color" 
            title="<?php echo esc_attr($tipCont); ?>"
            aria-expanded="false"
        ></i>
    </label>
    <div>
        <?php if ($isTemplateEdit) { ?>
            <i>
            <?php
                printf(
                    esc_html_x(
                        'This option is not available as a template setting. 
                        It can only be used when creating a new Backup. Please see the full %1$sFAQ details%2$s',
                        '1 and 2 are opening and closing anchor/link tags',
                        'gamb-duplica-wp'
                    ),
                    '<a href="' . esc_url(GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . 'how-to-fix-database-write-issues') . '" target="_blank">',
                    '</a>'
                );
            ?>
            </i>
        <?php } else { ?>
            <div class="dup-form-horiz-opts">
                <span>
                    <input 
                        type="checkbox" 
                        name="dbcompat[]" 
                        id="dbcompat-mysql40" 
                        class="margin-0"
                        value="mysql40"
                    >
                    <label for="dbcompat-mysql40"><?php esc_html_e("mysql40", 'gamb-duplica-wp') ?></label> 
                </span>
                <span>
                    <input 
                        type="checkbox" 
                        name="dbcompat[]" 
                        id="dbcompat-no_table_options" 
                        value="no_table_options" 
                        class="margin-0"
                    >
                    <label for="dbcompat-no_table_options"><?php esc_html_e("no_table_options", 'gamb-duplica-wp') ?></label>
                </span>
                <span>
                    <input 
                        type="checkbox" 
                        name="dbcompat[]" 
                        id="dbcompat-no_key_options" 
                        value="no_key_options" 
                        class="margin-0"
                    >
                    <label for="dbcompat-no_key_options"><?php esc_html_e("no_key_options", 'gamb-duplica-wp') ?></label>
                </span>
                <span>
                    <input 
                        type="checkbox" 
                        name="dbcompat[]" 
                        id="dbcompat-no_field_options" 
                        value="no_field_options" 
                        class="margin-0"
                    >
                    <label for="dbcompat-no_field_options"><?php esc_html_e("no_field_options", 'gamb-duplica-wp') ?></label>
                </span>
            </div>
            <div class="dup-tabs-opts-help">
                <?php esc_html_e("Compatibility mode settings are not persistent.  They must be enabled with every new build.", 'gamb-duplica-wp'); ?>&nbsp;
                <a href="<?php echo esc_url(GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . 'how-to-fix-database-write-issues');?>" target="_blank">
                    [<?php esc_html_e('full overview', 'gamb-duplica-wp'); ?>]
                </a>
            </div>
        <?php } ?>
    </div>
</div>