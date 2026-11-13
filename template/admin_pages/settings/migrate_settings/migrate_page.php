<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

use GambDuplicaWP\Addons\ProBase\License\License;

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

/* FOR PERSONAL LICENSE JUST SHOW MESSAGE */
if (!License::can(License::CAPABILITY_IMPORT_SETTINGS)) {
    $tplMng->render('admin_pages/settings/migrate_settings/no_capatibily');
    return;
}
?>

<?php $tplMng->render('admin_pages/settings/migrate_settings/export'); ?>
<hr size="1" />
<?php $tplMng->render('admin_pages/settings/migrate_settings/import'); ?>

<?php add_thickbox();

