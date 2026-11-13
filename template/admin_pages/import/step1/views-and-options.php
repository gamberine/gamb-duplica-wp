<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Controllers\SettingsPageController;
use gamb-duplica-wp\Core\CapMng;
use gamb-duplica-wp\Core\Controllers\ControllersManager;

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
<div id="dup-pro-import-vews-and-opt-wrapper" >
    <ul class="dup-pro-toggle-sub-menu">
        <li>
            <span class="dup-pro-toggle" >
                <span class="button" >
                    <i class="fas fa-bars fa-lg"></i><span class="screen-reader-text">Options</span>
                </span>
            </span>
            <ul>
                <li class="title"><?php esc_html_e('VIEWS', 'gamb-duplica-wp'); ?></li>
                <li>
                    <span class="link-style no-decoration dup-pro-import-view-list <?php echo $tplData['viewMode'] == 'list' ? 'active' : ''; ?>">
                        <?php esc_html_e('Advanced mode', 'gamb-duplica-wp'); ?>
                        <span class="description"><?php esc_html_e('View multiple Backups', 'gamb-duplica-wp') ?></span>
                    </span>
                </li>
                <li>
                    <span class="link-style no-decoration dup-pro-import-view-single <?php echo $tplData['viewMode'] == 'single' ? 'active' : ''; ?>">
                        <?php esc_html_e('Basic mode', 'gamb-duplica-wp') ?>
                        <span class="description"><?php esc_html_e('View last uploaded Backup', 'gamb-duplica-wp'); ?></span>
                    </span>
                </li>
                <li class="title separator"><?php esc_html_e('TOOLS', 'gamb-duplica-wp'); ?></li>
                <?php if (CapMng::can(CapMng::CAP_SETTINGS, false)) { ?>
                    <li>
                        <a class="no-decoration" href="<?php echo esc_url($importSettingsUrl); ?>" target="_blank">
                            <?php esc_html_e('Import Settings', 'gamb-duplica-wp'); ?>
                        </a>&nbsp;
                        <i class="fas fa-external-link-alt fa-small" ></i>
                    </li>
                <?php } ?>
                <li>
                    <span class="link-style no-decoration dup-pro-open-help-link">
                        <a href="<?php echo esc_url(gamb-duplica-wp_PRO_gamb-duplica-wp_DOCS_URL . 'import-install'); ?>" target="_blank">
                            <?php esc_html_e('Quick Start', 'gamb-duplica-wp'); ?>
                        </a>&nbsp;
                        <i class="fas fa-external-link-alt fa-small" ></i>
                    </span>
                </li>
            </ul>
        </li>
    </ul>
</div>
