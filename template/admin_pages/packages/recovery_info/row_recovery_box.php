<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Package\Recovery\RecoveryPackage;
use GambDuplicaWP\Views\ViewHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var \GAMB_DUPLICA_WP_Package $package
 */

$package      = $tplData['package'];
$isRecoveable = RecoveryPackage::isPackageIdRecoveable($package->ID);

if ($isRecoveable) {
    $tplMng->render('admin_pages/packages/recovery_info/row_recovery_box_available');
} else {
    $tplMng->render('admin_pages/packages/recovery_info/row_recovery_box_unavailable');
}
?>
<hr class="margin-top-1 margin-bottom-1" >
<small><i>
<?php
echo wp_kses(
    sprintf(
        _x(
            '%1$sDisaster Recovery%2$s makes it quick and easy to restore your site during an emergency. 
            You don’t need WordPress to be working for this. 
            Just keep the Disaster Recovery Link in a safe spot and use it when needed, or use the Launcher to restore your backup.',
            '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
            'gamb-duplica-wp'
        ),
        '<a href="' . esc_url(GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . 'tools-recovery') . '" target="_blank">',
        '</a>'
    ),
    ViewHelper::GEN_KSES_TAGS
);
?>
<br>
<?php
echo wp_kses(
    sprintf(
        _x(
            'If your backup isn’t eligible for Disaster Recovery, you can still use the %1$sRestore Backup button.%2$s',
            '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
            'gamb-duplica-wp'
        ),
        '<a href="' . esc_url(GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . 'restoring-your-backup') . '" target="_blank">',
        '</a>'
    ),
    ViewHelper::GEN_KSES_TAGS
);
?>
</i></small>