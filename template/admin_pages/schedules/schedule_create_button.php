<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Controllers\SchedulePageController;
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

if (CapMng::can(CapMng::CAP_SCHEDULE, false) && !$blur) {
    $scheduleEditBaseURL = SchedulePageController::getInstance()->getEditBaseUrl();
    $tipContent          = __(
        'Create a new Scheduled Backup.',
        'gamb-duplica-wp'
    );
    ?>  
    <span
        class="dup-new-package-wrapper"
        data-tooltip="<?php echo esc_attr($tipContent); ?>"
    >
        <a  
            href="<?php echo esc_url($scheduleEditBaseURL); ?>"
            id="dup-pro-create-new" 
            class="button primary tiny font-bold margin-bottom-0"
        >
           <?php esc_html_e('Add New', 'gamb-duplica-wp'); ?>
        </a>
    </span>
     <?php
}