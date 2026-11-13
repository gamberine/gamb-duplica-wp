<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Controllers\ToolsPageController;
use gamb-duplica-wp\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

if (CapMng::can(CapMng::CAP_CREATE, false)) {
    $edit_template_url =  $ctrlMng::getMenuLink(
        $ctrlMng::TOOLS_SUBMENU_SLUG,
        ToolsPageController::L2_SLUG_TEMPLATE,
        null,
        ['inner_page' => 'edit']
    );

    $tipContent = __(
        'Create a new Template.',
        'gamb-duplica-wp'
    );
    ?>  
    <span
        data-tooltip="<?php echo esc_attr($tipContent); ?>"
    >
        <a  
            href="<?php echo esc_url($edit_template_url); ?>"
            id="dup-pro-create-new" 
            class="button primary small font-bold margin-0"
        >
            <?php esc_html_e('Add New', 'gamb-duplica-wp'); ?>
        </a>
    </span>
    <?php
}