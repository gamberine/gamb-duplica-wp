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
 */
?>
<div class="dup-pro-recovery-widget-wrapper" >
    <?php if ($tplData['displayDetails']) { ?>
    <div class="dup-pro-recovery-point-details margin-bottom-1">
        <?php $tplMng->render('admin_pages/tools/recovery/widget/recovery-widget-details', $tplData); ?>
    </div>
    <?php } ?>
    <?php if ($tplData['selector']) {
        $tplMng->render('admin_pages/tools/recovery/widget/recovery-widget-selector', $tplData);
    } ?>
    <div class="dup-pro-recovery-point-actions">
        <?php if ($tplData['recoverablePackages']) {
            $tplMng->render('admin_pages/tools/recovery/widget/recovery-widget-link-actions', $tplData);
        } ?>
    </div>
</div>
