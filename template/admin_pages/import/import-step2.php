<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

use gamb-duplica-wp\Controllers\RecoveryController;
use gamb-duplica-wp\Libs\Snap\SnapWP;
use gamb-duplica-wp\Package\Recovery\RecoveryPackage;

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$postTypeCount = SnapWP::getPostTypesCount();
if (
    ($recoverPackage = RecoveryPackage::getRecoverPackage()) == false ||
    $recoverPackage->isOutToDate()
) {
    $badgeClass =  'badge-warn';
    $badgeLabel = 'Notice';
} else {
    $badgeClass =  'badge-pass';
    $badgeLabel = 'Good';
}
?>
<div class="dup-pro-import-header" >
    <h2 class="title">
        <b>
            <?php printf(esc_html__("Step %s of 2: Confirmation", 'gamb-duplica-wp'), '<span class="red">2</span>'); ?>
        </b>
    </h2>
</div>
<div class="dup-pro-recovery-details-max-width-wrapper" >
    <div class="dup-pro-import-box closable opened" >
        <div class="box-title" >
            <?php esc_html_e('Disaster Recovery', 'gamb-duplica-wp'); ?>
            <div class="badge <?php echo esc_attr($badgeClass); ?> margin-right-1">
                <?php echo esc_html($badgeLabel); ?>
            </div>
        </div>
        <div class="box-content">
            <div  id="dup-pro-recovery-details-select-entry" class="dup-pro-recovery-info-set" >
                <?php
                RecoveryController::renderRecoveryWidged([
                    'selector'   => true,
                    'subtitle'   => '',
                    'copyLink'   => true,
                    'copyButton' => true,
                    'launch'     => false,
                    'download'   => true,
                    'info'       => true,
                ]);
                ?>
            </div>
            <hr>

            <div class="dup-pro-recovery-not-required">
                <i class="far fa-arrow-alt-circle-right"></i>
                <?php
                esc_html_e(
                    'The Recovery Point is not mandatory to perform an import. 
                    However, it can assist in restoring this site if there is a problem during install. 
                    If you have no need to recover this site then you can continue without creating the Recovery Point.',
                    'gamb-duplica-wp'
                );
                ?>
            </div>

        </div>
    </div><br/>

    <div class="dup-pro-import-box closable opened" >
        <div class="box-title" >
            <?php esc_html_e('System Overview', 'gamb-duplica-wp'); ?>
        </div>
        <div class="box-content">
            <div id="dup-pro-recovery-details-overview" >
                <div>
                    <?php esc_html_e("This site currently contains", 'gamb-duplica-wp'); ?>:
                </div>

                <table class="margin-left-2" >
                    <?php foreach ($postTypeCount as $label => $count) { ?>
                        <tr>
                            <td><?php echo esc_html($label); ?></td>
                            <td class="text-right"><?php echo (int) $count; ?></td>
                        </tr>
                    <?php } ?>
                </table>
                <p>
                    <?php esc_html_e("This process will:", 'gamb-duplica-wp') ?>
                </p>
                <ul>
                    <li>
                        <i class="far fa-check-circle"></i>&nbsp;
                        <?php esc_html_e("Launch the interactive installer wizard to install this new Backup.", 'gamb-duplica-wp'); ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="dup-pro-import-confirm-buttons">
        <input 
            id="dup-pro-import-launch-installer-cancel" 
            type="button" class="button secondary hollow small recovery-reset" 
            value="<?php esc_attr_e('Cancel', 'gamb-duplica-wp'); ?>"
        >&nbsp;
        <button 
            id="dup-pro-import-launch-installer-confirm" 
            type="button" 
            class="button primary small" 
            onclick="DupPro.ImportManager.confirmLaunchInstaller();"
        >
            <i class="fa fa-bolt fa-sm"></i> <?php esc_html_e('Launch Installer', 'gamb-duplica-wp'); ?>
        </button>
    </div>
</div>
