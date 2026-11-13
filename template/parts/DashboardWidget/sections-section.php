<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Controllers\ToolsPageController;
use GambDuplicaWP\Core\CapMng;
use GambDuplicaWP\Core\Controllers\ControllersManager;
use GambDuplicaWP\Package\Recovery\RecoveryPackage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$templatesURL = ControllersManager::getMenuLink(
    ControllersManager::TOOLS_SUBMENU_SLUG,
    ToolsPageController::L2_SLUG_TEMPLATE
);
$recoveryURl  = ControllersManager::getMenuLink(
    ControllersManager::TOOLS_SUBMENU_SLUG,
    ToolsPageController::L2_SLUG_RECOVERY
);

if (
    !CapMng::can(CapMng::CAP_SCHEDULE, false) &&
    !CapMng::can(CapMng::CAP_STORAGE, false) &&
    !CapMng::can(CapMng::CAP_CREATE, false) &&
    !CapMng::can(CapMng::CAP_BACKUP_RESTORE, false)
) {
    return;
}

?>
<hr class="separator" >
<div class="dup-section-sections">
    <ul>
        <?php if (CapMng::can(CapMng::CAP_SCHEDULE, false)) { ?>
        <li class="dup-flex-content">
            <span class="dup-section-label-fixed-width" >
                <span class="dashicons dashicons-update gary"></span>
                <a href="<?php echo esc_url(ControllersManager::getMenuLink(ControllersManager::SCHEDULES_SUBMENU_SLUG)); ?>"><?php
                    echo esc_html(sprintf(
                        _n(
                            '%s Schedule',
                            '%s Schedules',
                            $tplData['numSchedules'],
                            'gamb-duplica-wp'
                        ),
                        $tplData['numSchedules']
                    ));
                            ?></a>
            </span>
            <span>
                <?php esc_html_e('Enabled', 'gamb-duplica-wp'); ?>: 
                <b class="<?php echo ($tplData['numSchedulesEnabled'] ? 'green' : 'maroon'); ?>">
                    <?php echo (int) $tplData['numSchedulesEnabled']; ?>
                </b>
                <?php if (strlen($tplData['nextScheduleString'])) { ?>
                    - <?php esc_html_e('Next', 'gamb-duplica-wp'); ?>: <b><?php echo esc_html($tplData['nextScheduleString']); ?></b>
                <?php } ?>
            </span>
        </li>
        <?php } ?>
        <?php if (CapMng::can(CapMng::CAP_STORAGE, false)) { ?>
        <li>
            <span class="dup-section-label-fixed-width" >
                <span class="dashicons dashicons-database gary"></span>
                <a href="<?php echo esc_url(ControllersManager::getMenuLink(ControllersManager::STORAGE_SUBMENU_SLUG)); ?>"><?php
                    echo esc_html(sprintf(
                        _n(
                            '%s Storage',
                            '%s Storages',
                            $tplData['numStorages'],
                            'gamb-duplica-wp'
                        ),
                        $tplData['numStorages']
                    ));
                            ?>
                </a>
            </span>
        </li>
        <?php } ?>
        <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
        <li>
            <span class="dup-section-label-fixed-width" >
                <span class="dashicons dashicons-admin-settings gary"></span>
                <a href="<?php echo esc_url($templatesURL); ?>"><?php
                    echo esc_html(sprintf(
                        _n(
                            '%s Template',
                            '%s Templates',
                            $tplData['numTemplates'],
                            'gamb-duplica-wp'
                        ),
                        $tplData['numTemplates']
                    ));
                            ?>
                </a>
            </span>
        </li>
        <?php } ?>
        <?php if (CapMng::can(CapMng::CAP_BACKUP_RESTORE, false)) { ?>
        <li  class="dup-flex-content">
            <span class="dup-section-label-fixed-width" >
                <span class="dashicons dashicons-image-rotate gary"></span>
                <a href="<?php echo esc_url($recoveryURl); ?>" ><?php
                    esc_html_e('Recovery Point', 'gamb-duplica-wp');
                ?> 
                </a>
            </span>
            <span>
                <?php if (RecoveryPackage::getRecoverPackageId() === false) { ?>
                    <span class="maroon"><b><?php esc_html_e('Not set', 'gamb-duplica-wp'); ?></b></span>
                <?php } else { ?>
                    <span class="green"><b><?php esc_html_e('Set On', 'gamb-duplica-wp'); ?></b></span>&nbsp; 
                    <b><?php echo esc_html($tplData['recoverDateString']); ?></b>
                <?php } ?>
            </span>
        </li>
        <?php } ?>
    </ul>
</div>