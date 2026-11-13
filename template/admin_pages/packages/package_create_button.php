<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Controllers\PackagesPageController;
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
$blur          = $tplData['blur'];
$disableCreate = GAMB_DUPLICA_WP_Package::isPackageRunning() || GAMB_DUPLICA_WP_Package::isPackageCancelling();

if (CapMng::can(CapMng::CAP_CREATE, false) && !$blur) {
    $tipContent = __(
        'Create a new backup. If a backup is currently running then this button will be disabled.',
        'gamb-duplica-wp'
    );
    ?>  
    <span
        class="dup-new-package-wrapper"
        data-tooltip="<?php echo esc_attr($tipContent); ?>"
    >
        <a  
            href="<?php echo esc_url(PackagesPageController::getInstance()->getPackageBuildS1Url()); ?>"
            id="dup-pro-create-new" 
            class="button primary tiny font-bold margin-bottom-0 <?php echo $disableCreate ? 'disabled' : ''; ?>"
        >
            <b><?php esc_html_e('Add New', 'gamb-duplica-wp'); ?></b>
        </a>
    </span>
    <?php
}
