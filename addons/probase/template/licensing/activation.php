<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Addons\ProBase\License\License;
use GambDuplicaWP\Addons\ProBase\Models\LicenseData;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$global = GAMB_DUPLICA_WP_Global_Entity::getInstance();

?>
<h3 class="title">
    <?php esc_html_e('License', 'gamb-duplica-wp') ?>
</h3>
<hr size="1" >
<p>
<?php esc_html_e('Your license key provides access to updates and addons.', 'gamb-duplica-wp') ?>
</p>

<label class="lbl-larger" >
    <?php esc_html_e("License Key", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <?php $tplMng->render('licensing/license_key_actions'); ?>
    <?php if ($global->license_key_visible !== License::VISIBILITY_NONE) { ?>
    <div id="dup-tr-license-type" >
        <?php $tplMng->render('licensing/license_info'); ?>
        <?php $tplMng->render('licensing/activation_issue_message'); ?>
    </div>
    <?php } ?>
</div>

