<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Addons\ProBase\License\License;
use GambDuplicaWP\Libs\Snap\SnapIO;
use GambDuplicaWP\Libs\Snap\SnapUtil;
use GambDuplicaWP\Models\DynamicGlobalEntity;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$hasDropbox       = $tplData['hasDropbox'];
$webServers       = implode(', ', $GLOBALS['gamb-duplica-wp_PRO_SERVER_LIST']);
$serverSoftware   = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'SERVER_SOFTWARE', 'unknown');
$transferMode     = DynamicGlobalEntity::getInstance()->getVal('dropbox_transfer_mode');
$fopenEnabled     = GAMB_DUPLICA_WP_Server::isURLFopenEnabled() ? '1' : '0';
$isCurlEnabled    = SnapUtil::isCurlEnabled() ? __('True', 'gamb-duplica-wp') : __('False', 'gamb-duplica-wp');
$openBaseDir      = SnapIO::isOpenBaseDirEnabled() ? esc_html__('on', 'gamb-duplica-wp') : esc_html__('off', 'gamb-duplica-wp') ;
$maxExecutionTime = set_time_limit(0) === true ? 0 : @ini_get('max_execution_time');
$memoryLimit      = @ini_get('memory_limit');
$architecture     = SnapUtil::getArchitectureString();
?>

