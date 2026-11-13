<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$info = $tplData['info'];
?>
<p>
    <?php echo esc_html($info['desc']); ?>
</p>
<?php if (strlen($info['parent']) > 0) { ?>
    <br>
    <?php esc_html_e('Parent', 'gamb-duplica-wp'); ?>: <b><?php echo esc_html($tplData['pLabel']); ?></b>
<?php } ?>