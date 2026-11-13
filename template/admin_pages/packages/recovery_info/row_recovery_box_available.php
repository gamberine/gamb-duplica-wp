<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Controllers\PackagesPageController;
use gamb-duplica-wp\Controllers\RecoveryController;
use gamb-duplica-wp\Core\Controllers\ControllersManager;
use gamb-duplica-wp\Core\Views\TplMng;
use gamb-duplica-wp\Package\Recovery\RecoveryPackage;
use gamb-duplica-wp\Views\ViewHelper;

/**
 * Variables
 *
 * @var ControllersManager $ctrlMng
 * @var TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var GAMB_DUPLICA_WP_Package $package
 */
$package        = $tplData['package'];
$isRecoverPoint = (RecoveryPackage::getRecoverPackageId() === $package->ID);

$colorClass = ($isRecoverPoint ? 'green' : '');
?>
<h3 class="dup-title margin-top-0">
    <?php ViewHelper::disasterIcon(true, $colorClass); ?>&nbsp;
    <?php
    if ($isRecoverPoint) {
        esc_html_e('Disaster Recovery - Is Set on this Backup', 'gamb-duplica-wp');
    } else {
        esc_html_e('Disaster Recovery - Is Available for this Backup', 'gamb-duplica-wp');
    }
    ?>
</h3>

<?php $tplMng->render('parts/recovery/package_info_mini'); ?>
<hr class="margin-top-1 margin-bottom-1" >

<?php if ($isRecoverPoint) {
    RecoveryController::renderRecoveryWidged([
        'details'    => false,
        'selector'   => false,
        'subtitle'   => '',
        'copyLink'   => false,
        'copyButton' => true,
        'launch'     => false,
        'download'   => true,
        'info'       => true,
    ]);
} else { ?>
    <form method="post" action="<?php PackagesPageController::getInstance()->getPageUrl(); ?>" >
        <?php PackagesPageController::getInstance()->getActionByKey(PackagesPageController::ACTION_SET_RECOVERY_POINT)->getActionNonceFileds(); ?>
        <input type="hidden" name="recovery_package" value="<?php echo (int) $package->ID; ?>" >
        <div class="dup-pro-recovery-widget-wrapper" >
            <div class="dup-pro-recovery-point-actions" >
                <div class="dup-pro-recovery-buttons">
                    <button
                        class="button primary dpro-btn-set-recovery margin-0" 
                        type="submit"
                    >
                        <span><?php ViewHelper::disasterIcon(); ?>&nbsp;
                        <?php esc_html_e("Set Disaster Recovery", 'gamb-duplica-wp'); ?></span>&nbsp;
                        <i 
                            class="fa-solid fa-question-circle fa-sm dark-gray-color dup-base-color white"
                            data-tooltip-title="<?php esc_attr_e("Activate Recovery", 'gamb-duplica-wp'); ?>"
                            data-tooltip="<?php esc_attr_e("This action will set this Backup as the active Disaster Recovery Backup.", 'gamb-duplica-wp'); ?>"
                            aria-expanded="false"
                        >
                        </i>
                    </button>
                </div>
            </div>
        </div>
    </form>
<?php }