<div class="scan-item scan-item-first">
    <div class='title' onclick="DupPro.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('System', 'gamb-duplica-wp'); ?></div>
        <div id="data-srv-php-all"></div>
    </div>
    <div class="info">
        <div class="scan-system-divider"><i class="fa fa-list"></i>&nbsp; <?php esc_html_e('General Checks', 'gamb-duplica-wp'); ?></div>
        <?php if (License::can(License::CAPABILITY_BRAND)) : ?>
            <span id="data-srv-brand-check"></span>&nbsp;
                <b><?php esc_html_e('Brand', 'gamb-duplica-wp'); ?>: </b>
                <span id="data-srv-brand-name"><?php esc_html_e('Default', 'gamb-duplica-wp'); ?></span>
            <br />
            <div class="scan-system-subnote" id="data-srv-brand-note">
                <?php esc_html_e('The default content used when a brand is not defined.', 'gamb-duplica-wp'); ?>
            </div>
            <hr size="1" />
        <?php endif; ?>
        <span id="data-srv-php-websrv"></span>
            &nbsp;<b><?php esc_html_e('Web Server', 'gamb-duplica-wp') ?>:</b>
            &nbsp; <?php echo esc_html($serverSoftware); ?><br/>
        <div class="scan-system-subnote">
            <?php esc_html_e("Supported Web Servers:", 'gamb-duplica-wp'); ?>&nbsp;<?php echo esc_html($webServers); ?>
        </div>
        <hr size="1" />
        <span id="data-srv-php-mysqli"></span>&nbsp;<b><?php esc_html_e('MySQLi', 'gamb-duplica-wp'); ?></b><br/>
        <div class="scan-system-subnote">
            <?php esc_html_e(
                'Creating the Backup does not require the mysqli module. However the installer file requires
                that the PHP module mysqli be installed on the server it is deployed on.',
                'gamb-duplica-wp'
            ); ?>
            <i><a href="http://php.net/manual/en/mysqli.installation.php" target="_blank">[<?php esc_html_e('details', 'gamb-duplica-wp'); ?>]</a></i>
        </div>
        <?php if ($hasDropbox) { ?>
            <hr size="1" />
            <span id="data-srv-php-openssl"></span>&nbsp;<b><?php esc_html_e('Open SSL - Dropbox', 'gamb-duplica-wp'); ?></b>
            <div class="scan-system-subnote">
                <?php esc_html_e(
                    'Dropbox storage requires an HTTPS connection. On windows systems enable "extension=php_openssl.dll" in the php.ini configuration file.  ',
                    'gamb-duplica-wp'
                ); ?>
                <?php esc_html_e('On Linux based systems check for the --with-openssl[=DIR] flag.', 'gamb-duplica-wp'); ?>
                <i><a href="http://php.net/manual/en/openssl.installation.php" target="_blank">[<?php esc_html_e('details', 'gamb-duplica-wp'); ?>]</a></i>
            </div>

            <?php if ($transferMode == GAMB_DUPLICA_WP_Dropbox_Transfer_Mode::FOpen_URL) { ?>
                <hr size="1" />
                <span id="data-srv-php-allowurlfopen"></span>
                <b><?php esc_html_e('Allow URL Fopen', 'gamb-duplica-wp'); ?>:</b>&nbsp; <?php echo esc_html($fopenEnabled); ?><br/>
                <div class="scan-system-subnote">
                    <?php esc_html_e(
                        'Dropbox communications requires that [allow_url_fopen] be set to 1 in the php.ini file.',
                        'gamb-duplica-wp'
                    ); ?>
                    <i>
                        <a href="http://php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen" target="_blank">
                            [<?php esc_html_e('details', 'gamb-duplica-wp'); ?>]
                        </a>
                    </i>
                    <br/>
                </div>
            <?php } elseif ($transferMode == GAMB_DUPLICA_WP_Dropbox_Transfer_Mode::cURL) { ?>
                <hr size="1" />
                <span id="data-srv-php-curlavailable"></span>
                <b><?php esc_html_e('cURL - Dropbox', 'gamb-duplica-wp'); ?>:</b>&nbsp; <?php echo esc_html($isCurlEnabled); ?>
                <br/>
                <div class="scan-system-subnote">
                    <?php esc_html_e('Dropbox communications requires that extension=php_curl.dll be present in the php.ini file.', 'gamb-duplica-wp'); ?>
                    <i><a href="http://php.net/manual/en/curl.installation.php" target="_blank">[<?php esc_html_e('details', 'gamb-duplica-wp'); ?>]</a></i><br/>
                </div>
            <?php } ?>
        <?php } ?>

        <div class="scan-system-divider margin-top-1"><i class="fa fa-list"></i>&nbsp;<?php esc_html_e('PHP Checks', 'gamb-duplica-wp'); ?></div>
        <span id="data-srv-php-version"></span>&nbsp;<b><?php esc_html_e('PHP Version: ', 'gamb-duplica-wp'); ?> </b> <?php echo PHP_VERSION; ?> <br/>
        <div class="scan-system-subnote">
            <?php
            echo esc_html(
                sprintf(
                    __(
                        'The minimum PHP version supported by gamb-duplica-wp is %1$s, however it is highly 
                        recommended to use PHP %2$s or higher for improved stability.',
                        'gamb-duplica-wp'
                    ),
                    GambDuplicaWP_PRO_PHP_MINIMUM_VERSION,
                    GambDuplicaWP_PRO_PHP_SUGGESTED_VERSION
                )
            );?>
        </div>
        <hr size="1" />
        <span id="data-srv-php-openbase"></span>&nbsp;
        <b><?php esc_html_e('PHP Open Base Dir', 'gamb-duplica-wp'); ?>:</b>&nbsp;<?php echo esc_html($openBaseDir); ?>
        <br/>
        <div class="scan-system-subnote">
            <?php esc_html_e(
                'Issues might occur when [open_basedir] is enabled. Work with your server admin or hosting provider to disable 
                this value in the php.ini file if you’re having issues building a Backup.',
                'gamb-duplica-wp'
            ); ?>
            &nbsp;
            <i>
                <a href="http://php.net/manual/en/ini.core.php#ini.open-basedir" target="_blank">[<?php esc_html_e('details', 'gamb-duplica-wp'); ?>]</a>
            </i>
            <br/>
        </div>

        <hr size="1" />
        <span id="data-srv-php-maxtime"></span>&nbsp;
        <b><?php esc_html_e('PHP Max Execution Time', 'gamb-duplica-wp'); ?>:</b>&nbsp; <?php echo esc_html($maxExecutionTime); ?>
        <br/>
        <div class="scan-system-subnote">
            <?php
            esc_html(
                sprintf(
                    __(
                        'Issues might occur for larger Backups when the [max_execution_time] value in the php.ini is too low. 
                        The minimum recommended timeout is "%1$s" seconds or higher. 
                        An attempt is made to override this value if the server allows it. A value of 0 (recommended) indicates that PHP has no time limits.',
                        'gamb-duplica-wp'
                    ),
                    GambDuplicaWP_PRO_SCAN_TIMEOUT
                )
            ); ?>
            &nbsp;
            <i>
                <a href="http://www.php.net/manual/en/info.configuration.php#ini.max-execution-time" target="_blank">
                    [<?php esc_html_e('details', 'gamb-duplica-wp'); ?>]
                </a>
            </i>
        </div>

        <hr size="1" />
        <span id="data-srv-php-minmemory"></span>&nbsp;
        <b><?php esc_html_e('PHP Memory Limit', 'gamb-duplica-wp'); ?>:</b>&nbsp; <?php echo esc_html($memoryLimit); ?>
        <br/>
        <div class="scan-system-subnote">
        <?php
        echo wp_kses(
            sprintf(
                _x(
                    'Issues might occur for larger Backups when the [memory_limit] value in the php.ini is too low.  
                    The minimum recommended memory limit is "%1$s" or higher. An attempt is made to override this value if the server allows it. 
                    To manually increase the memory limit have a look at this %2$s[FAQ item]%3$s',
                    '1: memory limit, 2: link start, 3: link end',
                    'gamb-duplica-wp'
                ),
                GambDuplicaWP_PRO_MIN_MEMORY_LIMIT,
                "<i><a href='" . GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . "how-to-manage-server-resources-cpu-memory-disk' target='_blank'>",
                "</a></i>"
            ),
            [
                'a' => [
                    'href'   => [],
                    'target' => [],
                ],
                'i' => [],
            ]
        ); ?>
        </div>

        <hr size="1" />
        <span id="data-srv-php-arch64bit"></span>&nbsp;
        <b><?php esc_html_e('PHP 64 Bit Architecture', 'gamb-duplica-wp'); ?>:</b>&nbsp; <?php echo esc_html($architecture); ?><br/>
        <div class="scan-system-subnote">
        <?php
        echo wp_kses(
            sprintf(
                _x(
                    'Servers that run a PHP 32-bit architecture are not capable of creating Backups larger than 2GB.   
                    If you need to create a Backup that is larger than 2GB in size talk with your host or server admin 
                    to change your version of PHP to 64-bit. %1$s[FAQ item]%2$s',
                    '1: link start, 2: link end',
                    'gamb-duplica-wp'
                ),
                "<i><a href='" . GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . "how-to-resolve-file-io-related-build-issues' target='_blank'>",
                "</a></i>"
            ),
            [
                'a' => [
                    'href'   => [],
                    'target' => [],
                ],
                'i' => [],
            ]
        ); ?>
        </div>
        <br/>
    </div>
</div>
