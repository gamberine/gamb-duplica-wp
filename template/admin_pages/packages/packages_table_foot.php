<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Models\Storages\StoragesUtil;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$global             = GAMB_DUPLICA_WP_Global_Entity::getInstance();
$maxDefaultPackages = StoragesUtil::getDefaultStorage()->getMaxPackages();
$toolTipContent     = sprintf(
    esc_attr__(
        'The number of Backups to keep is set at [%d]. To change this setting go to 
        gamb-duplica-wp Pro > Storage > Default > Max Backups and change the value, otherwise this note can be ignored.',
        'gamb-duplica-wp'
    ),
    $maxDefaultPackages
);
?>
<tfoot>
    <tr>
        <th colspan="11">
            <div class="dup-pack-status-info">
                <?php if ($maxDefaultPackages < $tplData['totalElements'] && $maxDefaultPackages != 0) { ?>
                    <?php echo esc_html__("Note: max backups retention enabled", 'gamb-duplica-wp'); ?>
                    <i 
                        class="fa-solid fa-question-circle fa-sm dark-gray-color" 
                        data-tooltip-title="<?php esc_attr_e("Storage Backups", 'gamb-duplica-wp'); ?>" 
                        data-tooltip="<?php echo esc_attr($toolTipContent); ?>"
                    >
                    </i>
                <?php } else { ?>
                    &nbsp;
                <?php } ?>
            </div>
        </th>
    </tr>
</tfoot>
