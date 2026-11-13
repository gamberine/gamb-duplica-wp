<?php

/**
 * gamb-duplica-wp messages sections
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Addons\FtpAddon\Models\FTPStorage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var FTPStorage $storage
 */
$storage = $tplData["storage"];
/** @var string */
$server = $tplData["server"];
/** @var int */
$port = $tplData["port"];
/** @var string */
$username = $tplData["username"];
/** @var string */
$password = $tplData["password"];
/** @var string */
$storageFolder = $tplData["storageFolder"];
/** @var int */
$maxPackages =  $tplData["maxPackages"];
/** @var int */
$timeout = $tplData["timeout"];
/** @var bool */
$useCurl = $tplData["useCurl"];
/** @var bool */
$isPassive = $tplData["isPassive"];
/** @var bool */
$useSSL = $tplData["useSSL"];

/** @var bool $forceCurl */
$forceCurl = $tplData["forceCurl"];

$tplMng->render('admin_pages/storages/parts/provider_head');
?>
<tr>
    <td class="dpro-sub-title" colspan="2"><b><?php esc_html_e("Credentials", 'gamb-duplica-wp'); ?></b></td>
</tr>
<tr>
    <th scope="row"><label for="ftp_server"><?php esc_html_e("Server", 'gamb-duplica-wp'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <input 
                id="ftp_server" 
                class="dup-empty-field-on-submit" 
                name="ftp_server" 
                type="text" 
                autocomplete="off" 
                value="<?php echo esc_attr($server); ?>"
                data-parsley-errors-container="#ftp_server_error_container" 
                data-parsley-required="true"
            >
            <label for="ftp_server">
                <?php esc_html_e("Port", 'gamb-duplica-wp'); ?>
            </label>
            <input 
                name="ftp_port" 
                id="ftp_port" 
                type="number" 
                min="1"
                max="65535"
                style="width:75px"  
                value="<?php echo (int) $port; ?>"
                data-parsley-errors-container="#ftp_server_error_container" 
                data-parsley-required="true"
                data-parsley-type="number"
                data-parsley-range="[1, 65535]"
            >
        </div>
        <div id="ftp_server_error_container" class="gamb-duplica-wp-error-container"></div>
    </td>
</tr>
<tr>
    <th scope="row"><label for="ftp_username"><?php esc_html_e("Username", 'gamb-duplica-wp'); ?></label></th>
    <td>
        <input 
            id="ftp_username" 
            class="dup-empty-field-on-submit" 
            name="ftp_username" 
            type="text" 
            autocomplete="off" 
            value="<?php echo esc_attr($username); ?>" 
            data-parsley-errors-container="#ftp_username_error_container" 
            data-parsley-required="true"
        >
        <div id="ftp_username_error_container" class="gamb-duplica-wp-error-container"></div>
    </td>
</tr>
<tr>
    <th scope="row"><label for="ftp_password"><?php esc_html_e("Password", 'gamb-duplica-wp'); ?></label></th>
    <td>
        <input 
            id="ftp_password"
            name="ftp_password" 
            type="password" 
            class="dup-empty-field-on-submit"
            placeholder="<?php echo esc_attr(str_repeat("*", strlen($password))); ?>"
            autocomplete="off" 
            value="" 
            data-parsley-errors-container="#ftp_password_error_container" 
            data-parsley-required="true"
        >
        <div id="ftp_password_error_container" class="gamb-duplica-wp-error-container"></div>
    </td>
</tr>
<tr>
    <th scope="row"><label for="ftp_password2"><?php esc_html_e("Retype Password", 'gamb-duplica-wp'); ?></label></th>
    <td>
        <input 
            id="ftp_password2" 
            class="dup-empty-field-on-submit" 
            name="ftp_password2" 
            type="password" 
            placeholder="<?php echo esc_attr(str_repeat("*", strlen($password))); ?>"
            autocomplete="off" 
            value="" 
            data-parsley-errors-container="#ftp_password2_error_container"  
            data-parsley-trigger="change" 
            data-parsley-equalto="#ftp_password" 
            data-parsley-required="true"
            data-parsley-equalto-message="<?php esc_html_e("Passwords do not match", 'gamb-duplica-wp'); ?>"
        ><br/>
        <div id="ftp_password2_error_container" class="gamb-duplica-wp-error-container"></div>
    </td>
</tr>
<tr>
    <td class="dpro-sub-title" colspan="2"><b><?php esc_html_e("Settings", 'gamb-duplica-wp'); ?></b></td>
</tr>
<tr>
    <th scope="row"><label for="_ftp_storage_folder"><?php esc_html_e("Storage Folder", 'gamb-duplica-wp'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <input 
                id="_ftp_storage_folder" 
                name="_ftp_storage_folder" 
                type="text" 
                value="<?php echo esc_attr($storageFolder); ?>" 
            >
        </div>
        <p>
            <i>
                <?php
                esc_html_e(
                    "Folder where backups will be stored. This should be unique for each web-site using gamb-duplica-wp.",
                    'gamb-duplica-wp'
                ); ?>
            </i>
        </p>
    </td>
</tr>
<tr>
    <th scope="row"><label for="ftp_max_files"><?php esc_html_e("Max Backups", 'gamb-duplica-wp'); ?></label></th>
    <td>
        <label for="ftp_max_files">
            <div class="horizontal-input-row">
                <input 
                    id="ftp_max_files" 
                    name="ftp_max_files" 
                    type="number" 
                    value="<?php echo (int) $maxPackages; ?>" 
                    min="0"
                    maxlength="4"
                    data-parsley-errors-container="#ftp_max_files_error_container" 
                    data-parsley-required="true" 
                    data-parsley-type="number"
                    data-parsley-min="0"
                >
                <label for="ftp_max_files"><?php esc_html_e("Number of Backups to keep in folder.", 'gamb-duplica-wp'); ?></label>
            </div>
            <?php $tplMng->render('admin_pages/storages/parts/max_backups_description'); ?>
        </label>
        <div id="ftp_max_files_error_container" class="gamb-duplica-wp-error-container"></div>
    </td>
</tr>
<tr>
    <th scope="row"><label for="ftp_timeout_in_secs"><?php esc_html_e("Timeout", 'gamb-duplica-wp'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <input 
                id="ftp_timeout" 
                name="ftp_timeout_in_secs" 
                type="number"
                min="10" 
                value="<?php echo (int) $timeout; ?>"
                data-parsley-errors-container="#ftp_timeout_error_container" 
                data-parsley-required="true"
                data-parsley-type="number"
                data-parsley-min="10"
            > 
            <label for="ftp_timeout_in_secs">
                <?php esc_html_e("seconds", 'gamb-duplica-wp'); ?>
            </label>
        </div>
        <p>
            <i><?php esc_html_e("Do not modify this setting unless you know the expected result or have talked to support.", 'gamb-duplica-wp'); ?> </i>
        </p>
        <div id="ftp_timeout_error_container" class="gamb-duplica-wp-error-container"></div>
    </td>
</tr>
<tr>
    <th scope="row"><label for="ftp_ssl"><?php esc_html_e("Explicit SSL", 'gamb-duplica-wp'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <input name="_ftp_ssl" <?php checked($useSSL); ?> class="checkbox" value="1" type="checkbox" id="_ftp_ssl" >
            <label for="_ftp_ssl"><?php esc_html_e("Enable", 'gamb-duplica-wp'); ?></label>
        </div>
    </td>
</tr>
<tr>
    <th scope="row"><label for="_ftp_passive_mode"><?php esc_html_e("Passive Mode", 'gamb-duplica-wp'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <input 
                <?php checked($isPassive); ?> 
                class="checkbox" 
                value="1" 
                type="checkbox" 
                name="_ftp_passive_mode" 
                id="_ftp_passive_mode"
            >
            <label for="_ftp_passive_mode"><?php esc_html_e("Enable", 'gamb-duplica-wp'); ?></label>
        </div>
    </td>
</tr>
<tr>
    <th scope="row"><label for="_ftp_use_curl"><?php esc_html_e("cURL", 'gamb-duplica-wp'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <input
                type="checkbox" 
                name="_ftp_use_curl" 
                id="_ftp_use_curl"
                value="1"
                <?php checked($useCurl); ?>
                <?php disabled($forceCurl); ?>
            >
            <label for="_ftp_use_curl"><?php esc_html_e("Enable", 'gamb-duplica-wp'); ?></label>
        </div>
        <?php if ($forceCurl) : ?>
            <p>
                <i>
                    <?php
                    esc_html_e(
                        "cURL is enabled by default as the FTP extension is not available. This setting cannot be changed unless you enable the FTP extension.",
                        'gamb-duplica-wp'
                    ); ?>
                </i>
            </p>
        <?php else : ?>
            <p><i><?php esc_html_e("PHP cURL. Only check if connection test recommends it.", 'gamb-duplica-wp'); ?></i></p>
        <?php endif; ?>
        <p>
            <?php
            echo wp_kses(
                __(
                    "<b>Note:</b> This setting is for FTP and FTPS (FTP/SSL) only. 
                    To use SFTP (SSH File Transfer Protocol) change the type dropdown above.",
                    'gamb-duplica-wp'
                ),
                [
                    'b' => [],
                ]
            );
            ?>
        </p>
    </td>
</tr>
<?php $tplMng->render('admin_pages/storages/parts/provider_foot'); ?>
