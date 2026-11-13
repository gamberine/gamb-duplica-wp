<?php

/**
 * @package gamb-duplica-wp
 */

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

// If its in the pending cancels consider it stopped
if (in_array($package->ID, $tplData['pending_cancelled_package_ids'])) {
    $status = GAMB_DUPLICA_WP_PackageStatus::PENDING_CANCEL;
} else {
    $status = $package->Status;
}

if ($package->Status >= GAMB_DUPLICA_WP_PackageStatus::COMPLETE) {
    $tplMng->render('admin_pages/packages/package_row_complete', ['status' => $status]);
} else {
    $tplMng->render('admin_pages/packages/package_row_incomplete', ['status' => $status]);
}
$tplMng->render('admin_pages/packages/package_row_building', ['status' => $status]);
