<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Package;

use GAMB_DUPLICA_WP_Archive_Build_Mode;
use GAMB_DUPLICA_WP_Global_Entity;
use GambDuplicaWP\Controllers\SettingsPageController;
use GambDuplicaWP\Core\Controllers\ControllersManager;
use GambDuplicaWP\Libs\DupArchive\DupArchive;
use GambDuplicaWP\Utils\ZipArchiveExtended;
use Exception;

class SettingsUtils
{
    /**
     * Return true if archive encryption is available
     *
     * @param string $unavaliableMessage if encryption isn't available the reason is put here
     *
     * @return bool
     */
    public static function isArchiveEncryptionAvailable(&$unavaliableMessage = ''): bool
    {
        try {
            $global   = GAMB_DUPLICA_WP_Global_Entity::getInstance();
            $guideMsg = sprintf(
                _x(
                    'For more details please see the %1$suser guide%2$s',
                    '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                    'gamb-duplica-wp'
                ),
                '<a href="' . GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . 'packages-new-step-1-setup" target="_blank">',
                '</a>'
            );

            switch ($global->archive_build_mode) {
                case GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec:
                    break;
                case GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive:
                    if (ZipArchiveExtended::isEncryptionAvaliable() == false) {
                        $settingsLink = ControllersManager::getInstance()->getMenuLink(
                            ControllersManager::SETTINGS_SUBMENU_SLUG,
                            SettingsPageController::L2_SLUG_PACKAGE
                        );

                        $msg = __('To enable this feature consider the following options:', 'gamb-duplica-wp');
                        ob_start();
                        ?>
                        <ul class="dup-tabs-opts-help">
                            <li>
                                <?php esc_html_e('Upgrade this server to PHP 7.2+ anz Libzip 1.2+ for ZIP encryption support. ', 'gamb-duplica-wp'); ?>
                            </li>
                            <li>
                                <?php
                                    printf(
                                        esc_html_x(
                                            'Change the %1$sArchive Engine%2$s settings to DupArchive.',
                                            '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                                            'gamb-duplica-wp'
                                        ),
                                        '<a href="' . esc_url($settingsLink) . '" target="_blank">',
                                        '</a>'
                                    );
                                ?>
                            </li>
                        </ul>
                        <?php
                        $msg .= ob_get_clean();
                        throw new Exception($msg . $guideMsg);
                    }
                    break;
                case GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive:
                    if (DupArchive::isEncryptionAvaliable() == false) {
                        $msg = sprintf(
                            _x(
                                'To enable encryption on the DupArchive format, contact your host and make sure they have enabled the %1$sOpenSSL module%2$s.',
                                '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                                'gamb-duplica-wp'
                            ),
                            '<a href="https://www.php.net/manual/en/book.openssl.php" target="_blank">',
                            '</a>'
                        );
                        throw new Exception($msg . '<br>' . $guideMsg);
                    }
                    break;
                case GAMB_DUPLICA_WP_Archive_Build_Mode::Unconfigured:
                default:
                    throw new Exception('Invalid build mode');
            }
        } catch (Exception $e) {
            $unavaliableMessage = $e->getMessage();
            return false;
        }

        return true;
    }
}
