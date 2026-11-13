<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Core\CapMng;
use gamb-duplica-wp\Views\ViewHelper;

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

if (!CapMng::can(CapMng::CAP_BACKUP_RESTORE, false)) {
    return;
}

$isRunning  = GAMB_DUPLICA_WP_Package::isPackageRunning();
$canEnabled = ($package->haveRemoteStorage() || $package->haveLocalStorage());
?>
<button 
    type="button" 
    class="full-cell-button dup-restore-backup link-style <?php echo ($canEnabled ? 'can-enabled' : ''); ?>"
    data-package-id="<?php echo (int) $package->ID; ?>"
    data-needs-download="<?php echo $package->haveLocalStorage() ? "false" : "true"; ?>"
    aria-label="<?php esc_attr_e("Restore backup", 'gamb-duplica-wp') ?>"
    title="<?php esc_attr_e("Restore backup.", 'gamb-duplica-wp') ?>"
    <?php disabled(!$canEnabled || $isRunning); ?>
>
    <?php ViewHelper::restoreIcon(); ?> <?php esc_html_e("Restore", 'gamb-duplica-wp'); ?>
</button>
