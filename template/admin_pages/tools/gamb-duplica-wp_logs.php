<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Controllers\SettingsPageController;
use gamb-duplica-wp\Core\Controllers\ControllersManager;
use gamb-duplica-wp\Controllers\ToolsPageController;
use gamb-duplica-wp\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */


$trace_log_filepath = GAMB_DUPLICA_WP_Log::getTraceFilepath();
$trace_filename     = basename($trace_log_filepath);
$logs               = ToolsPageController::getLogsList();
$global             = GAMB_DUPLICA_WP_Global_Entity::getInstance();
$logname            = (isset($_GET['logname'])) ? trim($_GET['logname']) : "";
$refresh            = (isset($_POST['refresh']) && $_POST['refresh'] == 1) ? 1 : 0;
$auto               = (isset($_POST['auto']) && $_POST['auto'] == 1) ? 1 : 0;

//Check for invalid file
if (isset($_GET['logname'])) {
    $validFiles = array_map('basename', $logs);
    if (validate_file($logname, $validFiles) > 0) {
        unset($logname);
    }
    unset($validFiles);
}

if (!isset($logname) || !$logname) {
    $logname = (count($logs) > 0) ? basename($logs[0]) : "";
}

$nocache     = @date("ymdHis");
$logurl      = gamb-duplica-wp_PRO_SSDIR_URL . "/{$logname}?{$nocache}";
$logurl_base = gamb-duplica-wp_PRO_SSDIR_URL . "/{$logname}";
$logfound    = strlen($logname) > 0;
?>
<script>
jQuery(document).ready(function ($)
{

    DupPro.Tools.FullLog = function () {
        var $panelL = $('#dpro-log-pnl-left');
        var $panelR = $('#dpro-log-pnl-right');

        if ($panelR.is(":visible")) {
            $panelR.hide(400);
            $panelL.css({width: '100%'});
        } else {
            $panelR.show(200);
            $panelL.css({width: '75%'});
        }
    }

    DupPro.Tools.Refresh = function () {
        $('#refresh').val(1);
        $('#dup-form-logs').submit();
    }

    DupPro.Tools.RefreshAuto = function () {
        if ($("#dup-auto-refresh").is(":checked")) {
            $('#auto').val(1);
            startTimer();
        } else {
            $('#auto').val(0);
        }
    }

    DupPro.Tools.WinResize = function () {
        var height = $(window).height() - 210;
        $("#dpro-log-content").css({height: height + 'px'});
    }

    var duration = 9;
    var count = duration;
    var timerInterval;
    function timer() {
        count = count - 1;
        $("#dup-refresh-count").html(count.toString());
        if (!$("#dup-auto-refresh").is(":checked")) {
            clearInterval(timerInterval);
            $("#dup-refresh-count").text(count.toString().trim());
            return;
        }

        if (count <= 0) {
            count = duration + 1;
            DupPro.Tools.Refresh();
        }
    }

    function startTimer() {
        timerInterval = setInterval(timer, 1000);
    }

    //INIT Events
    $(window).resize(DupPro.Tools.WinResize);
    $('#dup-options').click(DupPro.Tools.FullLog);
    $("#dup-refresh").click(DupPro.Tools.Refresh);
    $("#dup-auto-refresh").click(DupPro.Tools.RefreshAuto);
    $("#dup-refresh-count").html(duration.toString());

    //INIT
    DupPro.Tools.WinResize();

    <?php if ($refresh) : ?>
            //Scroll to Bottom
            $('#dpro-log-content').on('load', function() {
                var $contents = $('#dpro-log-content').contents();
                $contents.scrollTop($contents.height());
            });
        <?php if ($auto) : ?>
            $("#dup-auto-refresh").prop('checked', true);
            DupPro.Tools.RefreshAuto();
        <?php endif; ?>
    <?php endif; ?>
        
    // formatting log 
    $('#dpro-log-content').on('load', function() {
        $('#dpro-log-content').contents().find("head")
          .append($("<style type='text/css'>pre {line-height: 2;white-space: pre;}</style>"));
    });
});
</script>

<form id="dup-form-logs" method="post" action="">
<input type="hidden" id="refresh" name="refresh" value="<?php echo ($refresh) ? 1 : 0 ?>" />
<input type="hidden" id="auto" name="auto" value="<?php echo ($auto) ? 1 : 0 ?>" />

<?php if (!$logfound) : ?>
    <div style="padding:20px">
        <h2>
            <?php
            esc_html_e(
                "Log file not found or unreadable.",
                'gamb-duplica-wp'
            );
            ?>
        </h2>
        <?php
        printf(
            esc_html__(
                "Try to create a Backup, since no log files were found in the %s directory ending in *_log.txt",
                'gamb-duplica-wp'
            ),
            '<b>' . esc_html(gamb-duplica-wp_PRO_SSDIR_NAME) . '</b>'
        );
        ?>.<br/><br/>
        <?php
        esc_html_e(
            "Reasons for log file not showing",
            'gamb-duplica-wp'
        );
        ?>: <br/>
        -
        <?php
        esc_html_e(
            "The web server does not support returning .txt file extensions",
            'gamb-duplica-wp'
        );
        ?>.<br/>
        -
        <?php
        printf(
            esc_html__(
                "The %s directory does not have the correct permissions to write files.  Try setting the permissions to 755",
                'gamb-duplica-wp'
            ),
            '<b>' . esc_html(gamb-duplica-wp_PRO_SSDIR_NAME) . '</b>'
        );
        ?>. <br/>
        -
        <?php
        esc_html_e(
            "The process that PHP runs under does not have enough permissions to create files. 
             Please contact your hosting provider for more details",
            'gamb-duplica-wp'
        );
        ?>. <br/>
    </div>
