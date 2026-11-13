<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use gamb-duplica-wp\Package\Recovery\RecoveryStatus;
use gamb-duplica-wp\Views\ViewHelper;

/**
 * Variables
 *
 * @var array<string, mixed> $tplData
 * @var GAMB_DUPLICA_WP_Package_Template_Entity $template
 * @var GAMB_DUPLICA_WP_Schedule_Entity|null $schedule
 * @var bool $isList
 */

$isList   = $tplData['isList'] ?? false;
$template = $tplData['template'] ?? null;
$schedule = $tplData['schedule'] ?? null;

if (isset($schedule)) {
    $recoveryStatus = new RecoveryStatus($schedule);
} else {
    $recoveryStatus = new RecoveryStatus($template);
}

$isRecoveable          = $recoveryStatus->isRecoveable();
$templareRecoveryAlter = new GAMB_DUPLICA_WP_UI_Dialog();

if (!$isRecoveable) {
    $templareRecoveryAlter->title        = (
        isset($schedule) ?
            __('Schedule: Recovery Point', 'gamb-duplica-wp') :
            __('Template: Recovery Point', 'gamb-duplica-wp')
        );
    $templareRecoveryAlter->width        = 600;
    $templareRecoveryAlter->height       = 600;
    $templareRecoveryAlter->showButtons  = false;
    $templareRecoveryAlter->templatePath = 'parts/recovery/widget/template-filters-info';
    $templareRecoveryAlter->templateArgs = ['recoveryStatus' => $recoveryStatus];
    $templareRecoveryAlter->initAlert();
    ?>
    <script>
        jQuery(document).ready(function ($) {
            $('#dup-template-recoveable-info-<?php echo (int) $templareRecoveryAlter->getUniqueIdCounter(); ?>').click(function () {
    <?php $templareRecoveryAlter->showAlert(); ?>
            });
        });
    </script>
    <?php
}
?>
<span class="dup-template-recoveable-info-wrapper" >
    <?php
    if ($isRecoveable) {
        ?>
            <?php esc_html_e('Available', 'gamb-duplica-wp'); ?>
            <sup><?php ViewHelper::disasterIcon(); ?></sup>
        <?php
    } else {
        ?>
        <a href="javascript:void(0)"
            id="dup-template-recoveable-info-<?php echo (int) $templareRecoveryAlter->getUniqueIdCounter(); ?>" 
            class="dup-template-recoveable-info"><u><?php esc_html_e('Disabled', 'gamb-duplica-wp'); ?></u></a>
        <?php
    }

    if (!$isList) {
        ?>
        &nbsp;
        <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
            data-tooltip-title="<?php esc_attr_e("Recovery Status", 'gamb-duplica-wp'); ?>" 
            data-tooltip="<?php
            if (!isset($schedule)) {
                esc_html_e(
                    "The Recovery Status can be either 'Available' or 'Disabled'. 
                    An 'Available' status allows the templates archive to be restored through the recovery point wizard. 
                    A 'Disabled' status means the archive can still be used but just not ran as a rapid recovery point.",
                    'gamb-duplica-wp'
                );
            } else {
                esc_html_e(
                    "The Recovery Status can be either 'Available' or 'Disabled'. 
                    An 'Available' status allows the schedules archive to be restored through the recovery point wizard. 
                    A 'Disabled' status means the archive can still be used but just not ran as a rapid recovery point.",
                    'gamb-duplica-wp'
                );
            }
            ?>"
        ></i>
    <?php } ?>
</span>
