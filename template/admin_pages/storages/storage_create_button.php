<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Controllers\StoragePageController;
use gamb-duplica-wp\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var bool $blur
 */
$blur = $tplData['blur'];

if (CapMng::can(CapMng::CAP_STORAGE, false) && !$blur) {
    $edit_storage_url = $ctrlMng::getMenuLink(
        $ctrlMng::STORAGE_SUBMENU_SLUG,
        null,
        null,
        [
            $ctrlMng::QUERY_STRING_INNER_PAGE => StoragePageController::INNER_PAGE_EDIT,
        ]
    );

    $tipContent = __(
        'Create a new Storage.',
        'gamb-duplica-wp'
    );
    ?>  
    <span
        class="dup-new-package-wrapper"
        data-tooltip="<?php echo esc_attr($tipContent); ?>"
    >
        <a  
            href="<?php echo esc_url($edit_storage_url); ?>"
            id="dup-pro-create-new" 
            class="button primary tiny font-bold margin-bottom-0"
        >
            <?php esc_html_e('Add New', 'gamb-duplica-wp'); ?>
        </a>
    </span>
    <?php
}