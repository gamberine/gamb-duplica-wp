<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

use GambDuplicaWP\Controllers\SettingsPageController;

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var GAMB_DUPLICA_WP_Package $package
 */
$package            = $tplData['package'];
$settingsPackageUrl = SettingsPageController::getInstance()->getMenuLink(SettingsPageController::L2_SLUG_PACKAGE);

/** @var wpdb $wpdb */
global $wpdb;
?>
<div class="scan-item">
    <div class='title' onclick="DupPro.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Overview', 'gamb-duplica-wp');?></div>
        <div id="data-db-status-size1"></div>
    </div>
    <div class="info">
        <b> <?php esc_html_e('TOTAL SIZE', 'gamb-duplica-wp'); ?> &nbsp; &#8667; &nbsp; </b>
        <b><?php esc_html_e('Size', 'gamb-duplica-wp'); ?>:</b> <span id="data-db-size2"></span> &nbsp; | &nbsp;
        <b><?php esc_html_e('Tables', 'gamb-duplica-wp'); ?>:</b> <span id="data-db-tablecount"></span> &nbsp; | &nbsp;
        <b><?php esc_html_e('Records', 'gamb-duplica-wp'); ?>:</b> <span id="data-db-rows"></span> <br/>
        <?php echo wp_kses(
            sprintf(
                __(
                    'Total size and row count are approximate values. The thresholds that trigger warnings are
                    <i>%1$s OR %2$s records</i> total for the entire database. Large databases take time to process
                    and can cause issues with server timeout and memory settings on some budget hosts. If your server
                    supports popen or exec and mysqldump you can try to enable Shell Execution from the settings menu.',
                    'gamb-duplica-wp'
                ),
                GAMB_DUPLICA_WP_U::byteSize(GambDuplicaWP_PRO_SCAN_DB_ALL_SIZE),
                number_format(GambDuplicaWP_PRO_SCAN_DB_ALL_ROWS)
            ),
            ['i' => []]
        ); ?>
        <br/>
        <br/>
        <hr size="1" />
        <b><?php esc_html_e('TABLE DETAILS:', 'gamb-duplica-wp'); ?></b><br/>
        <?php
        echo wp_kses(
            sprintf(
                __(
                    'The notices for tables are <i>%1$s, %2$s records or names with upper-case characters</i>. 
                    Individual tables will not trigger a notice message, but can help narrow down issues if they occur later on.',
                    'gamb-duplica-wp'
                ),
                GAMB_DUPLICA_WP_U::byteSize(GambDuplicaWP_PRO_SCAN_DB_TBL_SIZE),
                number_format(GambDuplicaWP_PRO_SCAN_DB_TBL_ROWS)
            ),
            ['i' => []]
        );
        ?>
        <p>
            <b><?php echo esc_html(sprintf(__('Exclude all tables without prefix "%s"', 'gamb-duplica-wp'), $tplData['prefix'])); ?>:</b>&nbsp;
            <i class="maroon" >
            <?php echo ($tplData['prefixFilter'] ?
                    esc_html_e('Enabled', 'gamb-duplica-wp') :
                    esc_html_e('Disabled', 'gamb-duplica-wp')
                ); ?>
            </i><br>
            <?php if (is_multisite()) { ?>
                <b><?php esc_html_e('Exclude not existing subsite filter', 'gamb-duplica-wp'); ?>:</b>&nbsp;
                <i class="red" >
                    <?php echo ($tplData['prefixSubFilter'] ?
                        esc_html_e('Enabled', 'gamb-duplica-wp') :
                        esc_html_e('Disabled', 'gamb-duplica-wp')
                    ); ?>
                </i>
            <?php } ?>
        </p>
        <div id="dup-scan-db-info">
            <div id="data-db-tablelist"></div>
        </div>
        <br/>
        <hr size="1" />
        <b><?php esc_html_e('RECOMMENDATIONS:', 'gamb-duplica-wp'); ?></b><br/>
        <i>
            <?php esc_html_e(
                'The following recommendations are not needed unless you are having issues building or installing the Backup.',
                'gamb-duplica-wp'
            ); ?>
        </i>
        <br/>
        <div style="padding:5px">
        <?php echo wp_kses(
            sprintf(
                _x(
                    '1. Run a %1$srepair and optimization%2$s on the table to improve the overall size and performance.',
                    '1$s and 2$s represent opening and closing anchor tags',
                    'gamb-duplica-wp'
                ),
                '<a href="' . admin_url('maint/repair.php') . '" target="_blank">',
                '</a>'
            ),
            [
                'a' => [
                    'href'   => [],
                    'target' => [],
                ],
            ]
        ); ?>
        <br/><br/>
        <?php esc_html_e(
            '2. Remove post revisions and stale data from tables.  Tables such as logs, statistical or other non-critical data should be cleared.',
            'gamb-duplica-wp'
        ); ?>
        <br/><br/>
        <?php echo wp_kses(
            sprintf(
                _x(
                    '3. %1$sEnable mysqldump%2$s if this host supports the option.',
                    '1$s and 2$s represent opening and closing anchor tags',
                    'gamb-duplica-wp'
                ),
                '<a href="' . esc_url($settingsPackageUrl) . '" target="_blank">',
                '</a>'
            ),
            [
                'a' => [
                    'href'   => [],
                    'target' => [],
                ],
            ]
        ); ?>
        <br/><br/>
        <?php echo wp_kses(
            sprintf(
                _x(
                    '4. Restoring mixed-case tables can cause problems on some servers. If you experience a 
                    problem installing the backup change the %1$s system variable on the destination site\'s MySQL Server.',
                    '1$s represents an anchor tag with the variable name',
                    'gamb-duplica-wp'
                ),
                '<a href="http://dev.mysql.com/doc/refman/5.7/en/server-system-variables.html#sysvar_lower_case_table_names" target="_blank">
                    lower_case_table_names</a>'
            ),
            [
                'a' => [
                    'href'   => [],
                    'target' => [],
                ],
            ]
        ); ?>
        </div>
    </div>
</div>
