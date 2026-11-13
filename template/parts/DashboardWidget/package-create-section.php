<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Addons\ProBase\License\License;
use GambDuplicaWP\Controllers\PackagesPageController;
use GambDuplicaWP\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$tooltipTitle   = esc_attr__('Backup creation', 'gamb-duplica-wp');
$tooltipContent = esc_attr__(
    'This will create a new Backup. If a Backup is currently running then this button will be disabled.',
    'gamb-duplica-wp'
);
$disableCreate  = GAMB_DUPLICA_WP_Package::isPackageRunning() || GAMB_DUPLICA_WP_Package::isPackageCancelling();
?>
<div class="dup-section-package-create dup-flex-content">
    <span>
        <?php esc_html_e('Last backup:', 'gamb-duplica-wp'); ?>
        <span class="dup-last-backup-info">
            <?php
                echo wp_kses(
                    $tplData['lastBackupString'],
                    [
                        'b'    => [],
                        'span' => [
                            'class' => [],
                        ],
                    ]
                );
                ?>
        </span>
    </span>
    <?php if (CapMng::can(CapMng::CAP_CREATE, false) && (!is_multisite() || License::can(License::CAPABILITY_MULTISITE))) { ?>
    <span
        class="dup-new-package-wrapper"
        data-tooltip-title="<?php echo esc_attr($tooltipTitle); ?>"
        data-tooltip="<?php echo esc_attr($tooltipContent); ?>"
    >
        <a  
            id="dup-pro-create-new" 
            class="button button-primary <?php echo $disableCreate ? 'disabled' : ''; ?>"
            href="<?php echo esc_url(PackagesPageController::getInstance()->getPackageBuildS1Url()); ?>"
        >
            <?php esc_html_e('Create New', 'gamb-duplica-wp'); ?>
        </a>
    </span>
    <?php } ?>
</div>
