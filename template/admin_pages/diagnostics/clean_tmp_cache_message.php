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

if (!isset($tplData['tmpCleanUpSuccess'])) {
    return;
}

$messageClasses = [
    'notice',
    'dpro-admin-notice',
    'is-dismissible',
    'dpro-diagnostic-action-tmp-cache',
    'notice-success',
];

?>
<div id="message" class="<?php echo esc_attr(implode(' ', $messageClasses)); ?>">
    <p>
        <?php esc_html_e('Build cache removed.', 'gamb-duplica-wp'); ?>
    </p>
</div>
