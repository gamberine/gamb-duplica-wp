<?php

/**
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
<tr class="dup-pro-import-no-package-found">
    <td colspan="4" >
        <div class="dup-pro-import-no-package-found-msg">
           <b><?php esc_html_e("No archive files found!", 'gamb-duplica-wp'); ?></b><br/><br/>
           <?php esc_html_e("Please upload a gamb-duplica-wp archive.zip/daf in the area above.", 'gamb-duplica-wp'); ?><br/>
           <?php esc_html_e("This will start the import process to overwrite the current site.", 'gamb-duplica-wp'); ?>
           <br/><br/>
               <a href="<?php echo esc_url(GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . 'import-install'); ?>" target="_blank">
               <?php esc_html_e('How does this work?', 'gamb-duplica-wp'); ?>
           </a>
        </div>
    </td>
</tr>
