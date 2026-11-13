<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Core\MigrationMng;

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
    <?php
    esc_html_e(
        "Clicking on the 'Remove Installation Files' button will remove the following installation files. 
        These files are typically from a previous gamb-duplica-wp install. 
        If you are unsure of the source, please validate the files.  
        These files should never be left on production systems for security reasons.  
        Below is a list of all the installation files used by gamb-duplica-wp.  
        Please be sure these are removed from your server.",
        'gamb-duplica-wp'
    );
    ?>
<p>
<ul>
    <?php
    foreach (MigrationMng::getGenericInstallerFiles() as $instFileName) {
        ?>
        <li>
            <?php echo esc_html($instFileName); ?>
        </li>
        <?php
    }
    ?>
</ul>