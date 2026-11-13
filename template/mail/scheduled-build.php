<?php

/**
 * gamb-duplica-wp schedule success mail
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
 */
?>
<p><?php echo esc_html($tplData['messageTitle']) ?></p>
<p>
    <strong><?php esc_html_e('Backup Name', 'gamb-duplica-wp') ?>: </strong><?php echo esc_html($tplData['packageName']); ?><br/>
    <strong><?php esc_html_e('Backup ID', 'gamb-duplica-wp') ?>: </strong><?php echo esc_html($tplData['packageID']); ?><br/>
    <strong><?php esc_html_e('Date', 'gamb-duplica-wp') ?>: </strong><?php echo esc_html(date_i18n('Y-m-d H:i:s')); ?><br/>
    <strong><?php esc_html_e('Schedule', 'gamb-duplica-wp') ?>: </strong><?php echo esc_html($tplData['scheduleName']); ?>
</p>

<?php if ($tplData['success']) : ?>
<p>
    <strong><?php esc_html_e('Number of Files', 'gamb-duplica-wp') ?>: </strong><?php echo esc_html($tplData['fileCount']); ?><br/>
    <strong><?php esc_html_e('Backup size', 'gamb-duplica-wp') ?>: </strong><?php echo esc_html($tplData['packageSize']); ?>
</p>
<p>
    <strong><?php esc_html_e('Number of tables', 'gamb-duplica-wp') ?>: </strong><?php echo esc_html($tplData['tableCount']); ?><br/>
    <strong><?php esc_html_e('DB dump size', 'gamb-duplica-wp') ?>: </strong><?php echo esc_html($tplData['sqlSize']); ?>
</p>
<?php endif; ?>

<p>
    <strong><?php esc_html_e('Storages', 'gamb-duplica-wp') ?>: </strong>
    <?php foreach ($tplData['storageNames'] as $storageName) : ?>
        <br/> - <?php echo esc_html($storageName); ?>
    <?php endforeach; ?>
</p>
<p>
    <?php
        printf(
            esc_html_x(
                'To go to the "Backups" screen %1$sclick here%2$s.',
                '%1$s and %2$s represent the opening and closing anchor tags (<a> and </a>)',
                'gamb-duplica-wp'
            ),
            '<a href="' . esc_url($tplData['packagesLink']) . '" target="_blank">',
            '</a>'
        );
        ?>
</p>
<?php if ($tplData['logExists']) : ?>
<p>
    <?php esc_html_e('Log is attached.', 'gamb-duplica-wp'); ?>
</p>
<?php endif; ?>
