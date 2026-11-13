<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Controllers\PackagesPageController;

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
?>
<div class="progress-error text-center">
    <?php
    switch ($status) {
        case GAMB_DUPLICA_WP_PackageStatus::ERROR:
            $packageDetailsURL = PackagesPageController::getInstance()->getPackageDetailsURL($package->ID);
            ?>
            <a type="button" class="dup-cell-err-btn" href="<?php echo esc_url($packageDetailsURL) ?>">
                <i class="fa fa-exclamation-triangle fa-xs"></i>&nbsp;
                <?php esc_html_e('Error Processing', 'gamb-duplica-wp') ?> 
            </a>
            <?php
            break;
        case GAMB_DUPLICA_WP_PackageStatus::BUILD_CANCELLED:
            ?>
            <i class="fas fa-info-circle  fa-sm"></i>&nbsp;
            <?php esc_html_e('Build Cancelled', 'gamb-duplica-wp') ?>
            <?php
            break;
        case GAMB_DUPLICA_WP_PackageStatus::PENDING_CANCEL:
            ?>
            <i class="fas fa-info-circle  fa-sm"></i>
            <?php esc_html_e('Cancelling Build', 'gamb-duplica-wp') ?>
            <?php
            break;
        case GAMB_DUPLICA_WP_PackageStatus::STORAGE_CANCELLED:
            ?>
            <i class="fas fa-info-circle  fa-sm"></i>&nbsp;
            <?php esc_html_e('Storage Cancelled', 'gamb-duplica-wp') ?>
            <?php
            break;
        case GAMB_DUPLICA_WP_PackageStatus::REQUIREMENTS_FAILED:
            $packageLogFile = dirname($package->StorePath) . '/' . $package->getNameHash() . '_log.txt';
            if (file_exists($packageLogFile)) {
                $link_log = $package->StoreURL . $package->getNameHash() . "_log.txt";
            } else {
                // .log is for backward compatibility
                $link_log = $package->StoreURL . $package->getNameHash() . ".log";
            }
            ?>
            <a href="<?php echo esc_url($link_log) ?>" target="_blank">
                <i class="fas fa-info-circle"></i> <?php esc_html_e('Requirements Failed', 'gamb-duplica-wp') ?>
            </a>
            <?php
            break;
        default:
            break;
    }
    ?>
</div>
