<?php

/**
 * gamb-duplica-wp messages sections
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Models\Storages\UnknownStorage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var UnknownStorage $storage
 */
$storage = $tplData["storage"];

$tplMng->render('admin_pages/storages/parts/provider_head');
?>
<tr valign="top">
    <th scope="row">
        <label>
            <?php esc_html_e("Unknown Storage Type", 'gamb-duplica-wp'); ?>
        </label>
    </th>
    <td>
    </td>
</tr>
<?php $tplMng->render('admin_pages/storages/parts/provider_foot');

