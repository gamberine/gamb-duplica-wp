<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

use GambDuplicaWP\Core\Views\TplMng;
use GambDuplicaWP\Models\Storages\AbstractStorageEntity;

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var GAMB_DUPLICA_WP_Schedule_Entity $schedule
 */


?>
<tr>
    <td colspan="3">
        <div class="maroon">
            <?php esc_html_e('Error reading storages', 'gamb-duplica-wp'); ?>
        </div>
    </td>
</tr>