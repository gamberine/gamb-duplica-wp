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

$global = GAMB_DUPLICA_WP_Global_Entity::getInstance();

?>

<div class="dup-accordion-wrapper display-separators close" >
    <div class="accordion-header" >
        <h3 class="title" id="advanced-section-header">
            <?php esc_html_e('Advanced', 'gamb-duplica-wp') ?>
        </h3>
    </div>
        <div class="accordion-content">

        <label class="lbl-larger">
            <?php esc_html_e('Settings', 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1">
            <button 
                id="dup-pro-reset-all" 
                class="button secondary hollow small margin-0" 
                onclick="DupPro.Pack.ConfirmResetAll(); return false"
            >
                <i class="fas fa-redo fa-sm"></i> <?php esc_html_e('Reset All Settings', 'gamb-duplica-wp'); ?>
            </button>
            <p class="description">
                <?php
                    esc_html_e("Reset all settings to their defaults.", 'gamb-duplica-wp');
                    $tContent = __(
                        'Resets standard settings to defaults. Does not affect capabilities, license key, storage or schedules.',
                        'gamb-duplica-wp'
                    );
                    ?>
                <i 
                    class="fa-solid fa-question-circle fa-sm dark-gray-color" 
                    data-tooltip-title="<?php esc_attr_e("Reset Settings", 'gamb-duplica-wp'); ?>" 
                    data-tooltip="<?php echo esc_attr($tContent); ?>"
                >
                </i>
            </p>
        </div>

        <label class="lbl-larger">
            <?php esc_html_e("Foreign JavaScript", 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1">
            <input
                type="checkbox"
                name="_unhook_third_party_js"
                id="_unhook_third_party_js"
                value="1"
                class="margin-0"
                <?php checked($global->unhook_third_party_js); ?>
            >
            <label for="_unhook_third_party_js"><?php esc_html_e("Disable", 'gamb-duplica-wp'); ?></label> <br />
            <p class="description">
                <?php
                esc_html_e("Check this option if JavaScript from the theme or other plugins conflicts with gamb-duplica-wp Pro pages.", 'gamb-duplica-wp');
                ?>
                <br>
                <?php
                esc_html_e("Do not modify this setting unless you know the expected result or have talked to support.", 'gamb-duplica-wp');
                ?>
            </p>  
        </div>

        <label class="lbl-larger">
            <?php esc_html_e("Foreign CSS", 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1">
            <input
                type="checkbox"
                name="_unhook_third_party_css"
                id="unhook_third_party_css"
                value="1"
                class="margin-0"
                <?php checked($global->unhook_third_party_css); ?>
            >
            <label for="unhook_third_party_css"><?php esc_html_e("Disable", 'gamb-duplica-wp'); ?></label> <br />
            <p class="description">
                <?php
                esc_html_e("Check this option if CSS from the theme or other plugins conflicts with gamb-duplica-wp Pro pages.", 'gamb-duplica-wp');
                ?>
                <br>
                <?php
                esc_html_e("Do not modify this setting unless you know the expected result or have talked to support.", 'gamb-duplica-wp');
                ?>
            </p>
        </div>
    </div>
</div>
