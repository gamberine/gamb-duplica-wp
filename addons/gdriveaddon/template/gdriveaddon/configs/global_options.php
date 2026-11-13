<?php

/**
 * gamb-duplica-wp messages sections
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Addons\GDriveAddon\Models\GDriveStorage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<div class="dup-accordion-wrapper display-separators close" >
    <div class="accordion-header" >
        <h3 class="title"><?php echo esc_html(GDriveStorage::getStypeName()); ?></h3>
    </div>
    <div class="accordion-content">
        <label class="lbl-larger" >
            <?php esc_html_e("Upload Chunk Size", 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1" >
            <input
                class="text-right inline-display width-small margin-bottom-0"
                name="gdrive_upload_chunksize_in_kb"
                id="gdrive_upload_chunksize_in_kb"
                type="number"
                min="256"
                step="256"
                data-parsley-required
                data-parsley-type="number"
                data-parsley-errors-container="#gdrive_upload_chunksize_in_kb_error_container"
                value="<?php echo (int) $tplData['uploadChunkSize']; ?>"
            >&nbsp;<b>KB</b>
            <div id="gdrive_upload_chunksize_in_kb_error_container" class="gamb-duplica-wp-error-container"></div>
            <p class="description">
                <?php esc_html_e(
                    'How much should be uploaded to Google Drive per attempt. Higher=faster but less reliable. It should be multiple of 256.',
                    'gamb-duplica-wp'
                ); ?>
            </p>
        </div>

        <label class="lbl-larger" >
            <?php esc_html_e("Download Chunk Size", 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1" >
            <input
                class="text-right inline-display width-small margin-bottom-0"
                name="gdrive_download_chunksize_in_kb"
                id="gdrive_download_chunksize_in_kb"
                type="number"
                min="256"
                step="256"
                data-parsley-required
                data-parsley-type="number"
                data-parsley-errors-container="#gdrive_download_chunksize_in_kb_error_container"
                value="<?php echo (int) $tplData['downloadChunkSize']; ?>"
            >&nbsp;<b>KB</b>
            <div id="gdrive_download_chunksize_in_kb_error_container" class="gamb-duplica-wp-error-container"></div>
            <p class="description">
                <?php esc_html_e(
                    'How much should be downloaded from Google Drive per attempt.',
                    'gamb-duplica-wp'
                ); ?>
            </p>
        </div>

        <label class="lbl-larger" >
            <?php esc_html_e("Transfer Mode", 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1" >
            <input
                type="radio"
                class="margin-0"
                value="<?php echo (int) GAMB_DUPLICA_WP_Google_Drive_Transfer_Mode::Auto ?>"
                name="gdrive_transfer_mode" id="gdrive_transfer_mode_auto"
                <?php checked($tplData['transferMode'], GAMB_DUPLICA_WP_Google_Drive_Transfer_Mode::Auto); ?>
            >
            <label for="gdrive_transfer_mode_auto"><?php esc_html_e("Auto", 'gamb-duplica-wp'); ?></label> &nbsp;

            <input
                type="radio" <?php disabled(!GAMB_DUPLICA_WP_Server::isURLFopenEnabled()) ?>
                class="margin-0"
                value="<?php echo (int) GAMB_DUPLICA_WP_Google_Drive_Transfer_Mode::FOpen_URL ?>"
                name="gdrive_transfer_mode"
                id="gdrive_transfer_mode_stream"
                <?php checked($tplData['transferMode'], GAMB_DUPLICA_WP_Google_Drive_Transfer_Mode::FOpen_URL); ?>
            >
            <label for="gdrive_transfer_mode_stream"><?php esc_html_e("FOpen URL", 'gamb-duplica-wp'); ?></label> &nbsp;
            <?php if (!GAMB_DUPLICA_WP_Server::isURLFopenEnabled()) : ?>
                <i
                    class="fa-solid fa-question-circle fa-sm dark-gray-color"
                    data-tooltip-title="<?php esc_attr_e("FOpen URL", 'gamb-duplica-wp'); ?>"
                    data-tooltip="<?php esc_attr_e('Not available because "allow_url_fopen" is turned off in the php.ini', 'gamb-duplica-wp'); ?>">
                </i>
            <?php endif; ?>
        </div>
    </div>
</div>