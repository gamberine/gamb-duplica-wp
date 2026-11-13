<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Addons\ProBase\License\License;
use gamb-duplica-wp\Addons\ProBase\Models\LicenseData;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$global = GAMB_DUPLICA_WP_Global_Entity::getInstance();

if ($global->license_key_visible === License::VISIBILITY_ALL) {
    ?>
    <div id="dup-tr-license-key-and-description" class="inline-display" >
        <input
            type="text"
            class="dup-license-key-input width-large inline-display margin-0"
            name="_license_key"
            id="_license_key"
            value="<?php echo esc_attr(License::getLicenseKey()); ?>"
        >
        <span class="dup-license-check-icon" >
            <?php if (LicenseData::getInstance()->getStatus() === LicenseData::STATUS_VALID) { ?>
                <i class="fa-solid fa-circle-check fa-lg success-color"></i>
            <?php } else { ?>
                <i class="fa-solid fa-circle-xmark fa-lg alert-color"></i>
            <?php } ?>
        </span>
        &nbsp;
    </div>
<?php } ?>
<div class="dup-license-key-btns inline-display">
    <?php
    if (LicenseData::getInstance()->getStatus() === LicenseData::STATUS_VALID) {
        $echostring = 'false';
        $buttonText = __('Deactivate', 'gamb-duplica-wp');
    } else {
        $echostring = 'true';
        $buttonText = __('Activate', 'gamb-duplica-wp');
    }
    ?>
    <button
        id="dup-license-activation-btn"
        class="button secondary hollow small margin-0"
        onclick="DupPro.Licensing.ChangeActivationStatus(<?php echo esc_js($echostring); ?>);return false;">
        <?php echo esc_html($buttonText); ?>
    </button>
    &nbsp;
    <button 
        id="dup-license-clear-btn"
        class="button secondary hollow small margin-0" 
        onclick="DupPro.Licensing.ClearActivationStatus();return false;"
    >
        <?php esc_html_e('Clear Key', 'gamb-duplica-wp') ?>
    </button>
</div>