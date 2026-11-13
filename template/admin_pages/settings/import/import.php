<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

use GambDuplicaWP\Controllers\ImportPageController;
use GambDuplicaWP\Controllers\SettingsPageController;
use GambDuplicaWP\Core\Controllers\ControllersManager;

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$global = GAMB_DUPLICA_WP_Global_Entity::getInstance();

?>
<form id="dup-settings-form" action="<?php echo esc_url(ControllersManager::getCurrentLink()); ?>" method="post" data-parsley-validate>
    <?php $tplData['actions'][SettingsPageController::ACTION_IMPORT_SAVE_SETTINGS]->getActionNonceFileds(); ?>

    <h3 id="gamb-duplica-wp-import-settings" class="title">
        <?php esc_html_e("Import Settings", 'gamb-duplica-wp'); ?>
    </h3>
    <hr size="1" />
    <div class="dup-settings-wrapper margin-bottom-1" >
        <label class="lbl-larger" for="input_import_chunk_size" >
            <?php esc_html_e("Upload Chunk Size", 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1" >
            <select name="import_chunk_size" id="input_import_chunk_size" class="postform width-medium margin-bottom-0">
                <?php foreach (ImportPageController::getChunkSizes() as $size => $label) { ?>
                    <option value="<?php echo esc_attr($size); ?>" <?php selected($global->import_chunk_size, $size); ?>>
                        <?php echo esc_html($label); ?>
                    </option>
                <?php } ?>
            </select>
            <p class="description">
                <?php
                    esc_html_e(
                        "If you have issue uploading a Backup start with a lower size. The upload chunk size is ordered from slowest to fastest.",
                        'gamb-duplica-wp'
                    );
                    ?><br/>
                <small>
                    <?php
                        esc_html_e("Note: This setting only applies to the 'Import File' option.", 'gamb-duplica-wp');
                    ?>
                </small>
            </p>
        </div>

        <label class="lbl-larger" for="import_custom_path" >
            <?php esc_html_e("Import custom path", 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1" >
            <input 
                class="width-xxlarge margin-bottom-0" 
                type="text" 
                name="import_custom_path" 
                id="input_import_custom_path" 
                value="<?php echo esc_attr($global->import_custom_path); ?>" 
                placeholder=""
            >
            <p class="description">
                <?php
                esc_html_e(
                    "Setting a custom path does not change the folder where Backups are uploaded but adds a folder to check for Backups list.",
                    'gamb-duplica-wp'
                );
                ?>
                <br>
                <?php
                esc_html_e(
                    "This can be useful when you want to manually upload Backups to another location which can also be a local storage of current or other site.", // phpcs:ignore Generic.Files.LineLength
                    'gamb-duplica-wp'
                );
                ?>
            </p>
        </div>

        <h3 class="title"><?php esc_html_e('Recovery', 'gamb-duplica-wp') ?> </h3>
        <hr size="1" />

        <label class="lbl-larger" for="input_recovery_custom_path" >
            <?php esc_html_e("Recovery custom path", 'gamb-duplica-wp'); ?>
        </label>
        <div class="margin-bottom-1" >
            <input 
                class="width-xxlarge margin-bottom-0" 
                type="text" 
                name="recovery_custom_path" 
                id="input_recovery_custom_path" 
                value="<?php echo esc_attr($global->getRecoveryCustomPath()); ?>" 
                placeholder=""
            >
            <p class="description">
                <?php
                esc_html_e(
                    "Setting a custom path changes the location the recovery points are generated.",
                    'gamb-duplica-wp'
                );
                ?>
            </p>
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