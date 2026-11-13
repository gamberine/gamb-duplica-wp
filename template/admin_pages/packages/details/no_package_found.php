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
<br/><br/>
<div id='dpro-error' class="error">
    <p>
        <?php echo sprintf(
            esc_html__(
                "Unable to find Backup id %d.  The Backup does not exist or was deleted.",
                'gamb-duplica-wp'
            ),
            esc_html($tplData['packageId'])
        ); ?>
        <br/>
    </p>
</div>