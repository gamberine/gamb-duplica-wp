<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var \GAMB_DUPLICA_WP_Package $package
 */
$package = $tplData['package'];

$timeDiff = sprintf(
    _x('%s ago', '%s represents the time diff, eg. 2 days', 'gamb-duplica-wp'),
    $package->getPackageLife('human')
);

?>
<table>
    <tr>
        <td><b><?php esc_html_e('Backup', 'gamb-duplica-wp'); ?>:</b></td>
        <td><?php echo esc_html($package->getName()); ?></td>
    </tr>
    <tr>
        <td><b><?php esc_html_e('Created', 'gamb-duplica-wp'); ?>:</b>&nbsp; </td>
        <td>
            <?php echo esc_html($package->getCreated()); ?>&nbsp;-&nbsp;<i><?php echo esc_html($timeDiff); ?></i>
        </td>
    </tr>
</table>
