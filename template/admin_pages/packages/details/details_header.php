<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Controllers\PackagesPageController;
use GambDuplicaWP\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var GAMB_DUPLICA_WP_Package $package
 */

$package = $tplData['package'];
/** @var string */
$innerPage           = $tplData['currentInnerPage'];
$enable_transfer_tab = (
    $package->getLocalPackageFilePath(GAMB_DUPLICA_WP_Package_File_Type::Installer) !== false &&
    $package->getLocalPackageFilePath(GAMB_DUPLICA_WP_Package_File_Type::Archive) !== false
);

$packagesListUrl   = PackagesPageController::getInstance()->getMenuLink();
$packgeDefailsUrl  = PackagesPageController::getInstance()->getPackageDetailsUrl($package->ID);
$packgeTransferUrl = PackagesPageController::getInstance()->getPackageTransferUrl($package->ID);
?>
<h2 class="nav-tab-wrapper">  
    <a 
        href="<?php echo esc_url($packgeDefailsUrl); ?>" 
        class="nav-tab <?php echo ($innerPage == PackagesPageController::LIST_INNER_PAGE_DETAILS) ? 'nav-tab-active' : '' ?>"
    > 
        <?php esc_html_e('Details', 'gamb-duplica-wp'); ?>
    </a> 
    <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
        <a 
            href="<?php echo esc_url($packgeTransferUrl); ?>" 
            class="nav-tab <?php echo ($innerPage == PackagesPageController::LIST_INNER_PAGE_TRANSFER) ? 'nav-tab-active' : '' ?>"
            <?php if ($enable_transfer_tab === false) { ?>
                onclick="DupPro.Pack.TransferDisabled(); return false;" 
            <?php } ?>
        > 
            <?php esc_html_e('Transfer', 'gamb-duplica-wp'); ?>
        </a>  
    <?php } ?>  
</h2>
<div class="dup-details-packages-list">
    <a href="<?php echo esc_url($packagesListUrl); ?>">[<?php esc_html_e('Backups', 'gamb-duplica-wp'); ?>]</a>
</div>

<?php if ($package->Status == GAMB_DUPLICA_WP_PackageStatus::ERROR) { ?>
<div id='dpro-error' class="error">
    <p>
        <b>
            <?php
                printf(
                    esc_html_x(
                        'Error encountered building Backup, please review %1$sBackup log%2$s for details.',
                        '1 and 2 are opening and closing anchor tags',
                        'gamb-duplica-wp'
                    ),
                    '<a target="_blank" href="' . esc_url($package->get_log_url()) . '">',
                    '</a>'
                );
            ?> 
        </b>
        <br/>
        <?php
            printf(
                esc_html_x(
                    'For more help read the %1$sFAQ pages%2$s or submit a %3$shelp ticket%4$s.',
                    '1 and 3 are opening, 2 and 4 are closing anchor/link tags',
                    'gamb-duplica-wp'
                ),
                '<a target="_blank" href="' . esc_url(GambDuplicaWP_PRO_TECH_FAQ_URL) . '">',
                '</a>',
                '<a target="_blank" href="' . esc_url(GambDuplicaWP_PRO_BLOG_URL . 'my-account/support/') . '">',
                '</a>'
            );
        ?>
    </p>
</div>
    <?php
}

$alertTransferDisabled          = new GAMB_DUPLICA_WP_UI_Dialog();
$alertTransferDisabled->title   = __('Transfer Error', 'gamb-duplica-wp');
$alertTransferDisabled->message = __('No Backup in default location so transfer is disabled.', 'gamb-duplica-wp');
$alertTransferDisabled->initAlert();
?>
<script>
    DupPro.Pack.TransferDisabled = function() {
        <?php $alertTransferDisabled->showAlert(); ?>
    }
</script>