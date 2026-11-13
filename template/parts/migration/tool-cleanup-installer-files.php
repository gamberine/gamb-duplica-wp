<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 */

if (!isset($tplData['isInstallerCleanup']) || !$tplData['isInstallerCleanup']) {
    return;
}

?>
<div id="message" class="notice notice-success dpro-admin-notice">
    <?php $tplMng->render('parts/migration/clean-installation-files'); ?>
</div>
