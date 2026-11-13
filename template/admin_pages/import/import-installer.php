<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Controllers\ImportPageController;
use GambDuplicaWP\Package\Import\PackageImporter;
use GambDuplicaWP\Package\Recovery\RecoveryPackage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var PackageImporter $importObj
 */
$importObj = $tplData['importObj'];
/** @var string $iframeSrc */
$iframeSrc         = $tplData['iframeSrc'];
$importFailMessage = '';

if (!$importObj->isImportable($importFailMessage)) {
    ?>
    <div class="wrap dup-styles">
        <h1>
            <?php esc_html_e("Install Backup", 'gamb-duplica-wp'); ?>
        </h1>
        <div class="dpro-pro-import-installer-content-wrapper" >
            <p class="orangered">
                <?php echo esc_html($importFailMessage); ?>
            </p>
        </div>
    </div>
<?php } else { ?>
    <div id="dpro-pro-import-installer-wrapper" class="dup-styles" >
        <div id="dpro-pro-import-installer-top-bar" class="dup-pro-recovery-details-max-width-wrapper" >
            <a href="<?php echo esc_url(ImportPageController::getImportPageLink()); ?>" class="button secondary hollow small margin-bottom-0" >
                <i class="fa fa-caret-left"></i> <?php esc_html_e("Back to Import", 'gamb-duplica-wp'); ?>
            </a>&nbsp;
            <span class="link-style no-decoration recovery-copy-top-wrapper" >
                <?php if (($recoverPackage = RecoveryPackage::getRecoverPackage()) !== false) { ?>
                    <span class="button secondary hollow small margin-bottom-0" 
                          data-tooltip-placement="right"
                          data-dup-copy-value="<?php echo esc_url($recoverPackage->getInstallLink()); ?>"
                          data-dup-copy-title="<?php esc_attr_e("Copy Recovery URL to clipboard", 'gamb-duplica-wp'); ?>"
                          data-dup-copied-title="<?php esc_attr_e("Recovery URL copied to clipboard", 'gamb-duplica-wp'); ?>" >
                              <?php esc_html_e("Copy Recovery URL", 'gamb-duplica-wp'); ?>
                    </span>
                <?php } else { ?>
                    <span class="button secondary hollow disabled small margin-bottom-0">
                        <i class="fas fa-exclamation-circle"></i> <?php esc_html_e("Recovery Point Not Set", 'gamb-duplica-wp'); ?>
                    </span>
                <?php } ?>
            </span>
        </div>
        <div id="dup-pro-import-installer-modal" class="no-display"></div>
        <iframe id="dpro-pro-import-installer-iframe" src="<?php echo esc_url($iframeSrc); ?>" ></iframe>
    </div>
    <?php
}
