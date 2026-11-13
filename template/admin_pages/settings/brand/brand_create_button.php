<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Controllers\SettingsPageController;
use gamb-duplica-wp\Core\Controllers\ControllersManager;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$brandNewUrl = ControllersManager::getCurrentLink(
    [
        ControllersManager::QUERY_STRING_INNER_PAGE => SettingsPageController::BRAND_INNER_PAGE_EDIT,
        'action'                                    => 'new',
    ]
);

$tipContent = __(
    'Create a new Brand.',
    'gamb-duplica-wp'
);
?>  
<span
    data-tooltip="<?php echo esc_attr($tipContent); ?>"
>
    <a  
        href="<?php echo esc_url($brandNewUrl); ?>"
        id="dup-pro-create-new" 
        class="button primary small font-bold margin-0"
    >
        <?php esc_html_e('Add New', 'gamb-duplica-wp'); ?>
    </a>
</span>
