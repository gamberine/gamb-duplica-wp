<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Core\Controllers\ControllersManager;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */
?>

<form 
    id="form-gamb-duplica-wp scan-result" 
    method="post"
    action="<?php echo esc_attr(ControllersManager::getMenuLink(ControllersManager::PACKAGES_SUBMENU_SLUG)); ?>"
>
    <!--  ERROR MESSAGE -->
    <div id="dup-msg-error">
        <div class="dup-hdr-error"><i class="fa fa-exclamation-circle"></i> <?php esc_html_e('Input fields not valid', 'gamb-duplica-wp'); ?></div>
        <i><?php esc_html_e('Please try again!', 'gamb-duplica-wp'); ?></i><br/>
        <div style="text-align:left">
            <b><?php esc_html_e("Server Status:", 'gamb-duplica-wp'); ?></b> &nbsp;
            <div id="dup-msg-error-response-status" style="display:inline-block"></div><br/>
            <b><?php esc_html_e("Error Message:", 'gamb-duplica-wp'); ?></b>
            <div id="dup-msg-error-response-text">
                <ul>
                    <?php $tplData['validator']->getErrorsFormat("<li>%s</li>"); ?>
                </ul>
            </div>
        </div>
    </div>
    <input
        type="button"
        value="&#9664; <?php esc_html_e("Back", 'gamb-duplica-wp') ?>"
        class="button hollow secondary dup-go-back-to-new1"
    >
</form>
