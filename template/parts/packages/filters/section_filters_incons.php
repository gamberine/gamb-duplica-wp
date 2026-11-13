<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

?>
<span class="dup-archive-filters-icons">
    <span id="dup-archive-filter-file-icon" title="<?php esc_attr_e('Folder/File Filters Enabled', 'gamb-duplica-wp'); ?>">
        <i class="fa-solid fa-folder-minus fa-sm primary-color"></i>
    </span>
    <span id="dup-archive-filter-db-icon" title="<?php esc_attr_e('Database Filters Enabled', 'gamb-duplica-wp'); ?>">
        <i class="fa-solid fa-table fa-sm primary-color"></i>
    </span>
    <span id="dup-archive-db-only-icon" title="<?php esc_attr_e('Backup Only the Database', 'gamb-duplica-wp'); ?>">
        <i class="fa-solid fa-database fa-sm primary-color"></i>
    </span>
    <span id="dup-archive-media-only-icon" title="<?php esc_attr_e('Backup Only Media files', 'gamb-duplica-wp'); ?>">
        <i class="fa-solid fa-file-image fa-sm primary-color"></i>
    </span>
    <span id="dpro-install-secure-lock-icon" title="<?php esc_attr_e('Backup Password Protection is On', 'gamb-duplica-wp'); ?>" >
        <i class="fa-solid fa-lock fa-sm primary-color"></i>
    </span>
</span>