<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

?>
<tr class="dpro-nopackages">
    <td colspan="11" class="dup-list-nopackages">
        <br />
        <i class="fa fa-archive fa-sm"></i>
        <?php esc_html_e("No Backups Found", 'gamb-duplica-wp'); ?><br />
        <i><?php esc_html_e("Click 'Add New' to Backup Site", 'gamb-duplica-wp'); ?></i>
        <div class="dup-quick-start">
            <b><?php esc_html_e("New to gamb-duplica-wp?", 'gamb-duplica-wp'); ?></b><br />
            <a
                class="dup-quick-start-link"
                href="<?php echo esc_url(GambDuplicaWP_PRO_BLOG_URL . 'knowledge-base-article-categories/quick-start/'); ?>"
                target="_blank">
                <?php esc_html_e("Visit the 'Quick Start' guide!", 'gamb-duplica-wp'); ?>
            </a>
        </div>
        <div style="height:75px">&nbsp;</div>
    </td>
</tr>