<?php else : ?>
    <table id="dpro-log-pnls">
        <tr>
            <td id="dpro-log-pnl-left">
                <div class="name"><i class="fas fa-file-contract fa-fw"></i> <?php echo basename($logurl); ?></div>
                <div class="opts">
                    <a href="javascript:void(0)" id="dup-options">
                        <?php esc_html_e("Options", 'gamb-duplica-wp') ?> <i class="fa fa-angle-double-right"></i>
                    </a> &nbsp;
                </div>
                <br style="clear:both" />
                <iframe id="dpro-log-content" src="<?php echo esc_url($logurl); ?>" ></iframe>
            </td>
            <td id="dpro-log-pnl-right">
                <h2><?php esc_html_e("Options", 'gamb-duplica-wp') ?></h2>

                <div class="dpro-opts-items">
                    <input 
                        type="button" 
                        class="button secondary hollow tiny margin-0" 
                        id="dup-refresh" 
                        value="<?php esc_attr_e("Refresh", 'gamb-duplica-wp') ?>" 
                    > &nbsp;
                    <div style="display:inline-block;margin-top:1px;">
                        <input type='checkbox' id="dup-auto-refresh" class="margin-0" />
                        <label id="dup-auto-refresh-lbl" for="dup-auto-refresh">
                        <?php esc_html_e("Auto Refresh", 'gamb-duplica-wp') ?>  [<span id="dup-refresh-count"></span>]
                        </label>
                    </div>
                </div>

                <div class="dpro-log-hdr">
                    <?php esc_html_e('Trace Log:', 'gamb-duplica-wp') ?> &nbsp;
                    <span style="font-size:11px; font-weight: normal">
                        <?php
                        $trace_on        = get_option('gamb-duplica-wp_pro_trace_log_enabled', false);
                        $txt_clear_trace = esc_html__('Clear', 'gamb-duplica-wp');
                        $txt_profile     = '';
                        $html            = "";

                        if (CapMng::can(CapMng::CAP_SETTINGS, false)) {
                            if (!$trace_on) {
                                $url  = SettingsPageController::getInstance()->getTraceActionUrl(true);
                                $html = '<a href="' . esc_url($url) . '" target="_blank">' . __("Turn On", 'gamb-duplica-wp') . $txt_profile . '</a>';
                            } else {
                                $url  = SettingsPageController::getInstance()->getTraceActionUrl(false);
                                $html = '<a href="' . esc_url($url) . '" target="_blank">' . __("Turn Off", 'gamb-duplica-wp') . $txt_profile . '</a>';
                            }
                            $html .= " | ";
                        }
                        if (CapMng::can(CapMng::CAP_CREATE, false)) {
                            $html .= "<a href='javascript:void(0)' onclick='DupPro.UI.ClearTraceLog(1);'>{$txt_clear_trace}</a>";
                        }

                        echo $html;
                        ?>
                    </span>
                </div>
                <div class="dpro-log-file-list">
                    <?php
                    $trace_log_filepath = GAMB_DUPLICA_WP_Log::getTraceFilepath();
                    if (file_exists($trace_log_filepath)) {
                        $time = date('m/d/y h:i:s', @filemtime($trace_log_filepath));
                    } else {
                        $time = __('No trace log found', 'gamb-duplica-wp');
                    }
                    $active_filename = basename($logurl_base);
                    $trace_log_url   = ControllersManager::getMenuLink(
                        ControllersManager::TOOLS_SUBMENU_SLUG,
                        ToolsPageController::L2_SLUG_LOGS,
                        null,
                        ['logname' => $trace_filename]
                    );
                    $is_trace_active = ($active_filename == $trace_filename);

                    echo ($is_trace_active)
                        ? '<div class="dpro-trace-log-link-green"><i class="fa fa-caret-right"></i> ' . $time . '</div>'
                        : '<a href="' . esc_url($trace_log_url) . '">' . $time . '</a>';
                    ?>
                </div>

                <br/>
                <div class="dpro-log-hdr">
                    <?php esc_html_e('Backup Logs', 'gamb-duplica-wp');  ?>
                    <small><?php esc_html_e('Top 20', 'gamb-duplica-wp');  ?></small>
                </div>

                <div class="dpro-log-file-list" style="white-space: nowrap">
                    <?php
                    $count  = 0;
                    $active = basename($logurl_base);
                    foreach ($logs as $log) {
                        $time = date('m/d/y h:i:s', filemtime($log));
                        $name = sanitize_text_field(basename($log));
                        $url  = ControllersManager::getMenuLink(
                            ControllersManager::TOOLS_SUBMENU_SLUG,
                            ToolsPageController::L2_SLUG_LOGS,
                            null,
                            ['logname' => $name]
                        );
                        if ($name !== $trace_filename) {
                            $shortname = substr($name, 0, 15) . '***.log';
                            echo ($active == $name)
                                ? '<span title="' . esc_attr($name) . '"><i class="fa fa-caret-right"></i> ' . $time . '-' . $shortname . '</span><br/>'
                                : '<a href="' . esc_url($url) . '" title="' . esc_attr($name) . '">' . $time . '-' . $shortname . '</a><br/>';
                            if ($count > 20) {
                                break;
                            }
                            $count++;
                        }
                    }
                    ?>
                </div>
            </td>
        </tr>
    </table>
<?php endif; ?>
</form>
