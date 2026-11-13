<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Utils\Crypt\CryptBlowfish;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$global = GAMB_DUPLICA_WP_Global_Entity::getInstance();

?>

<h3 class="title">
    <?php esc_html_e("Plugin", 'gamb-duplica-wp') ?>
</h3>
<hr size="1" />

<label class="lbl-larger">
    <?php esc_html_e("Version", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <?php echo esc_html(gamb-duplica-wp_PRO_VERSION); ?>
</div>

<label class="lbl-larger">
    <?php esc_html_e("Uninstall", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <input
        type="checkbox"
        name="uninstall_settings"
        id="uninstall_settings"
        value="1"
        class="margin-0"
        <?php checked($global->uninstall_settings); ?>
    >
    <label for="uninstall_settings"><?php esc_html_e("Delete plugin settings", 'gamb-duplica-wp'); ?> </label><br />

    <input
        type="checkbox"
        name="uninstall_packages"
        id="uninstall_packages"
        value="1"
        class="margin-0"
        <?php checked($global->uninstall_packages); ?>
    >
    <label for="uninstall_packages"><?php esc_html_e("Delete entire storage directory", 'gamb-duplica-wp'); ?></label><br />
</div>

<label class="lbl-larger">
    <?php esc_html_e("Encrypt Settings", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <input
        type="checkbox"
        name="crypt"
        id="crypt"
        value="1"
        class="margin-0"
        <?php checked($global->isEncryptionEnabled()); ?>
    >
    <label for="crypt"><?php esc_html_e("Enable settings encryption", 'gamb-duplica-wp'); ?> </label><br />
    <p class="description">
        <?php if (CryptBlowfish::isEncryptAvailable()) { ?>
            <?php esc_html_e(
                "When this option is enabled, all sensitive data (such as passwords, storage data, and license data) 
                will be saved encrypted in the database. Disable this option only in case of problems in saving data.",
                'gamb-duplica-wp'
            ); ?>
        <?php } else { ?>
            <span class="maroon">
                <?php esc_html_e('Encryption is not available on this server.', 'gamb-duplica-wp'); ?>
            </span>
        <?php } ?>
    </p>
</div>

<label class="lbl-larger">
    <?php esc_html_e("Usage statistics", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <?php if (gamb-duplica-wp_USTATS_DISALLOW) {  // @phpstan-ignore-line ?>
        <span class="maroon">
            <?php esc_html_e('Usage statistics are hardcoded disallowed.', 'gamb-duplica-wp'); ?>
        </span>
    <?php } else { ?>
        <input
            type="checkbox"
            name="usage_tracking"
            id="usage_tracking"
            value="1"
            class="margin-0"
            <?php checked($global->getUsageTracking()); ?>
        >
        <label for="usage_tracking"><?php esc_html_e("Enable usage tracking", 'gamb-duplica-wp'); ?> </label>
        <i 
            class="fa-solid fa-question-circle fa-sm dark-gray-color" 
            data-tooltip-title="<?php esc_attr_e("Usage Tracking", 'gamb-duplica-wp'); ?>" 
            data-tooltip="<?php echo esc_attr($tplMng->render('admin_pages/settings/general/usage_tracking_tooltip', [], false)); ?>"
            data-tooltip-width="600"
        >
        </i>
    <?php } ?>
</div>

<label class="lbl-larger">
    <?php esc_html_e("Hide Announcements", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <input
        type="checkbox"
        name="dup_am_notices"
        id="dup_am_notices"
        value="1"
        class="margin-0"
        <?php checked(!$global->isAmNoticesEnabled()); ?>
    >
    <label for="dup_am_notices">
        <?php esc_html_e("Check this option to hide plugin announcements and update details.", 'gamb-duplica-wp'); ?>
    </label>
</div>
