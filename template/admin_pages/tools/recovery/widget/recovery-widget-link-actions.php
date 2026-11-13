<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Package\Recovery\RecoveryPackage;
use gamb-duplica-wp\Views\ViewHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var ?gamb-duplica-wp\Package\Recovery\RecoveryPackage $recoverPackage
 */

$recoverPackage = $tplData['recoverPackage'];
$installerLink  = ($recoverPackage instanceof RecoveryPackage) ? $recoverPackage->getInstallLink() : '';
$disabledClass  = empty($installerLink) ? 'disabled' : '';

if ($tplData['displayCopyLink']) {
    $toolTipContent  = __(
        'The recovery point URL is the link to the recovery point Backup installer. 
        The link will run the installer wizard used to re-install and recover the site. 
        Copy this link and keep it in a safe location to easily restore this site.',
        'gamb-duplica-wp'
    );
    $toolTipContent .= '<br><br><b>';
    $toolTipContent .= __('This URL is valid until another recovery point is set.', 'gamb-duplica-wp');
    $toolTipContent .= '</b>';
    ?>
    <label>
        <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
            data-tooltip-title="<?php esc_attr_e("Recovery Point URL", 'gamb-duplica-wp'); ?>"
            data-tooltip="<?php echo esc_attr($toolTipContent); ?>"
        >
        </i> 
        <b><?php _e('Step 2 ', 'gamb-duplica-wp'); ?>:</b> <i><?php _e('Copy Recovery URL &amp; Store in Safe Place', 'gamb-duplica-wp'); ?></i>
    </label>
    <div class="copy-link <?php echo $disabledClass; ?>"
         data-dup-copy-value="<?php echo esc_url($installerLink); ?>"
         data-dup-copy-title="<?php _e("Copy Recovery URL to clipboard", 'gamb-duplica-wp'); ?>"
         data-dup-copied-title="<?php _e("Recovery URL copied to clipboard", 'gamb-duplica-wp'); ?>" >
        <div class="content" >
            <?php echo empty($installerLink) ? __('Please set the Recovery Point to generate the Recovery URL', 'gamb-duplica-wp') : $installerLink; ?>
        </div>
        <i class="far fa-copy copy-icon"></i>
    </div>
<?php } ?>
<div class="dup-pro-recovery-buttons" >
    <?php
    if ($tplData['displayLaunch']) { ?>
        <a href="<?php echo esc_url($installerLink); ?>"
           class="button primary hollow dup-pro-launch small <?php echo $disabledClass; ?>" target="_blank"
           title="<?php _e('Initiates system recovery using the Recovery Point URL.', 'gamb-duplica-wp'); ?>" 
        >
            <?php ViewHelper::restoreIcon(); ?>&nbsp;<?php _e('Restore Backup', 'gamb-duplica-wp'); ?>
        </a>
        <?php
    }
    if ($tplData['displayDownload']) {
        $title = __(
            'This button downloads a recovery launcher that allows you to perform the recovery with a simple click of the downloaded file.',
            'gamb-duplica-wp'
        );
        ?>
        <button 
            type="button" 
            class="button primary hollow small dup-pro-recovery-download-launcher <?php echo $disabledClass; ?>" 
            title="<?php echo esc_attr($title); ?>"
        >
            <i class="fa fa-rocket" ></i>&nbsp;<?php _e('Download Launcher', 'gamb-duplica-wp'); ?>
        </button>
        <?php
    }
    if ($tplData['displayCopyButton']) {
        ?>
        <button type="button" class="button primary small hollow dup-pro-recovery-copy-url <?php echo $disabledClass; ?>" 
                data-dup-copy-value="<?php echo $installerLink; ?>"
                data-dup-copy-title="<?php _e("Copy Recovery URL to clipboard", 'gamb-duplica-wp'); ?>"
                data-dup-copied-title="<?php _e("Recovery URL copied to clipboard", 'gamb-duplica-wp'); ?>" >
            <i class="far fa-copy copy-icon"></i>&nbsp;<?php _e('Copy LINK', 'gamb-duplica-wp'); ?>
        </button>
        <?php
    }
    ?>
</div>
