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

<p>
<?php esc_html_e("This option determines the logic of backup record handling (in the \"Backups\" screen)
in case the archive file is deleted from a storage due to the \"Max Backups\" limit being reached.", 'gamb-duplica-wp'); ?>
</p>
<ul>
    <li>
        <?php esc_html_e("The first option will delete the record after the backup archive is removed from ALL storages.", 'gamb-duplica-wp'); ?>
    </li>
    <li>
        <?php esc_html_e(
            "The second option will delete the record when the backup archive is removed from the Default Local Storage 
            even if it still exists in other storages.",
            'gamb-duplica-wp'
        ); ?>
    </li>
    <li>
        <?php esc_html_e("The third option will never delete the record.", 'gamb-duplica-wp'); ?>
    </li>
</ul>
