<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

use gamb-duplica-wp\Controllers\SettingsPageController;
use gamb-duplica-wp\Core\Controllers\ControllersManager;

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var gamb-duplica-wp\Core\Controllers\PageAction[] $tplData['actions']
 */

$global = GAMB_DUPLICA_WP_Global_Entity::getInstance();
?>
<form 
    id="dup-settings-form" 
    action="<?php echo esc_attr(ControllersManager::getCurrentLink()); ?>" 
    method="post" data-parsley-validate
>
    <?php $tplData['actions'][SettingsPageController::ACTION_SAVE_SCHEDULE]->getActionNonceFileds(); ?>
    <h3 class="title"><?php esc_html_e("Schedule Notifications", 'gamb-duplica-wp'); ?></h3>
    <hr size="1" />
    <div class="dup-settings-wrapper margin-bottom-1" >
        <label class="lbl-larger" for="send_email_on_build_mode" >
            <?php esc_html_e("Send Build Email", 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1" >
            <input 
                type="radio" 
                name="send_email_on_build_mode" 
                id="send_email_on_build_mode_never" 
                class="margin-0"
                value="<?php echo (int) GAMB_DUPLICA_WP_Email_Build_Mode::No_Emails; ?>" 
                <?php checked($global->send_email_on_build_mode, GAMB_DUPLICA_WP_Email_Build_Mode::No_Emails); ?> 
            >
            <label for="send_email_on_build_mode_never"><?php esc_attr_e("Never", 'gamb-duplica-wp'); ?></label> &nbsp;
            <input 
                type="radio" 
                name="send_email_on_build_mode" 
                id="send_email_on_build_mode_failure" 
                class="margin-0"
                value="<?php echo (int) GAMB_DUPLICA_WP_Email_Build_Mode::Email_On_Failure; ?>" 
                <?php checked($global->send_email_on_build_mode, GAMB_DUPLICA_WP_Email_Build_Mode::Email_On_Failure); ?> 
            >
            <label for="send_email_on_build_mode_failure"><?php esc_attr_e("On Failure", 'gamb-duplica-wp'); ?></label> &nbsp;
            <input
                type="radio" 
                name="send_email_on_build_mode" 
                id="send_email_on_build_mode_always"
                class="margin-0" 
                value="<?php echo (int) GAMB_DUPLICA_WP_Email_Build_Mode::Email_On_All_Builds; ?>" 
                <?php checked($global->send_email_on_build_mode, GAMB_DUPLICA_WP_Email_Build_Mode::Email_On_All_Builds); ?> 
            >
            <label for="send_email_on_build_mode_always"><?php esc_attr_e("Always", 'gamb-duplica-wp'); ?></label> &nbsp;
            <p class="description">
                <?php
                esc_html_e("When to send emails after a scheduled build.", 'gamb-duplica-wp');
                ?>
            </p>
        </div>

        <label class="lbl-larger" for="notification_email_address" >
            <?php esc_html_e("Email Address", 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1" >
            <input 
                data-parsley-errors-container="#notification_email_address_error_container" 
                data-parsley-type="email" 
                type="email" 
                name="notification_email_address" 
                id="notification_email_address" 
                class="width-large margin-bottom-0" 
                value="<?php echo esc_attr($global->notification_email_address); ?>" 
            >
            <p class="description">
                <?php esc_html_e('Admininstrator default email will be used if empty.', 'gamb-duplica-wp'); ?>
            </p>
            <div id="notification_email_address_error_container" class="gamb-duplica-wp-error-container"></div>
        </div>
    </div>
    <hr>
    <p class="submit dpro-save-submit">
        <input 
            type="submit" 
            name="submit" 
            id="submit" 
            class="button primary small" 
            value="<?php esc_attr_e('Save Settings', 'gamb-duplica-wp') ?>" 
        >
    </p>
</form>

