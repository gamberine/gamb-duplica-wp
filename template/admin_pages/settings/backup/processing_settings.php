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

<h3 class="title">
    <?php esc_html_e("Processing", 'gamb-duplica-wp') ?>
</h3>
<hr size="1" />

<label class="lbl-larger" >
    <?php esc_html_e("Server Throttle", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <input 
        type="radio" 
        name="server_load_reduction" 
        id="server_load_reduction_off" 
        value="<?php echo (int) GAMB_DUPLICA_WP_Server_Load_Reduction::None; ?>"
        class="margin-0"
        <?php checked($global->server_load_reduction, GAMB_DUPLICA_WP_Server_Load_Reduction::None); ?> 
    >
    <label for="server_load_reduction_off">
        <?php esc_html_e("Off", 'gamb-duplica-wp'); ?>
    </label> &nbsp;
    <input 
        type="radio" 
        name="server_load_reduction"  
        id="server_load_reduction_low" 
        value="<?php echo (int) GAMB_DUPLICA_WP_Server_Load_Reduction::A_Bit; ?>"
        class="margin-0"
        <?php checked($global->server_load_reduction, GAMB_DUPLICA_WP_Server_Load_Reduction::A_Bit); ?>  
    >
    <label for="server_load_reduction_low">
        <?php esc_html_e("Low", 'gamb-duplica-wp'); ?>
    </label> &nbsp;
    <input 
        type="radio" 
        name="server_load_reduction"  
        id="server_load_reduction_medium" 
        value="<?php echo (int) GAMB_DUPLICA_WP_Server_Load_Reduction::More; ?>"
        class="margin-0"
        <?php checked($global->server_load_reduction, GAMB_DUPLICA_WP_Server_Load_Reduction::More); ?>  
    >
    <label for="server_load_reduction_medium">
        <?php esc_html_e("Medium", 'gamb-duplica-wp'); ?>
    </label> &nbsp;
    <input 
        type="radio" 
        name="server_load_reduction"  
        id="server_load_reduction_high" 
        value="<?php echo (int) GAMB_DUPLICA_WP_Server_Load_Reduction::A_Lot ?>"
        class="margin-0"
        <?php checked($global->server_load_reduction, GAMB_DUPLICA_WP_Server_Load_Reduction::A_Lot); ?>  
    >
    <label for="server_load_reduction_high">
        <?php esc_html_e("High", 'gamb-duplica-wp'); ?>
    </label>
    <p class="description">
        <?php esc_html_e(
            "Throttle to prevent resource complaints on budget hosts. The higher the value the slower the backup.",
            'gamb-duplica-wp'
        ); ?>
    </p>
</div>

<label class="lbl-larger" >
    <?php esc_html_e("Max Build Time", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <input 
        data-parsley-required data-parsley-errors-container="#max_package_runtime_in_min_error_container"
        data-parsley-min="0"
        data-parsley-type="number"
        class="inline-display width-small margin-0"
        type="text" 
        name="max_package_runtime_in_min" 
        id="max_package_runtime_in_min" 
        value="<?php echo (int) $global->max_package_runtime_in_min; ?>"
    >
    <span>&nbsp;<?php esc_html_e('Minutes', 'gamb-duplica-wp'); ?></span>
    <div id="max_package_runtime_in_min_error_container" class="gamb-duplica-wp-error-container"></div>
    <p class="description">
        <?php esc_html_e('Max build time until Backup is auto-cancelled. Set to 0 for no limit.', 'gamb-duplica-wp'); ?>
    </p>
</div>

<label class="lbl-larger" >
    <?php esc_html_e("Max Transfer Time", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <input 
        data-parsley-required data-parsley-errors-container="#max_package_transfer_time_in_min_error_container"
        data-parsley-min="1"
        data-parsley-type="number"
        class="inline-display width-small margin-0"
        type="text" 
        name="max_package_transfer_time_in_min" 
        id="max_package_transfer_time_in_min" 
        value="<?php echo (int) $global->max_package_transfer_time_in_min; ?>"
    >
    <span>&nbsp;<?php esc_html_e('Minutes', 'gamb-duplica-wp'); ?></span>
    <div id="max_package_transfer_time_in_min_error_container" class="gamb-duplica-wp-error-container"></div>
    <p class="description">
        <?php esc_html_e('Max Backup transfer time in minutes until the transfer is auto-cancelled.', 'gamb-duplica-wp'); ?>
    </p>
</div>
