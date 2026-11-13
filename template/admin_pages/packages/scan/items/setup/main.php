<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

use GambDuplicaWP\Addons\DropboxAddon\Models\DropboxStorage;
use GambDuplicaWP\Core\Views\TplMng;

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var GAMB_DUPLICA_WP_Package $package
 */
$package = $tplData['package'];
$global  = GAMB_DUPLICA_WP_Global_Entity::getInstance();

global $wp_version;
?>
<!-- ================================================================
SETUP
================================================================ -->
<div class="details-title">
    <i class="fas fa-tasks fa-sm fa-fw"></i> <?php esc_html_e("Setup", 'gamb-duplica-wp'); ?>
    <div class="dup-more-details">
        <a href="site-health.php" target="_blank" title="<?php esc_attr_e('Site Health', 'gamb-duplica-wp'); ?>">
            <i class="fas fa-file-medical-alt"></i>
        </a>
    </div>
</div>

<!-- ======================
SYSTEM SETTINGS -->
<?php TplMng::getInstance()->render(
    'admin_pages/packages/scan/items/setup/system',
    ['hasDropbox' => $package->contains_storage_type(DropboxStorage::getSType())]
); ?>
<!-- ======================
WP SETTINGS -->
<?php TplMng::getInstance()->render(
    'admin_pages/packages/scan/items/setup/wordpress',
    ['wpVersion' => $wp_version]
); ?>

<!-- ======================
Restore only Backup -->
<?php TplMng::getInstance()->render('admin_pages/packages/scan/items/setup/restore'); ?>
