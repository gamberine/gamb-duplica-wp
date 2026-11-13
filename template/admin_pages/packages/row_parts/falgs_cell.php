<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Core\CapMng;
use gamb-duplica-wp\Package\Recovery\RecoveryPackage;
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

?>
<div class="dup-package-flags" >
    <?php if ($package->hasFlag(GAMB_DUPLICA_WP_Package::FLAG_MANUAL) || $package->hasFlag(GAMB_DUPLICA_WP_Package::FLAG_SCHEDULE_RUN_NOW)) { ?>
        <span class="icon-wrapper" title="<?php esc_attr_e('Manual Backup', 'gamb-duplica-wp') ?>" >
            <i class="fa-solid fa-hand" ></i>
        </span>
    <?php } ?>
    <?php if ($package->hasFlag(GAMB_DUPLICA_WP_Package::FLAG_SCHEDULE)) { ?>
        <span class="icon-wrapper" title="<?php esc_attr_e('Schedule Backup', 'gamb-duplica-wp') ?>" >
            <i class="fa-solid fa-clock" ></i>
        </span>
    <?php } ?>
    <?php if ($package->hasFlag(GAMB_DUPLICA_WP_Package::FLAG_HAVE_LOCAL)) { ?>
        <span 
            class="icon-wrapper cursor-pointer" 
            title="<?php esc_attr_e('The Backup is in Local Storage', 'gamb-duplica-wp') ?>" 
            onclick="DupPro.Pack.ShowRemote(<?php echo (int) $package->ID; ?>, '<?php echo esc_js($package->getNameHash()); ?>');"
        >
            <i class="fa-solid fa-hard-drive"></i>
        </span>
    <?php } ?>
    <?php if ($package->hasFlag(GAMB_DUPLICA_WP_Package::FLAG_HAVE_REMOTE)) { ?>
        <span 
            class="icon-wrapper cursor-pointer remote-storage-flag" 
            title="<?php esc_attr_e('The Backup is in Remote Storage', 'gamb-duplica-wp') ?>" 
            onclick="DupPro.Pack.ShowRemote(<?php echo (int) $package->ID; ?>, '<?php echo esc_js($package->getNameHash()); ?>');"
        >
            <i class="fa-solid fa-cloud"></i>
        </span>
    <?php } ?>
    <?php if ($package->hasFlag(GAMB_DUPLICA_WP_Package::FLAG_DB_ONLY)) { ?>
        <span class="icon-wrapper" title="<?php esc_attr_e('Database Only Backup', 'gamb-duplica-wp') ?>" >
            <i class="fa-solid fa-database"></i>
        </span>
    <?php } ?>
    <?php if ($package->hasFlag(GAMB_DUPLICA_WP_Package::FLAG_MEDIA_ONLY)) { ?>
        <span class="icon-wrapper" title="<?php esc_attr_e('Media Only Backup', 'gamb-duplica-wp') ?>" >
            <i class="fa-solid fa-images"></i>
        </span>
    <?php } ?>
    <?php if (CapMng::can(CapMng::CAP_BACKUP_RESTORE, false)) { ?>
        <?php if ($package->hasFlag(GAMB_DUPLICA_WP_Package::FLAG_DISASTER_AVAIABLE) || $package->hasFlag(GAMB_DUPLICA_WP_Package::FLAG_DISASTER_SET)) {
            if ($package->hasFlag(GAMB_DUPLICA_WP_Package::FLAG_DISASTER_SET)) {
                $title      = __("Disaster Recovery URL is set on this Backup", 'gamb-duplica-wp');
                $colorClass = 'green';
            } else {
                $colorClass = '';
                $title      = __('This Backup is available for Disaster Recovery', 'gamb-duplica-wp');
            }
            ?>
            <span 
                class="dpro-btn-open-recovery-box icon-wrapper link-style no-decoration" 
                aria-label="<?php echo esc_attr($title); ?>"
                title="<?php echo esc_attr($title); ?>"
                data-package-id="<?php echo (int) $package->ID; ?>"
            >
                <?php ViewHelper::disasterIcon(true, $colorClass); ?>
            </span>
        <?php } ?>
    <?php } ?>
    <?php if ($package->hasFlag(GAMB_DUPLICA_WP_Package::FLAG_CREATED_AFTER_RESTORE)) { ?>
        <span class="icon-wrapper" title="<?php esc_attr_e('This Backup is created after the Last Restored Backup', 'gamb-duplica-wp') ?>" >
            <i class="fa-solid fa-clock-rotate-left maroon"></i>
        </span>
    <?php } ?>
</div>
