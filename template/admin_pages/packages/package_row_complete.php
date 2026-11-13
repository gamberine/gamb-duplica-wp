<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Controllers\PackagesPageController;
use gamb-duplica-wp\Package\Recovery\RecoveryPackage;
use gamb-duplica-wp\Views\UserUIOptions;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var ?GAMB_DUPLICA_WP_Package $package
 */
$package = $tplData['package'];

/** @var int */
$status = $tplData['status'];

if ($status < GAMB_DUPLICA_WP_PackageStatus::COMPLETE) {
    return;
}

$global            = GAMB_DUPLICA_WP_Global_Entity::getInstance();
$isRecoveable      = RecoveryPackage::isPackageIdRecoveable($package->ID);
$isRecoverPoint    = (RecoveryPackage::getRecoverPackageId() === $package->ID);
$pack_name         = $package->getName();
$pack_archive_size = $package->Archive->Size;
$pack_dbonly       = $package->isDBOnly();
$brand             = $package->Brand;

//Links
$uniqueid         = $package->getNameHash();
$archive_exists   = ($package->getLocalPackageFilePath(GAMB_DUPLICA_WP_Package_File_Type::Archive) != false);
$installer_exists = ($package->getLocalPackageFilePath(GAMB_DUPLICA_WP_Package_File_Type::Installer) != false);
$progress_error   = '';

//ROW CSS
$rowClasses   = [''];
$rowClasses[] = 'dup-row';
$rowClasses[] = 'dup-row-complete';
$rowClasses[] = ($isRecoverPoint) ? 'dup-recovery-package' : '';
$rowCSS       = trim(implode(' ', $rowClasses));


//ArchiveInfo
$archive_name         = $package->Archive->File;
$archiveDownloadURL   = $package->getLocalPackageFileURL(GAMB_DUPLICA_WP_Package_File_Type::Archive);
$installerDownloadURL = $package->getLocalPackageFileURL(GAMB_DUPLICA_WP_Package_File_Type::Installer);
$installerFullName    = $package->Installer->getInstallerName();
$isBackupAvailable    = ($package->haveRemoteStorage() || $package->haveLocalStorage());

//Lang Values
$txt_DatabaseOnly       = __('Database Only', 'gamb-duplica-wp');
$txt_BackupNotAvailable = __('Backup doesn\'t exist', 'gamb-duplica-wp');

switch ($package->Type) {
    case GAMB_DUPLICA_WP_PackageType::MANUAL:
        $package_type_string = __('Manual', 'gamb-duplica-wp');
        break;
    case GAMB_DUPLICA_WP_PackageType::SCHEDULED:
        $package_type_string = __('Schedule', 'gamb-duplica-wp');
        break;
    case GAMB_DUPLICA_WP_PackageType::RUN_NOW:
        $lang_schedule       = __('Schedule', 'gamb-duplica-wp');
        $lang_title          = __('This Backup was started manually from the schedules page.', 'gamb-duplica-wp');
        $package_type_string = "{$lang_schedule}<span><sup>&nbsp;<i class='fas fa-cog fa-sm pointer' title='{$lang_title}'></i>&nbsp;</sup><span>";
        break;
    default:
        $package_type_string = __('Unknown', 'gamb-duplica-wp');
        break;
}

$packageDetailsURL = PackagesPageController::getInstance()->getPackageDetailsURL($package->ID);
$createdFormat     = UserUIOptions::getInstance()->get(UserUIOptions::VAL_CREATED_DATE_FORMAT);

?>
<tr 
    id="dup-row-pack-id-<?php echo (int) $package->ID; ?>" 
    data-package-id="<?php echo (int) $package->ID; ?>" 
    class="<?php echo esc_attr($rowCSS); ?>" >
    <td class="dup-check-column dup-cell-chk">
        <label for="<?php echo (int) $package->ID; ?>">
        <input 
            name="delete_confirm" 
            type="checkbox" 
            id="<?php echo (int) $package->ID; ?>" 
            data-archive-name="<?php echo esc_attr($archive_name); ?>" 
            data-installer-name="<?php echo esc_attr($installerFullName); ?>" />
        </label>
    </td>
    <td class="dup-name-column dup-cell-name">
        <?php echo esc_html($pack_name); ?>
    </td>
    <td class="dup-note-column">
        <?php echo esc_html($package->notes); ?>
    </td>
    <td class="dup-storages-column">
    </td>
    <td class="dup-flags-column">
        <?php $tplMng->render('admin_pages/packages/row_parts/falgs_cell'); ?>
    </td>
    <td class="dup-size-column" >
        <?php echo esc_html(GAMB_DUPLICA_WP_U::byteSize($pack_archive_size)); ?>
    </td>
    <td class="dup-created-column" >
        <?php echo esc_html(GAMB_DUPLICA_WP_Package::format_and_get_local_date_time($package->getCreated(), $createdFormat)); ?>
    </td>
    <td class="dup-age-column">
        <?php echo esc_html($package->getPackageLife('human')); ?>
    </td>
    <td class="dup-cell-btns dup-download-column" <?php echo $isBackupAvailable ? '' : 'data-tooltip="' . esc_attr($txt_BackupNotAvailable) . '"'; ?>>
        <?php $tplMng->render('admin_pages/packages/row_parts/download_buttons'); ?>
    </td>
    <td class="dup-cell-btns dup-restore-column" <?php echo $isBackupAvailable ? '' : 'data-tooltip="' . esc_attr($txt_BackupNotAvailable) . '"'; ?>>
        <?php $tplMng->render('admin_pages/packages/row_parts/restore_backup_button'); ?>
    </td>
    <td class="dup-cell-btns dup-cell-toggle-btn dup-toggle-details dup-details-column">
        <span class="full-cell-button link-style">
            <i class="fa-solid fa-plus"></i>
        </span>
    </td>
</tr>
<tr id="dup-row-pack-id-<?php echo (int) $package->ID; ?>-details" class="dup-row-details no-display">
    <?php $tplMng->render('admin_pages/packages/row_parts/details_package'); ?>
</tr>

