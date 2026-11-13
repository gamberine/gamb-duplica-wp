<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var array{name: string,slug: string,more: string,pro: array{file: string}} $plugin
 */
$plugin = $tplData['plugin'];

/** @var string */
$installUrl = $tplData['installUrl'];
$moreUrl    = $plugin['more'] . '?' . http_build_query([
    'utm_medium'   => 'link',
    'utm_source'   => 'gamb-duplica-wpplugin',
    'utm_campaign' => 'gamb-duplica-wpdashboardwidget',
]);

?>
<div class="dup-section-recommended">
    <hr>
    <div class="dup-flex-content" >
        <div>
            <span class="dup-recommended-label">
                <?php esc_html_e('Recommended Plugin:', 'gamb-duplica-wp'); ?>
            </span>
            <b><?php echo esc_html($plugin['name']); ?></b>
            -
            <span class="action-links">
                <?php if (CapMng::can('install_plugins', false) && CapMng::can('activate_plugins', false)) { ?>
                    <a href="<?php echo esc_url($installUrl); ?>"><?php esc_html_e('Install', 'gamb-duplica-wp'); ?></a>
                <?php } ?>
                <a href="<?php echo esc_url($moreUrl); ?>" target="_blank" ><?php
                    esc_html_e('Learn More', 'gamb-duplica-wp');
                ?></a>
            </span>
        </div>
        <div>
            <button type="button" id="dup-dash-widget-section-recommended" title="<?php esc_html_e('Dismiss recommended plugin', 'gamb-duplica-wp'); ?>">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
    </div>
</div>
