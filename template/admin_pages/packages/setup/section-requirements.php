<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

use GambDuplicaWP\Controllers\ToolsPageController;
use GambDuplicaWP\Core\MigrationMng;
use GambDuplicaWP\Libs\Snap\FunctionalityCheck;

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string,mixed> $tplData
 * @var array<string,mixed> $requirements
 */
$requirements = $tplData['requirements'];

if ($requirements['Success']) {
    return;
}
?>
<div class="dup-box dup-requirements-wrapper">
    <div class="dup-box-title">
        <i class="far fa-check-circle"></i>
        <?php esc_html_e("Requirements:", 'gamb-duplica-wp'); ?> <div class="dup-sys-fail">Fail</div>
        <button class="dup-box-arrow">
            <span class="screen-reader-text">
                <?php esc_html_e('Toggle panel:', 'gamb-duplica-wp') ?> <?php esc_html_e('Requirements:', 'gamb-duplica-wp') ?>
            </span>
        </button>
    </div>
    <div class="dup-box-panel">
        <div class="dup-sys-section">
            <i><?php esc_html_e("System requirements must pass for the gamb-duplica-wp to work properly.  Click each link for details.", 'gamb-duplica-wp'); ?></i>
        </div>

        <!-- PHP SUPPORT -->
        <div class='dup-sys-req'>
            <div class='dup-sys-title'>
                <a><?php esc_html_e('PHP Support', 'gamb-duplica-wp'); ?></a>
                <div><?php echo esc_html($requirements['PHP']['ALL']); ?></div>
            </div>
            <div class="dup-sys-info dup-info-box">
                <table class="dup-sys-info-results">
                    <tr>
                        <td>
                            <?php esc_html_e('PHP Version', 'gamb-duplica-wp'); ?> [<?php echo esc_html(phpversion()); ?>]
                        </td>
                        <td><?php echo esc_html($requirements['PHP']['VERSION']); ?></td>
                    </tr>  
                    <?php foreach (GAMB_DUPLICA_WP_Server::getFunctionalitiesCheckList() as $func) { ?>
                    <tr>
                        <td>
                            <?php
                            switch ($func->getType()) {
                                case FunctionalityCheck::TYPE_FUNCTION:
                                    esc_html_e('Function', 'gamb-duplica-wp');
                                    break;
                                case FunctionalityCheck::TYPE_CLASS:
                                    esc_html_e('Class', 'gamb-duplica-wp');
                                    break;
                                default:
                                    throw new Exception('Invalid item type');
                            }
                            ?>
                            <a href="<?php echo esc_url($func->link); ?>" target="_blank">
                                <?php echo esc_html($func->getItemKey()); ?>
                            </a>
                        </td>
                        <td>
                        <?php
                        if ($func->check()) {
                            echo esc_html_e('Pass', 'gamb-duplica-wp');
                        } elseif ($func->isRequired()) {
                            echo esc_html_e('Fail', 'gamb-duplica-wp');
                        } else {
                            echo esc_html_e('Warning', 'gamb-duplica-wp');
                        }
                        if (strlen($func->troubleshoot) > 0) {
                            echo ' &nbsp; ';
                            echo wp_kses(
                                $func->troubleshoot,
                                [
                                    'a' => [
                                        'href'   => [],
                                        'target' => [],
                                    ],
                                    'i' => [
                                        'class' => [],
                                        'style' => [],
                                    ],
                                ]
                            );
                        }
                        ?>
                        </td>
                    </tr>
                    <?php } ?>                          
                </table>
                <small>
                    <?php
                    printf(
                        esc_html__(
                            "PHP versions %s+ including the listed functions are required for the plugin to create a Backup. 
                            For additional information see our online technical FAQs.",
                            'gamb-duplica-wp'
                        ),
                        esc_html(GambDuplicaWP_PRO_PHP_MINIMUM_VERSION)
                    );
                    ?>
                </small>
            </div>
        </div>      

        <!-- PERMISSIONS -->
        <div class='dup-sys-req'>
            <div class='dup-sys-title'>
                <a><?php esc_html_e('Permissions', 'gamb-duplica-wp'); ?></a> <div><?php echo esc_html($requirements['IO']['ALL']); ?></div>
            </div>
            <div class="dup-sys-info dup-info-box">
                <b><?php esc_html_e("Required Paths", 'gamb-duplica-wp'); ?></b>
                <div style="padding:3px 0px 0px 15px">
                    <?php
                    printf("<b>%s</b> &nbsp; [%s] <br/>", esc_html($requirements['IO']['WPROOT']), esc_html(GAMB_DUPLICA_WP_Archive::getArchiveListPaths('home')));
                    printf("<b>%s</b> &nbsp; [%s] <br/>", esc_html($requirements['IO']['SSDIR']), esc_html(GambDuplicaWP_PRO_SSDIR_PATH));
                    printf("<b>%s</b> &nbsp; [%s] <br/>", esc_html($requirements['IO']['SSTMP']), esc_html(GambDuplicaWP_PRO_SSDIR_PATH_TMP));
                    ?>
                </div>

                <small>
                <?php
                esc_html_e(
                    "Permissions can be difficult to resolve on some systems. If the plugin can not read the above paths here 
                    are a few things to try. 1) Set the above paths to have permissions of 755 for directories and 644 for files. 
                    You can temporarily try 777 however, be sure you don’t leave them this way. 
                    2) Check the owner/group settings for both files and directories. 
                    The PHP script owner and the process owner are different. The script owner owns the PHP script but the process owner 
                    is the user the script is running as, thus determining its capabilities/privileges in the file system. 
                    For more details contact your host or server administrator or visit the 'Help' menu under gamb-duplica-wp for additional online resources.",
                    'gamb-duplica-wp'
                );
                ?>
                </small>                    
            </div>
        </div>

        <!-- SERVER SUPPORT -->
        <div class='dup-sys-req'>
            <div class='dup-sys-title'>
                <a><?php esc_html_e('Server Support', 'gamb-duplica-wp'); ?></a>
                <div><?php echo esc_html($requirements['SRV']['ALL']); ?></div>
            </div>
            <div class="dup-sys-info dup-info-box">
                <table class="dup-sys-info-results">
                    <tr>
                        <td><?php printf("%s [%s]", esc_html__("MySQL Version", 'gamb-duplica-wp'), esc_html(GAMB_DUPLICA_WP_DB::getVersion())); ?></td>
                        <td><?php echo esc_html($requirements['SRV']['MYSQL_VER']); ?></td>
                    </tr>
                </table>
                <small>
                    <?php esc_html_e(
                        "MySQL version 5.0+ or better is required.  Contact your server administrator and request MySQL Server 5.0+ be installed.",
                        'gamb-duplica-wp'
                    ); ?>
                </small>
                <hr>
                <table class="dup-sys-info-results">
                    <tr>
                        <td><a href="https://www.php.net/manual/en/mysqli.real-escape-string.php" target="_blank">mysqli_real_escape_string</a></td>
                        <td><?php echo esc_html($requirements['SRV']['MYSQL_ESC']); ?></td>
                    </tr>
                </table>
                <small>
                    <?php esc_html_e(
                        "The function mysqli_real_escape_string is not working properly. 
                        Please consult host support and ask them to switch to a different PHP version or configuration.",
                        'gamb-duplica-wp'
                    ); ?>
                </small>
            </div>
        </div>

        <!-- INSTALLATION FILES -->
        <div class='dup-sys-req'>
            <div class='dup-sys-title'>
                <a><?php esc_html_e('Installation Files', 'gamb-duplica-wp'); ?></a> <div><?php echo esc_html($requirements['RES']['INSTALL']); ?></div>
            </div>
            <div class="dup-sys-info dup-info-box">
                <?php
                if ($requirements['RES']['INSTALL'] == 'Pass') :
                    esc_html_e("No reserved installation files were found from a previous install. You are clear to create a new Backup.", 'gamb-duplica-wp');
                else :
                    ?>                     
                    <form method="post" action="<?php echo esc_url(ToolsPageController::getInstance()->getCleanFilesAcrtionUrl()); ?>">
                        <?php
                        esc_html_e(
                            "An installer file(s) was found in the WordPress root directory.
                            To archive your data correctly please remove any of these files and try creating your Backup again.",
                            'gamb-duplica-wp'
                        );
                        ?><br/>
                        <b><?php esc_html_e('Installer file names include', 'gamb-duplica-wp'); ?></b>
                        <ul>
                            <?php foreach (MigrationMng::checkInstallerFilesList() as $filePath) { ?>
                                <li>
                                    <?php echo esc_html($filePath); ?>
                                </li>
                            <?php } ?>
                        </ul>
                        <input 
                            type='submit' 
                            class='button action' 
                            value='<?php esc_attr_e('Remove Files Now', 'gamb-duplica-wp') ?>' 
                            style='font-size:10px; margin-top:5px;' 
                        >
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- ONLINE SUPPORT -->
        <div class="dup-sys-contact">
            <?php
            printf(
                "<i class='fa fa-question-circle'></i> %s <a href='" . esc_attr(GambDuplicaWP_PRO_TECH_FAQ_URL) . "' target='_blank'>[%s]</a>",
                esc_html__("For additional help please see the ", 'gamb-duplica-wp'),
                esc_html__("online FAQs", 'gamb-duplica-wp')
            );
            ?>
        </div>

    </div>
</div>
<script>
//INIT
    jQuery(document).ready(function ($)
    {
        DupPro.Pack.ToggleSystemDetails = function (anchor)
        {
            $(anchor).parent().siblings('.dup-sys-info').toggle();
        }

        //Init: Toogle for system requirment detial links
        $('.dup-sys-title a').each(function () {
            $(this).attr('href', 'javascript:void(0)');
            $(this).click(function () {
                DupPro.Pack.ToggleSystemDetails(this);
            });
            $(this).prepend("<span class='ui-icon ui-icon-triangle-1-e dup-toggle' />");
        });

        //Init: Color code Pass/Fail/Warn items
        $('.dup-sys-title div').each(function () {
            $(this).addClass(($(this).text() == 'Pass') ? 'dup-sys-pass' : 'dup-sys-fail');
        });

    });
</script>
