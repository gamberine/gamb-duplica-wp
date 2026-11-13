<?php

use GambDuplicaWP\Controllers\ToolsPageController;
use GambDuplicaWP\Core\Views\TplMng;
use GambDuplicaWP\Package\Recovery\RecoveryStatus;
use GambDuplicaWP\Views\ViewHelper;

defined('ABSPATH') || defined('DUPXABSPATH') || exit;


/**
 * Variables
 *
 * @var array<string, mixed> $tplData
 * @var RecoveryStatus $recoveryStatus
 */
$recoveryStatus = $tplData['recoveryStatus'];

if ($recoveryStatus->getType() == RecoveryStatus::TYPE_SCHEDULE) {
    /** @var GAMB_DUPLICA_WP_Schedule_Entity */
    $schedule = $recoveryStatus->getObject();
    if (($template       = $schedule->getTemplate()) === false) {
        $template       = new GAMB_DUPLICA_WP_Package_Template_Entity();
        $template->name = __('Template not found', 'gamb-duplica-wp');
    }
    $tooltipContent = esc_attr__(
        'A Schedule is not required to have a recovery point. For example if a schedule is backing up
        only a database then the recovery will always be disabled and may be desirable.',
        'gamb-duplica-wp'
    );
} else {
    $schedule = null;
    /** @var GAMB_DUPLICA_WP_Package_Template_Entity */
    $template       = $recoveryStatus->getObject();
    $tooltipContent = __(
        'A Template is not required to have a recovery point. For example if backing up only a database 
        then the recovery will always be disabled and may be desirable.',
        'gamb-duplica-wp'
    );
}
?>
<div class="dup-recover-dlg-title">
<b><?php ViewHelper::disasterIcon() ?>&nbsp;<?php esc_html_e('Status', 'gamb-duplica-wp'); ?>: </b>
    <?php esc_html_e('Disabled', 'gamb-duplica-wp'); ?>
    <sup>
        <i class="fas fa-question-circle fa-xs"
           data-tooltip-title="<?php esc_html_e('Recovery Status', 'gamb-duplica-wp'); ?>"
           data-tooltip="<?php echo esc_attr($tooltipContent); ?>">
        </i>
    </sup>
</div>

<div class="dup-recover-dlg-subinfo">
    <table>
        <?php if ($recoveryStatus->getType() == RecoveryStatus::TYPE_SCHEDULE) { ?>
            <tr>
                <td><b><?php esc_html_e("Schedule", 'gamb-duplica-wp'); ?>:</b></td>
                <td> <?php echo esc_html($schedule->name); ?></td>
            </tr>
            <tr>
                <td> <b><?php esc_html_e("Template", 'gamb-duplica-wp'); ?>:</b></td>
                <td>
                    <a href="<?php echo esc_url(ToolsPageController::getTemplateEditURL($template->getId())); ?>" >
                        <?php echo esc_html($template->name); ?>
                    </a>
                </td>
            </tr>
        <?php } else { ?>
            <tr>
                <td> <b><?php esc_html_e("Template", 'gamb-duplica-wp'); ?>:</b> </td>
                <td><?php echo esc_html($template->name); ?></td>
            </tr>
            <tr>
                <td><b><?php esc_html_e('Notes', 'gamb-duplica-wp'); ?>:</b>&nbsp; </td>
                <td><?php  echo (strlen($template->notes))  ? esc_html($template->notes) : esc_html__("- no notes -", 'gamb-duplica-wp'); ?></td>
            </tr>
        <?php } ?>
    </table>
</div>
<?php
TplMng::getInstance()->render(
    'parts/recovery/exclude_data_box',
    ['recoverStatus' => $recoveryStatus]
);
