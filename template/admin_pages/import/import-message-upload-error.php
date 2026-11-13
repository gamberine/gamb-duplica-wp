<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Controllers\SettingsPageController;
use GambDuplicaWP\Core\Controllers\ControllersManager;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$importSettingsUrl = $ctrlMng->getMenuLink(
    ControllersManager::SETTINGS_SUBMENU_SLUG,
    SettingsPageController::L2_SLUG_IMPORT
);

?>
<div class="dup-pro-import-upload-message" >
    <p class="import-upload-reset-message-error">
        <i class="fa fa-exclamation-triangle"></i> <b><?php esc_html_e('UPLOAD FILE PROBLEM', 'gamb-duplica-wp'); ?></b>
    </p>
    <p>
        <?php esc_html_e('Error message:', 'gamb-duplica-wp'); ?>&nbsp;
        <b><span class="import-upload-error-message"><!-- here is set the message received from the server --></span></b>
    </p>
    <div><?php esc_html_e('Possible solutions:', 'gamb-duplica-wp'); ?></div>
    <ul class="dup-pro-simple-style-list" >
        <li>
            <?php esc_html_e('If you are using Server to Server transfer function make sure the URL is a valid URL', 'gamb-duplica-wp'); ?>
        </li>
        <li>
            <?php
                printf(
                    esc_html_x(
                        'If you are using the upload function try to change the chunk size in %1$ssettings%2$s and try again',
                        '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                        'gamb-duplica-wp'
                    ),
                    '<a href="' . esc_url($importSettingsUrl) . '">',
                    '</a>'
                );
                ?>
        </li>
        <li>
            <?php
                printf(
                    esc_html__('Upload the file via FTP/file manager to the "%s" folder and reload the page.', 'gamb-duplica-wp'),
                    esc_html(GambDuplicaWP_PRO_PATH_IMPORTS)
                );
                ?>
        </li>
    </ul>
    <p>
        <b>
        <?php
        printf(
            esc_html_x(
                'For more information see %1$s[this FAQ item]%2$s',
                '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                'gamb-duplica-wp'
            ),
            '<a href="' . esc_url(GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . 'how-to-handle-import-install-upload-launch-issues') . '" target="_blank">',
            '</a>'
        );
        ?>
        </b>
    </p>
</div>
