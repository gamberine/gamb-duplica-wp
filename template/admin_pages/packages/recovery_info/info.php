<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Controllers\RecoveryController;
use GambDuplicaWP\Package\Recovery\RecoveryPackage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<div class="dup-pro-toolbar-recovery-info margin-bottom-1">
    <?php
    if (RecoveryPackage::getRecoverPackageId() === false) {
        $tplMng->render('admin_pages/packages/recovery_info/no_recovery_set');
    } else {
        RecoveryController::renderRecoveryWidged(
            [
                'selector'   => false,
                'subtitle'   => '',
                'copyLink'   => true,
                'copyButton' => true,
                'launch'     => true,
                'download'   => true,
                'info'       => true,
            ]
        );
    }
    ?>
</div>
