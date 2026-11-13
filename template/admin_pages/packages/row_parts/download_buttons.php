<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var ?GAMB_DUPLICA_WP_Package $package
 */

$package              = $tplData['package'];
$archive_exists       = ($package->getLocalPackageFilePath(GAMB_DUPLICA_WP_Package_File_Type::Archive) != false);
$installer_exists     = ($package->getLocalPackageFilePath(GAMB_DUPLICA_WP_Package_File_Type::Installer) != false);
$archiveDownloadURL   = $package->getLocalPackageFileURL(GAMB_DUPLICA_WP_Package_File_Type::Archive);
$installerDownloadURL = $package->getLocalPackageFileURL(GAMB_DUPLICA_WP_Package_File_Type::Installer);
$pack_format          = strtolower($package->Archive->Format);

if (!CapMng::can(CapMng::CAP_EXPORT, false)) {
    return;
}

$isRunning  = GAMB_DUPLICA_WP_Package::isPackageRunning();
$canEnabled = ($package->haveRemoteStorage() || $package->haveLocalStorage());

if ($archive_exists) : ?>
    <nav class="dup-dnload-menu">
        <button
            class="dup-dnload-btn full-cell-button link-style no-select can-enabled"
            type="button" 
            aria-haspopup="true"
            data-tooltip="<?php esc_attr_e('Download Backup.', 'gamb-duplica-wp') ?>"
            <?php disabled(!$canEnabled || $isRunning); ?>
            >
            <i class="fa fa-download"></i>&nbsp;
            <span><?php esc_html_e("Download", 'gamb-duplica-wp'); ?></span>
        </button>

        <nav class="dup-dnload-menu-items no-display">
            <button
                aria-label="<?php esc_html_e("Download Installer and Archive", 'gamb-duplica-wp') ?>"
                title="<?php echo ($installer_exists ? '' : esc_html__("Unable to locate both Backup files!", 'gamb-duplica-wp')); ?>"
                onclick="DupPro.Pack.DownloadFile('<?php echo esc_attr($archiveDownloadURL); ?>',
                        '<?php echo esc_attr($package->get_archive_filename()); ?>');
                        setTimeout(function () {DupPro.Pack.DownloadFile('<?php echo esc_attr($installerDownloadURL); ?>');}, 700);
                        jQuery(this).parent().addClass('no-display');
                        return false;"
                class="dup-dnload-both"
                >
                    <i class="fa fa-fw <?php echo ($installer_exists ? 'fa-download' : 'fa-exclamation-triangle') ?>"></i>
                    &nbsp;<?php esc_html_e("Both Files", 'gamb-duplica-wp') ?>
            </button>
            <button
                aria-label="<?php esc_html_e("Download Installer", 'gamb-duplica-wp') ?>"
                title="<?php echo ($installer_exists) ? '' : esc_html__("Unable to locate installer Backup file!", 'gamb-duplica-wp'); ?>"
                onclick="DupPro.Pack.DownloadFile('<?php echo esc_attr($installerDownloadURL); ?>');
                        jQuery(this).parent().addClass('no-display');
                        return false;"
                class="dup-dnload-installer">
                <i class="fa fa-fw <?php echo ($installer_exists ? 'fa-bolt' : 'fa-exclamation-triangle') ?>"></i>&nbsp;
                <?php esc_html_e("Installer", 'gamb-duplica-wp') ?>
            </button>
            <button
                aria-label="<?php esc_html_e("Download Archive", 'gamb-duplica-wp') ?>"
                onclick="DupPro.Pack.DownloadFile('<?php echo esc_attr($archiveDownloadURL); ?>',
                        '<?php echo esc_attr($package->get_archive_filename()); ?>');
                        jQuery(this).parent().addClass('no-display');
                        return false;"
                        
                class="dup-dnload-archive">
                    <i class="fa-fw far fa-file-archive"></i>&nbsp;
                    <?php echo esc_html__('Archive', 'gamb-duplica-wp') . ' (' . esc_html($pack_format) . ')'; ?>
            </button>
        </nav>
    </nav>
<?php else :
    ?>
    <button 
        type="button" 
        class="full-cell-button link-style dup-remote-download <?php echo ($canEnabled ? 'can-enabled' : ''); ?>"
        data-package-id="<?php echo (int) $package->ID; ?>"
        data-needs-download="<?php echo $package->haveLocalStorage() ? "false" : "true"; ?>"
        aria-label="<?php esc_attr_e("Download Backup", 'gamb-duplica-wp') ?>"
        data-tooltip="<?php esc_attr_e("Download Backup.", 'gamb-duplica-wp') ?>"
        <?php disabled(!$canEnabled || $isRunning); ?>
    >
        <i class="fas fa-download fa-fw"></i> <?php esc_html_e("Download", 'gamb-duplica-wp'); ?>
    </button>
<?php endif; ?>
