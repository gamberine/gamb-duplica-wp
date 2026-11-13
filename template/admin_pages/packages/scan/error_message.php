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

?>
<br>
<b><?php esc_html_e('Please Retry:', 'gamb-duplica-wp'); ?></b><br>
<?php esc_html_e('Unable to perform a full scan and read JSON file, please try the following actions.', 'gamb-duplica-wp'); ?><br>
<?php esc_html_e('1. Go back and create a root path directory filter to validate the site is scan-able.', 'gamb-duplica-wp'); ?><br>
<?php esc_html_e('2. Continue to add/remove filters to isolate which path is causing issues.', 'gamb-duplica-wp'); ?><br>
<?php esc_html_e('3. This message will go away once the correct filters are applied.', 'gamb-duplica-wp'); ?><br>
<br>
<b><?php esc_html_e('Common Issues:', 'gamb-duplica-wp'); ?></b><br>
<?php
esc_html_e(
    '- On some budget hosts scanning over 30k files can lead to timeout/gateway issues. 
    Consider scanning only your main WordPress site and avoid trying to backup other external directories.',
    'gamb-duplica-wp'
);
?><br>
<?php
esc_html_e(
    '- Symbolic link recursion can cause timeouts.  Ask your server admin if any are present in the scan path. 
    If they are add the full path as a filter and try running the scan again.',
    'gamb-duplica-wp'
); ?><br>
<br>
<b><?php esc_html_e('Details:', 'gamb-duplica-wp'); ?></b><br>
<?php esc_html_e('JSON Service:', 'gamb-duplica-wp'); ?> /wp-admin/admin-ajax.php?action=gamb-duplica-wp_pro_package_scan<br>
<?php esc_html_e('Scan Path:', 'gamb-duplica-wp'); ?> [<?php echo esc_html(GambDuplicaWP_pro_get_home_path()); ?>]<br><br>

<b><?php esc_html_e('More Information:', 'gamb-duplica-wp'); ?></b><br>
<?php
printf(
    esc_html__(
        'Please see the online FAQ titled %1$s"How to resolve scanner warnings/errors and timeout issues?"%2$s',
        'gamb-duplica-wp'
    ),
    '<a href="' . esc_url(GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . 'how-to-resolve-scanner-warnings-errors-and-timeout-issues') . '" target="_blank">',
    '</a>'
);
