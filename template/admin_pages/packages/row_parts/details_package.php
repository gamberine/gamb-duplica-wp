<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Controllers\PackagesPageController;
use GambDuplicaWP\Core\CapMng;
use GambDuplicaWP\Package\Recovery\RecoveryPackage;
use GambDuplicaWP\Views\ViewHelper;

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

$pack_dbonly       = $package->isDBOnly();
$pack_format       = strtolower($package->Archive->Format);
$packageDetailsURL = PackagesPageController::getInstance()->getPackageDetailsURL($package->ID);
$txt_DBOnly        = __('DB Only', 'gamb-duplica-wp');
$archive_exists    = ($package->getLocalPackageFilePath(GAMB_DUPLICA_WP_Package_File_Type::Archive) != false);
$isRecoveable      = RecoveryPackage::isPackageIdRecoveable($package->ID);

?>
<td colspan="9">
    <div  class="dup-package-row-details-wrapper" >
        <div class="dup-ovr-hdr">
            <h3 class="font-bold" >
                <i class="fas fa-archive"></i>
                <?php esc_html_e('Backup Overview', 'gamb-duplica-wp'); ?>
            </h3>
        </div>

        <div class="dup-ovr-bar-flex-box">
            <div>
                <label><?php esc_html_e('WordPress', 'gamb-duplica-wp');?></label>
                <?php echo esc_html($package->VersionWP); ?> &nbsp;
            </div>
            <div>
                <label><?php esc_html_e('Format', 'gamb-duplica-wp');?></label>
                <?php echo esc_html(strtoupper($pack_format)); ?>
            </div>
            <div>
                <label><?php esc_html_e('Files', 'gamb-duplica-wp');?></label>
                <?php echo ($pack_dbonly)
                    ? '<i>' . esc_html($txt_DBOnly) . '</i>'
                    : number_format($package->Archive->FileCount); ?>
            </div>
            <div>
                <label><?php esc_html_e('Folders', 'gamb-duplica-wp');?></label>
                <?php echo ($pack_dbonly)
                    ? '<i>' . esc_html($txt_DBOnly) . '</i>'
                    :  number_format($package->Archive->DirCount) ?>
            </div>
            <div>
                <label><?php esc_html_e('Tables', 'gamb-duplica-wp');?></label>
                <?php
                printf(
                    esc_html_x(
                        '%1$d of %2$d',
                        'Example: 7 of 10',
                        'gamb-duplica-wp'
                    ),
                    (int) $package->Database->info->tablesFinalCount,
                    (int) $package->Database->info->tablesBaseCount
                );
                ?>
            </div>
        </div>

        <div class="dup-ovr-ctrls-flex-box">
            <div class="flex-item">
                <?php
                if (CapMng::can(CapMng::CAP_EXPORT, false)) {
                    $tplMng->render('admin_pages/packages/row_parts/details_download_block');
                }
                ?>
            </div>

            <div class="flex-item dup-ovr-opts">
                <div class="dup-ovr-ctrls-hdrs">
                    <h3 class="font-bold margin-bottom-0">
                        <?php esc_html_e('Options', 'gamb-duplica-wp');?>
                    </h3>
                    <small class="xsmall dark-gray-color">
                        <i><?php esc_html_e('Backup actions.', 'gamb-duplica-wp'); ?></i>
                    </small>
                </div>
                <ul class="no-bullet">
                    <li>
                        <a
                            aria-label="<?php esc_attr_e("Go to Backup details screen", 'gamb-duplica-wp') ?>"
                            class="button hollow secondary expanded dup-details"
                            href="<?php echo esc_url($packageDetailsURL); ?>"
                        >
                            <span><i class="fas fa-search"></i> <?php esc_html_e("View Details", 'gamb-duplica-wp') ?></span>
                        </a>
                    </li>
                <?php if (CapMng::can(CapMng::CAP_STORAGE, false)) { ?>
                    <li>
                        <?php if ($archive_exists) : ?>
                            <button class="button hollow secondary expanded dup-transfer"
                                aria-label="<?php esc_attr_e('Go to Backup transfer screen', 'gamb-duplica-wp') ?>"
                                onclick="DupPro.Pack.OpenPackTransfer(<?php echo (int) $package->ID; ?>); return false;">
                                <span><i class="fa fa-exchange-alt fa-fw"></i> <?php esc_html_e("Transfer Backup", 'gamb-duplica-wp') ?></span>
                            </button>
                        <?php else : ?>
                            <span title="<?php esc_attr_e('Transfer Backups requires the use of built-in default storage!', 'gamb-duplica-wp') ?>">
                                <button class="button hollow secondary expanded disabled" >
                                    <span><i class="fa fa-exchange-alt fa-fw"></i> <?php esc_html_e("Transfer Backup", 'gamb-duplica-wp') ?></span>
                                </button>
                            </span>
                        <?php endif; ?>
                        </li>
                <?php } ?>

                <?php if (CapMng::can(CapMng::CAP_BACKUP_RESTORE, false)) { ?>
                    <li>
                        <button
                            aria-label="<?php esc_attr_e("Recover this Backup", 'gamb-duplica-wp') ?>"
                            class="button hollow secondary expanded dpro-btn-open-recovery-box <?php echo ($isRecoveable) ? '' : 'maroon'?>"
                            data-package-id="<?php echo (int) $package->ID; ?>"
                        >
                            <?php ViewHelper::disasterIcon(true); ?>
                            <?php esc_html_e("Disaster Recovery", 'gamb-duplica-wp'); ?>
                        </button>
                    </li>
                <?php } ?>
                </ul>
        </div>
    </div>
</td>
