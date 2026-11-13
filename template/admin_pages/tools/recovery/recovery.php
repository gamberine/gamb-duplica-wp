<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Controllers\RecoveryController;
use GambDuplicaWP\Package\Recovery\RecoveryPackage;
use GambDuplicaWP\Views\ViewHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var bool $blur
 */

$blur = $tplData['blur'];

$recoverPackage      = RecoveryPackage::getRecoverPackage();
$recoverPackageId    = RecoveryPackage::getRecoverPackageId();
$recoverablePackages = RecoveryPackage::getRecoverablesPackages();

?>
<h2 class="margin-bottom-0">
    <?php ViewHelper::disasterIcon(); ?>&nbsp;<?php esc_html_e("Disaster Recovery", 'gamb-duplica-wp'); ?>
</h2>
<hr/>

<p class="margin-bottom-1">
    <?php esc_html_e("Quickly restore this site to a specific Backup in time.", 'gamb-duplica-wp'); ?>
    <span class="link-style dup-global-help">
        <?php esc_html_e("Need more help?", 'gamb-duplica-wp'); ?>
    </span>
</p>
<div class="dup-pro-recovery-details-max-width-wrapper <?php echo ($blur ? 'dup-mock-blur' : ''); ?>" >
    <?php if (RecoveryController::isDisallow()) { ?>
        <p>
            <?php esc_html_e("The import function is disabled", 'gamb-duplica-wp'); ?>
        </p>
        <?php
        return;
    }
    ?>
    <form id="dpro-recovery-form" method="post">
        <?php
        RecoveryController::renderRecoveryWidged([
            'selector'   => true,
            'subtitle'   => '',
            'copyLink'   => true,
            'copyButton' => true,
            'launch'     => true,
            'download'   => true,
            'info'       => true,
        ]);
        ?>
    </form>
</div>
<?php
$tplMng->render('admin_pages/tools/recovery/widget/recovery-widget-scripts');
