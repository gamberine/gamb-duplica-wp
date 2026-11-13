<?php

/**
 * gamb-duplica-wp messages sections
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Addons\AmazonS3Addon\Models\AmazonS3Storage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var AmazonS3Storage $storage
 */
$storage = $tplData["storage"];
/** @var int */
$maxPackages =  $tplData["maxPackages"];
/** @var string */
$storageFolder = $tplData["storageFolder"];
/** @var string */
$accessKey = $tplData["accessKey"];
/** @var string */
$bucket = $tplData["bucket"];
/** @var string */
$region = $tplData["region"];
/** @var string */
$secretKey = $tplData["secretKey"];
/** @var string */
$storageClass = $tplData["storageClass"];
/** @var string */
$endpoint = $tplData["endpoint"];
/** @var string */
$aclFullControl = $tplData["aclFullControl"];
/** @var array<string,string> */
$regionOptions = $tplData["regionOptions"];

$tplMng->render('admin_pages/storages/parts/provider_head');
?>
<tr>
    <td colspan="2" style="padding-left:0">
        <i>
            <?php
            printf(
                esc_html_x(
                    'Amazon S3 Setup Guide: %1$sStep-by-Step%2$s and %3$sUser Bucket Policy%4$s.',
                    '1,3 represents <a> tag, 2,4 represents </a> tag',
                    'gamb-duplica-wp'
                ),
                '<a target="_blank" href="' . esc_url(GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . 'amazon-s3-step-by-step') . '">',
                '</a>',
                '<a href="' . esc_url(GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . 'amazon-s3-policy-setup') . '" target="_blank">',
                '</a>'
            );
            ?>
        </i>
    </td>
</tr>
<tr>
    <th scope="row"><label for=""><?php esc_html_e("Authorization", 'gamb-duplica-wp'); ?></label></th>
    <td class="dup-s3-auth-account">
        <h3>
            <?php esc_html_e('Amazon Account', 'gamb-duplica-wp'); ?><br/>
        </h3>
        <table class="dup-form-sub-area">
            <tr>
                <th scope="row"><label for="s3_access_key_amazon"><?php esc_html_e("Access Key", 'gamb-duplica-wp'); ?>:</label></th>
                <td>
                    <input 
                        id="s3_access_key_amazon" 
                        class="margin-0"
                        name="s3_access_key" 
                        data-parsley-errors-container="#s3_access_key_amazon_error_container" 
                        type="text" 
                        autocomplete="off" 
                        value="<?php echo esc_attr($accessKey); ?>"
                    >
                    <div id="s3_access_key_amazon_error_container" class="gamb-duplica-wp-error-container"></div>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="s3_secret_key_amazon"><?php esc_html_e("Secret Key", 'gamb-duplica-wp'); ?>:</label>
                </th>

                <td>
                    <input
                        id="s3_secret_key_amazon"
                        name="s3_secret_key"
                        class="margin-0"
                        type="password"
                        placeholder="<?php echo esc_attr(str_repeat("*", strlen($secretKey))); ?>"
                        data-parsley-errors-container="#s3_secret_key_amazon_error_container"
                        autocomplete="off"
                        value=""
                    >
                    <div id="s3_secret_key_amazon_error_container" class="gamb-duplica-wp-error-container"></div>
                </td>
            </tr>
        </table>
    </td>
</tr>            
<tr>
    <th scope="row"></th>
    <td>
        <table class="dup-form-sub-area dup-s3-auth-provider">
            <tr>
                <th><label for="s3_region_amazon"><?php esc_html_e("Region", 'gamb-duplica-wp'); ?>:</label></th>
                <td>
                    <select id="s3_region_amazon" name="s3_region" class="margin-0 width-large">
                        <?php
                        foreach ($regionOptions as $value => $label) {
                            ?>
                            <option
                                <?php selected($region, $value); ?>
                                value="<?php echo esc_attr($value); ?>"
                            >
                                <?php echo esc_html($label . " - '" . $value . "'"); ?>
                            </option>
                            <?php
                        }
                        ?>                                    
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="s3_storage_class_amazon"><?php esc_html_e("Storage Class", 'gamb-duplica-wp'); ?>:</label></th>
                <td>
                    <select id="s3_storage_class_amazon" name="s3_storage_class" class="margin-0 width-large">
                        <option <?php selected($storageClass == 'REDUCED_REDUNDANCY'); ?> value="REDUCED_REDUNDANCY">
                            <?php esc_html_e("Reduced Redundancy", 'gamb-duplica-wp'); ?>
                        </option>
                        <option <?php selected($storageClass == 'STANDARD'); ?> value="STANDARD">
                            <?php esc_html_e("Standard", 'gamb-duplica-wp'); ?>
                        </option>
                        <option <?php selected($storageClass == 'STANDARD_IA'); ?> value="STANDARD_IA">
                            <?php esc_html_e("Standard IA", 'gamb-duplica-wp'); ?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="_s3_storage_folder_amazon"><?php esc_html_e("Storage Folder", 'gamb-duplica-wp'); ?>:</label></th>
                <td>
                    <input 
                        id="_s3_storage_folder_amazon" 
                        class="margin-0"
                        name="_s3_storage_folder" 
                        type="text" 
                        value="<?php echo esc_attr($storageFolder); ?>"
                    >
                    <p>
                        <i>
                            <?php esc_html_e(
                                "Folder where backups will be stored. This should be unique for each web-site using gamb-duplica-wp.",
                                'gamb-duplica-wp'
                            ); ?>
                        </i>
                    </p>
                </td>
            </tr>
        </table>

    </td>
</tr>
<tr>
    <th scope="row"><label for="s3_bucket_amazon"><?php esc_html_e("Bucket", 'gamb-duplica-wp'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <input id="s3_bucket_amazon" name="s3_bucket" type="text" value="<?php echo esc_attr($bucket); ?>">
        </div>
        <p><i><?php esc_html_e("S3 Bucket where you want to save the backups.", 'gamb-duplica-wp'); ?></i></p>
    </td>
</tr>
<tr>
    <th scope="row"><label for="s3_max_files_amazon"><?php esc_html_e("Max Backups", 'gamb-duplica-wp'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <input 
                id="s3_max_files_amazon" 
                class="s3_max_files margin-0"
                name="s3_max_files" 
                type="number" 
                value="<?php echo (int) $maxPackages; ?>" 
                min="0"
                maxlength="4"
                data-parsley-errors-container="#s3_max_files_amazon_error_container" 
                data-parsley-required="true" 
                data-parsley-type="number"
                data-parsley-min="0"
            >
            <label for="s3_max_files_amazon">
                <?php esc_html_e("Number of Backups to keep in folder.", 'gamb-duplica-wp'); ?><br/>
            </label>
        </div>
        <?php $tplMng->render('admin_pages/storages/parts/max_backups_description'); ?>
        <div id="s3_max_files_amazon_error_container" class="gamb-duplica-wp-error-container"></div>
    </td>
</tr>
<?php $tplMng->render('admin_pages/storages/parts/provider_foot'); ?>
