<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Addons\ProBase\License\License;
use gamb-duplica-wp\Addons\ProBase\LicensingController;
use gamb-duplica-wp\Core\Controllers\ControllersManager;

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
<div class="dup-accordion-wrapper display-separators close" >
    <div class="accordion-header" >
        <h3 class="title">
            <?php esc_html_e("License Key Visibility", 'gamb-duplica-wp') ?>
        </h3>
    </div>
    <div class="accordion-content">
        <p>
            <?php
            esc_html_e(
                "This is an optional setting that prevents the 'License Key' from being copied. 
                Select the desired visibility mode, enter a password and hit the 'Change Visibility' button.",
                'gamb-duplica-wp'
            );
            ?>
        </p>

        <form
            id="dup-license-visibility-form"
            action="<?php echo esc_url(ControllersManager::getCurrentLink()); ?>"
            method="post"
            data-parsley-validate
        >
            <?php $tplData['actions'][LicensingController::ACTION_CHANGE_VISIBILITY]->getActionNonceFileds(); ?>

            <label class="lbl-larger" >
                <?php esc_html_e("Visibility", 'gamb-duplica-wp'); ?>
            </label>
            <div class="margin-bottom-1" >
                <label class="inline-display margin-right-1">
                    <input
                        type="radio"
                        name="license_key_visible"
                        value="<?php echo (int) License::VISIBILITY_ALL;?>"
                        class="margin-0"
                        onclick="DupPro.Licensing.VisibilityTemporary(<?php echo (int) License::VISIBILITY_ALL;?>);"
                        <?php checked($global->license_key_visible, License::VISIBILITY_ALL); ?>
                    >
                    <?php esc_html_e("License Visible", 'gamb-duplica-wp'); ?>
                </label>
                <label class="inline-display margin-right-1">
                    <input
                        type="radio"
                        name="license_key_visible"
                        value="<?php echo (int) License::VISIBILITY_INFO;?>"
                        class="margin-0"
                        onclick="DupPro.Licensing.VisibilityTemporary(<?php echo (int) License::VISIBILITY_INFO;?>);"
                        <?php checked($global->license_key_visible, License::VISIBILITY_INFO); ?>
                    >
                    <?php esc_html_e("Info Only", 'gamb-duplica-wp'); ?>
                </label>
                <label class="inline-display">
                    <input
                        type="radio"
                        name="license_key_visible"
                        value="<?php echo (int) License::VISIBILITY_NONE;?>"
                        class="margin-0"
                        onclick="DupPro.Licensing.VisibilityTemporary(<?php echo (int) License::VISIBILITY_NONE;?>);"
                        <?php checked($global->license_key_visible, License::VISIBILITY_NONE); ?>
                    >
                    <?php esc_html_e("License Invisible", 'gamb-duplica-wp'); ?>
                </label>
            </div>

            <label class="lbl-larger" >
                <?php esc_html_e("Password", 'gamb-duplica-wp'); ?>
            </label>
            <div class="margin-bottom-1" >
                <input 
                    type="password" 
                    class="dup-wide-input" 
                    name="_key_password" 
                    id="_key_password" size="50" 
                >
            </div>
            <?php if ($global->license_key_visible == License::VISIBILITY_ALL) { ?>
                <label class="lbl-larger" >
                    <?php esc_html_e("Retype Password", 'gamb-duplica-wp'); ?>
                </label>
                <div class="margin-bottom-1" >
                    <input
                        type="password"
                        class="dup-wide-input"
                        name="_key_password_confirmation"
                        id="_key_password_confirmation"
                        data-parsley-equalto="#_key_password"
                        size="50"
                    >
                </div>
            <?php } ?>

            <label class="lbl-larger" >
                &nbsp;
            </label>
            <div class="margin-bottom-1" >
                <button
                    class="button secondary hollow small margin-0"
                    id="show_hide"
                    onclick="DupPro.Licensing.ChangeKeyVisibility(); return false;"
                >
                    <?php  esc_html_e('Change Visibility', 'gamb-duplica-wp'); ?>
                </button>
            </div>
        </form>
    </div>
</div>
