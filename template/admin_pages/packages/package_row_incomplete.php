<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Controllers\PackagesPageController;
use GambDuplicaWP\Package\Recovery\RecoveryPackage;
use GambDuplicaWP\Views\UserUIOptions;

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
$global  = GAMB_DUPLICA_WP_Global_Entity::getInstance();

/** @var int */
$status = $tplData['status'];

if ($status >= GAMB_DUPLICA_WP_PackageStatus::COMPLETE) {
    return;
}

$isRecoveable      = RecoveryPackage::isPackageIdRecoveable($package->ID);
$isRecoverPoint    = (RecoveryPackage::getRecoverPackageId() === $package->ID);
$pack_name         = $package->getName();
$pack_archive_size = $package->Archive->Size;
$pack_namehash     = $package->getNameHash();
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
$rowClasses[] = 'dup-row-incomplete';
$rowClasses[] = ($isRecoverPoint) ? 'dup-recovery-package' : '';
$rowCSS       = trim(implode(' ', $rowClasses));

//ArchiveInfo
$archive_name         = $package->Archive->File;
$archiveDownloadURL   = $package->getLocalPackageFileURL(GAMB_DUPLICA_WP_Package_File_Type::Archive);
$installerDownloadURL = $package->getLocalPackageFileURL(GAMB_DUPLICA_WP_Package_File_Type::Installer);
$installerFullName    = $package->Installer->getInstallerName();

$createdFormat = UserUIOptions::getInstance()->get(UserUIOptions::VAL_CREATED_DATE_FORMAT);

//Lang Values
$txt_DatabaseOnly = __('Database Only', 'gamb-duplica-wp');

$cellErrCSS = '';

if ($status < GAMB_DUPLICA_WP_PackageStatus::COPIEDPACKAGE) {
    // In the process of building
    $size      = 0;
    $tmpSearch = glob(GambDuplicaWP_PRO_SSDIR_PATH_TMP . "/{$pack_namehash}_*");

    if (is_array($tmpSearch)) {
        $result = @array_map('filesize', $tmpSearch);
        $size   = array_sum($result);
    }
    $pack_archive_size = $size;
}

if ($status < 0) {
    //FAILURES AND CANCELLATIONS
    switch ($status) {
        case GAMB_DUPLICA_WP_PackageStatus::ERROR:
            $cellErrCSS = 'dup-cell-err';
            break;
        case GAMB_DUPLICA_WP_PackageStatus::BUILD_CANCELLED:
        case GAMB_DUPLICA_WP_PackageStatus::STORAGE_CANCELLED:
            $cellErrCSS = 'dup-cell-cancelled';
            break;
    }
}
?>

<tr 
   id="dup-row-pack-id-<?php echo (int) $package->ID; ?>"
   data-package-id="<?php echo (int) $package->ID; ?>" 
   data-status="<?php echo (int) $status; ?>"
   class="<?php echo esc_attr($rowCSS); ?>" 
>
    <td class="dup-check-column dup-cell-chk">
        <label for="<?php echo (int) $package->ID; ?>">
        <input name="delete_confirm"
                type="checkbox" id="<?php echo (int) $package->ID;?>"
                <?php echo ($status >= GAMB_DUPLICA_WP_PackageStatus::PRE_PROCESS) ? 'disabled="disabled"' : ''; ?> />
        </label>
    </td>
    <td class="dup-name-column dup-cell-name">
        <?php echo esc_html($pack_name); ?>
    </td>
    <td class="dup-note-column">
    </td>
    <td class="dup-storages-column">
    </td>
    <td class="dup-flags-column">
        <?php $tplMng->render('admin_pages/packages/row_parts/falgs_cell'); ?>
    </td>
    <td class="dup-size-column" >
        <?php if ($status >= GAMB_DUPLICA_WP_PackageStatus::PRE_PROCESS) {
            $package->get_display_size();
        } else {
            esc_html_e('N/A', 'gamb-duplica-wp');
        }?>
    </td>
    <td class="dup-created-column" >
        <?php echo esc_html(GAMB_DUPLICA_WP_Package::format_and_get_local_date_time($package->getCreated(), $createdFormat)); ?>
    </td>
    <td class="dup-age-column">
        <?php echo esc_html($package->getPackageLife('human')); ?>
    </td>
    <td class="dup-cell-incomplete <?php echo esc_attr($cellErrCSS); ?> no-select" colspan="3">
        <?php if ($status >= GAMB_DUPLICA_WP_PackageStatus::PRE_PROCESS) { ?>
            <i><?php esc_html_e('Creating Backup ...', 'gamb-duplica-wp'); ?></i>
        <?php } else {
            $tplMng->render(
                'admin_pages/packages/row_parts/package_progress_error',
                [
                    'package' => $package,
                    'status'  => $status,
                ]
            );
        }?>
    </td>
</tr>

