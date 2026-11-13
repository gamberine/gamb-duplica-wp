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
 */
?>
<div id="migration-status-scan-item" class="scan-item">
    <div class='title' onclick="DupPro.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Import Status', 'gamb-duplica-wp');?></div>
        <div id="data-arc-status-migratepackage"></div>
    </div>
    <div class="info">
        <script id="hb-migrate-package-result" type="text/x-handlebars-template">
            <div class="container">
                <div class="data">
                    {{#if ARC.Status.PackageIsNotImportable}}
                        <hr>
                        <p>
                            <span class="maroon">
                            <?php esc_html_e("This Backup is not compatible with", 'gamb-duplica-wp'); ?>
                                <i data-tooltip-title="<?php esc_attr_e("Drag and Drop Import", 'gamb-duplica-wp'); ?>"
                                   data-tooltip="<?php esc_attr_e('The Drag and Drop import method is a way to migrate Backups.
                                                                    You can find it under gamb-duplica-wp Pro > Import.', 'gamb-duplica-wp'); ?>">
                                   <u><?php esc_html_e("Drag and Drop import", 'gamb-duplica-wp'); ?></u>.&nbsp;
                                </i>
                                <?php esc_html_e("However it can still be used to perform a database migration.", 'gamb-duplica-wp'); ?>
                            </span>

                            {{#if ARC.Status.IsDBOnly}}
                                <?php
                                esc_attr_e(
                                    "Database only Backups can only be installed via the installer.php file. 
                                    The Drag and Drop interface only processes Backups that have all WordPress core directories and all database tables.",
                                    'gamb-duplica-wp'
                                );
                                ?>
                            {{else}}
                                <?php esc_attr_e(
                                    "To make the Backup compatible with Drag and Drop import don't filter any tables or core directories.",
                                    'gamb-duplica-wp'
                                ); ?>
                            {{/if}}
                        </p>
                        {{#if ARC.Status.HasFilteredCoreFolders}}
                        <p>
                            <b><?php esc_attr_e("FILTERED CORE DIRS:", 'gamb-duplica-wp'); ?></b>
                        </p>
                        <ol>
                            {{#each ARC.FilteredCoreDirs as |dir|}}
                            <li>{{dir}} </li>
                            {{/each}}
                        </ol>
                        {{/if}}
                        {{#if ARC.Status.HasFilteredSiteTables}}
                            <b><?php esc_attr_e("FILTERED SITE TABLES:", 'gamb-duplica-wp'); ?></b>
                            <div class="dup-scan-files-migrae-status">
                                <ol>
                                    {{#each DB.FilteredTables as |table|}}
                                    <li>{{table}} </li>
                                    {{/each}}
                                </ol>
                            </div>
                        {{/if}}
                    {{else}}
                        <?php esc_html_e("The Backup you are about to create is compatible with Drag and Drop import.", 'gamb-duplica-wp'); ?>
                    {{/if}}
                </div>
            </div>
        </script>
        <div id="migrate-package-result"></div>
    </div>
</div>
