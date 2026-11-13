<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Controllers\ImportPageController;
use GambDuplicaWP\Controllers\SettingsPageController;
use GambDuplicaWP\Controllers\ToolsPageController;
use GambDuplicaWP\Core\CapMng;
use GambDuplicaWP\Package\Recovery\RecoveryPackage;
use GambDuplicaWP\Views\ViewHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$settingsUrl = esc_url($ctrlMng->getMenuLink($ctrlMng::SETTINGS_SUBMENU_SLUG, SettingsPageController::L2_SLUG_PACKAGE));
$templateUrl = esc_url($ctrlMng->getMenuLink($ctrlMng::TOOLS_SUBMENU_SLUG, ToolsPageController::L2_SLUG_TEMPLATE));
$recoveryUrl = esc_url($ctrlMng->getMenuLink($ctrlMng::TOOLS_SUBMENU_SLUG, ToolsPageController::L2_SLUG_RECOVERY));
?>
<div class="dup-toolbar">
    <label for="dup-pack-bulk-actions" class="screen-reader-text">Select bulk action</label>
    <select id="dup-pack-bulk-actions" class="small" >
        <option value="-1" selected="selected">
            <?php esc_html_e("Bulk Actions", 'gamb-duplica-wp') ?>
        </option>
        <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
        <option value="delete" title="<?php esc_attr_e("Delete selected Backup(s)", 'gamb-duplica-wp') ?>">
            <?php esc_html_e("Delete", 'gamb-duplica-wp') ?>
        </option>
        <?php } ?>
    </select>
    <input 
        type="button"
        id="dup-pack-bulk-apply" 
        class="button hollow secondary small"
        value="<?php esc_attr_e("Apply", 'gamb-duplica-wp') ?>"
        onclick="DupPro.Pack.ConfirmDelete()" 
    >

    <span class="separator"></span>

    <?php if (CapMng::can(CapMng::CAP_SETTINGS, false)) { ?>
    <a href="<?php echo esc_url($settingsUrl); ?>"
        class="button hollow secondary small dpro-toolbar-settings"
        data-tooltip-title="<?php esc_attr_e("Backup Settings", 'gamb-duplica-wp'); ?>" 
        data-tooltip="<?php esc_attr_e("Advanced settings for backups.", 'gamb-duplica-wp'); ?>"
    >
        <i class="fas fa-sliders-h fa-fw"></i>
    </a>
    <?php } ?>
    <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
    <a href="<?php echo esc_url($templateUrl); ?>" 
        class="button hollow secondary small dpro-toolbar-templates"
        data-tooltip-title="<?php esc_attr_e("Templates", 'gamb-duplica-wp'); ?>" 
        data-tooltip="<?php esc_attr_e("Create Backup Templates with Preset Configurations.", 'gamb-duplica-wp'); ?>"
    >
        <i class="far fa-clone fa-fw"></i>
    </a>
    <?php } ?>

    <?php if (CapMng::can(CapMng::CAP_IMPORT, false)) { ?>
    <a href="<?php echo esc_url(ImportPageController::getImportPageLink()); ?>" 
        id="btn-logs-dialog"
        class="button hollow secondary small dpro-toolbar-import" 
        data-tooltip-title="<?php esc_attr_e("Import", 'gamb-duplica-wp'); ?>" 
        data-tooltip="<?php esc_attr_e("Import a backups from a file or link.", 'gamb-duplica-wp'); ?>"
    >
        <i class="fas fa-arrow-alt-circle-down fa-fw"></i>
    </a>
    <?php } ?>
    <?php if (CapMng::can(CapMng::CAP_BACKUP_RESTORE, false)) { ?>
    <span 
        class="dpro-toolbar-recovery-info 
        button hollow secondary small <?php echo (RecoveryPackage::getRecoverPackageId() === false ? 'dup-recovery-unset' : ''); ?>"
        data-tooltip-title="<?php esc_attr_e("Disaster Recovery", 'gamb-duplica-wp') ?>" 
        data-tooltip="<?php esc_attr_e("Quickly restore this site to a specific point in time.", 'gamb-duplica-wp') ?>"  
    >
        <?php ViewHelper::disasterIcon(); ?> 
    </span>
    <?php } ?>
</div>
