<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

use GambDuplicaWP\Addons\ProBase\License\License;
use GambDuplicaWP\Controllers\SettingsPageController;
use GambDuplicaWP\Core\Controllers\ControllersManager;
use GambDuplicaWP\Models\BrandEntity;

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var int $activeBrandId
 */
$activeBrandId = ($tplData['activeBrandId'] ?? -1);
/** @var string */
$dbHost = ($tplData['dbHost'] ?? '');
/** @var string */
$dbName = ($tplData['dbName'] ?? '');
/** @var string */
$dbUser = ($tplData['dbUser'] ?? '');
/** @var bool */
$cpnlEnable = ($tplData['cpnlEnable'] ?? false);
/** @var string */
$cpnlHost = ($tplData['cpnlHost'] ?? '');
/** @var string */
$cpnlUser = ($tplData['cpnlUser'] ?? '');
/** @var string */
$cpnlDbAction = ($tplData['cpnlDbAction'] ?? '');
/** @var string */
$cpnlDbHost = ($tplData['cpnlDbHost'] ?? '');
/** @var string */
$cpnlDbName = ($tplData['cpnlDbName'] ?? '');
/** @var string */
$cpnlDbUser = ($tplData['cpnlDbUser'] ?? '');

$ui_css_installer = (GAMB_DUPLICA_WP_UI_ViewState::getValue('dpro-pack-installer-panel') ? 'display:block' : 'display:none');

?>
<div class="dup-box">
    <div class="dup-box-title">
        <i class="fa fa-bolt fa-sm"></i> <?php esc_html_e('Installer', 'gamb-duplica-wp') ?>
        <button class="dup-box-arrow">
            <span class="screen-reader-text"><?php esc_html_e('Toggle panel:', 'gamb-duplica-wp') ?> 
                <?php esc_html_e('Installer Settings', 'gamb-duplica-wp') ?>
            </span>
        </button>
    </div>      
    <div class="dup-box-panel" id="dpro-pack-installer-panel" style="<?php echo esc_attr($ui_css_installer); ?>"> 
        <div class="dup-package-hdr-1">
            <?php esc_html_e("Setup", 'gamb-duplica-wp') ?>
        </div>
        <label class="lbl-larger" >
            <?php esc_html_e('Branding', 'gamb-duplica-wp'); ?>
            <?php
                $tipContent = __(
                    'This option changes the branding of the installer file. Click the preview button to see the selected style.',
                    'gamb-duplica-wp'
                );
                ?>
            <i 
                class="fa-solid fa-question-circle fa-sm dark-gray-color"
                data-tooltip-title="<?php esc_attr_e("Choose Brand", 'gamb-duplica-wp'); ?>"
                data-tooltip="<?php echo esc_attr($tipContent); ?>"
            ></i>
        </label>   
        <div>
        <?php
        if (License::can(License::CAPABILITY_BRAND)) :
            $brands = BrandEntity::getAllWithDefault();
            /** @todo remove this */
            /*
            $activeBrandId = GAMB_DUPLICA_WP_Package_Template_Entity::get_manual_template()->installer_opts_brand;
            if ($activeBrandId < 0) {
                $activeBrandId = -1; // for old brand version
            }
                */
            ?>
                <select name="installer_opts_brand" id="brand" class="width-auto" >
                <?php foreach ($brands as $i => $brand) { ?>
                        <option 
                            value="<?php echo (int) $brand->getId(); ?>" 
                            title="<?php echo esc_attr($brand->notes); ?>" 
                            <?php selected($brand->getId(), $activeBrandId); ?>
                        >
                            <?php echo esc_html($brand->name); ?>
                        </option>
                <?php } ?>
                </select>
                <?php
                if ($activeBrandId > 0) {
                    $preview_url = ControllersManager::getMenuLink(
                        ControllersManager::SETTINGS_SUBMENU_SLUG,
                        SettingsPageController::L2_SLUG_PACKAGE_BRAND,
                        null,
                        [
                            ControllersManager::QUERY_STRING_INNER_PAGE => SettingsPageController::BRAND_INNER_PAGE_EDIT,
                            'action'                                    => 'edit',
                            'id'                                        => intval($activeBrandId),
                        ]
                    );
                } else {
                    $preview_url = ControllersManager::getMenuLink(
                        ControllersManager::SETTINGS_SUBMENU_SLUG,
                        SettingsPageController::L2_SLUG_PACKAGE_BRAND,
                        null,
                        [
                            ControllersManager::QUERY_STRING_INNER_PAGE => SettingsPageController::BRAND_INNER_PAGE_EDIT,
                            'action'                                    => 'default',
                        ]
                    );
                }
                ?>
                &nbsp;
                <a href="<?php echo esc_url($preview_url); ?>" target="_blank" class="button hollow secondary small" id="brand-preview">
                <?php esc_html_e("Preview", 'gamb-duplica-wp'); ?>
                </a> &nbsp;
        <?php else :
                $link =  ControllersManager::getMenuLink(
                    ControllersManager::SETTINGS_SUBMENU_SLUG,
                    SettingsPageController::L2_SLUG_PACKAGE_BRAND
                );
            ?>
                <a href="<?php echo esc_url($link); ?>"><?php esc_html_e("Enable Branding", 'gamb-duplica-wp'); ?></a>
        <?php endif; ?>
        </div>

        <div class="dup-package-hdr-1">
            <?php esc_html_e("Prefills", 'gamb-duplica-wp') ?>&nbsp;
            <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
               data-tooltip-title="<?php esc_attr_e("Setup/Prefills", 'gamb-duplica-wp'); ?>"
               data-tooltip="<?php
                esc_attr_e(
                    'All values in this section are OPTIONAL! If you know ahead of time the database input fields the installer will use, 
                    then you can optionally enter them here and they will be prefilled at install time. 
                    Otherwise you can just enter them in at install time and ignore all these options in the Installer section.',
                    'gamb-duplica-wp'
                );
                ?>"
            >
            </i>
        </div>

        <!-- ===================
        BASIC/CPANEL TABS -->
        <div data-dpro-tabs="true">
            <ul>
                <li id="dpro-bsc-tab-lbl"><?php esc_html_e('Basic', 'gamb-duplica-wp') ?></li>
                <li id="dpro-cpnl-tab-lbl"><?php esc_html_e('cPanel', 'gamb-duplica-wp') ?></li>
            </ul>

            <!-- ===================
            TAB1: Basic -->
            <div>
                <div class="dup-package-hdr-2">
                    <?php esc_html_e("MySQL Server", 'gamb-duplica-wp') ?>
                    <div class="dup-package-hdr-usecurrent">
                        <a href="javascript:void(0)" onclick="DupPro.Pack.ApplyDataCurrent('s1-installer-dbbasic')">
                        [<?php esc_html_e('use current', 'gamb-duplica-wp') ?>]
                        </a>
                    </div>
                </div>

                <div id="s1-installer-dbbasic" >
                    <label class="lbl-larger" >
                        <?php esc_html_e("Host", 'gamb-duplica-wp') ?>:
                    </label>
                    <div>
                        <input 
                            type="text" 
                            name="installer_opts_db_host" 
                            id="dbhost" 
                            maxlength="200" 
                            placeholder="<?php esc_html_e("example: localhost (value is optional)", 'gamb-duplica-wp') ?>" 
                            data-current="<?php echo esc_attr(DB_HOST); ?>"
                            value="<?php echo esc_attr($dbHost); ?>"
                        >
                    </div>

                    <label class="lbl-larger" >
                        <?php esc_html_e("Database", 'gamb-duplica-wp') ?>:
                    </label>
                    <div>
                        <input 
                            type="text" 
                            name="installer_opts_db_name" 
                            id="dbname" 
                            maxlength="100" 
                            placeholder="<?php esc_html_e("example: DatabaseName (value is optional)", 'gamb-duplica-wp') ?>" 
                            data-current="<?php echo esc_attr(DB_NAME) ?>" 
                            value="<?php echo esc_attr($dbName); ?>"
                        >
                    </div>

                    <label class="lbl-larger" >
                        <?php esc_html_e("User", 'gamb-duplica-wp') ?>:
                    </label>
                    <div>
                        <input 
                            type="text" 
                            name="installer_opts_db_user" 
                            id="dbuser" 
                            maxlength="100" 
                            placeholder="<?php esc_html_e("example: DatabaseUser (value is optional)", 'gamb-duplica-wp') ?>" 
                            data-current="<?php echo esc_attr(DB_USER); ?>" 
                            value="<?php echo esc_attr($dbUser); ?>"
                        >
                    </div>
                </div>

            </div>

            <!-- ===================
            TAB2: cPanel -->
            <div>
                <div class="dup-package-hdr-2">
                    <?php esc_html_e("cPanel Login", 'gamb-duplica-wp') ?>
                </div>

                <label class="lbl-larger" >
                    <?php esc_html_e("Automation", 'gamb-duplica-wp') ?>:
                </label>
                <div>
                    <?php
                    $tipContent = __(
                        'Enabling this options will automatically select the cPanel tab when step one of the installer is shown.',
                        'gamb-duplica-wp'
                    );
                    ?>
                    <input 
                        type="checkbox" 
                        name="installer_opts_cpnl_enable" 
                        id="cpnl-enable" 
                        value="1"
                        <?php checked($cpnlEnable); ?> 
                    >
                    <label for="cpnl-enable"><?php esc_html_e('Auto Select cPanel', 'gamb-duplica-wp') ?></label>
                    <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
                       data-tooltip-title="<?php esc_attr_e('Auto Select cPanel', 'gamb-duplica-wp'); ?>"
                       data-tooltip="<?php echo esc_attr($tipContent); ?>">
                    </i>
                </div>

                <label class="lbl-larger" >
                    <?php esc_html_e("Host", 'gamb-duplica-wp') ?>:
                </label>
                <div>
                    <input 
                        type="text" 
                        name="installer_opts_cpnl_host" 
                        id="cpnl-host"  
                        maxlength="200" 
                        value="<?php echo esc_attr($cpnlHost); ?>"
                        placeholder="<?php esc_attr_e("example: cpanelHost (value is optional)", 'gamb-duplica-wp') ?>"
                    >
                </div>

                <label class="lbl-larger" >
                    <?php esc_html_e("User", 'gamb-duplica-wp') ?>:
                </label>
                <div>
                    <input 
                        type="text" 
                        name="installer_opts_cpnl_user" 
                        id="cpnl-user" 
                        value="<?php echo esc_attr($cpnlUser); ?>"
                        maxlength="200" 
                        placeholder="<?php esc_attr_e("example: cpanelUser (value is optional)", 'gamb-duplica-wp') ?>"
                    >
                </div>
                
                <div class="dup-package-hdr-2">
                    <?php esc_html_e("MySQL Server", 'gamb-duplica-wp') ?>
                    <div class="dup-package-hdr-usecurrent">
                        <a href="javascript:void(0)" onclick="DupPro.Pack.ApplyDataCurrent('s1-installer-dbcpanel')">
                        [<?php esc_html_e('use current', 'gamb-duplica-wp') ?>]
                        </a>
                    </div>
                </div>

                <div id="s1-installer-dbcpanel" >
                    <label class="lbl-larger" >
                        <?php esc_html_e("Action", 'gamb-duplica-wp') ?>:
                    </label>
                    <div>
                        <select name="installer_opts_cpnl_db_action" id="cpnl-dbaction">
                            <option value="" <?php selected($cpnlDbAction, ''); ?>>
                                <?php esc_html_e('Default', 'gamb-duplica-wp'); ?>
                            </option>
                            <option value="create" <?php selected($cpnlDbAction, 'create'); ?>>
                                <?php esc_html_e('Create A New Database', 'gamb-duplica-wp'); ?>
                            </option>
                            <option value="empty" <?php selected($cpnlDbAction, 'empty'); ?>>
                                <?php esc_html_e('Connect and Delete Any Existing Data', 'gamb-duplica-wp'); ?>
                            </option>
                            <option value="rename" <?php selected($cpnlDbAction, 'rename'); ?>>
                                <?php esc_html_e('Connect and Backup Any Existing Data', 'gamb-duplica-wp'); ?>
                            </option>
                            <option value="manual" <?php selected($cpnlDbAction, 'manual'); ?>>
                                <?php esc_html_e('Skip Database Extraction', 'gamb-duplica-wp'); ?>
                            </option>
                        </select>
                    </div>

                    <label class="lbl-larger" >
                        <?php esc_html_e("Host", 'gamb-duplica-wp') ?>:
                    </label>
                    <div>
                        <input 
                            type="text" 
                            name="installer_opts_cpnl_db_host" 
                            id="cpnl-dbhost" 
                            value="<?php echo esc_attr($cpnlDbHost); ?>"
                            maxlength="200" 
                            placeholder="<?php esc_attr_e("example: localhost (value is optional)", 'gamb-duplica-wp') ?>" 
                            data-current="<?php echo esc_html(DB_HOST); ?>"
                        >
                    </div>

                    <label class="lbl-larger" >
                        <?php esc_html_e("Database", 'gamb-duplica-wp') ?>:
                    </label>
                    <div>
                        <input 
                            type="text" 
                            name="installer_opts_cpnl_db_name" 
                            value="<?php echo esc_attr($cpnlDbName); ?>"
                            id="cpnl-dbname" 
                            data-parsley-pattern="/^[a-zA-Z0-9-_]+$/" 
                            maxlength="100"
                            placeholder="<?php esc_attr_e("example: DatabaseName (value is optional)", 'gamb-duplica-wp') ?>"
                            data-current="<?php echo esc_html(DB_NAME); ?>"
                        >
                    </div>

                    <label class="lbl-larger" >
                        <?php esc_html_e("User", 'gamb-duplica-wp') ?>:
                    </label>
                    <div>
                        <input 
                            type="text" 
                            name="installer_opts_cpnl_db_user" 
                            value="<?php echo esc_attr($cpnlDbUser); ?>"
                            id="cpnl-dbuser" 
                            data-parsley-pattern="/^[a-zA-Z0-9-_]+$/" 
                            maxlength="100" 
                            placeholder="<?php esc_attr_e("example: DatabaseUserName (value is optional)", 'gamb-duplica-wp') ?>" 
                            data-current="<?php echo esc_html(DB_USER); ?>" 
                        >
                    </div>
                </div>
            </div>
        </div><br/>

        <small><?php esc_html_e("Additional inputs can be entered at install time.", 'gamb-duplica-wp') ?></small>
        <br/><br/>
    </div>      
</div><br/>

<script>
    (function ($) {
        DupPro.Pack.ApplyDataCurrent = function (id)
        {
            $('#' + id + ' input').each(function ()
            {
                var attr = $(this).attr('data-current');
                if (typeof attr !== typeof undefined && attr !== false) {
                    $(this).val($(this).attr('data-current'));
                }
            });
        };
    <?php if (License::can(License::CAPABILITY_BRAND)) : ?>
    // brand-preview
    var $brand = $("#brand"),
        brandCheck = function (e) {
            var $this = $(this) || $brand;
            var $id = $this.val();
            <?php
            $prewURLs = [
                ControllersManager::getMenuLink(
                    ControllersManager::SETTINGS_SUBMENU_SLUG,
                    SettingsPageController::L2_SLUG_PACKAGE_BRAND,
                    null,
                    [
                        ControllersManager::QUERY_STRING_INNER_PAGE => SettingsPageController::BRAND_INNER_PAGE_EDIT,
                        'action'                                    => 'default',
                    ]
                ),
                ControllersManager::getMenuLink(
                    ControllersManager::SETTINGS_SUBMENU_SLUG,
                    SettingsPageController::L2_SLUG_PACKAGE_BRAND,
                    null,
                    [
                        ControllersManager::QUERY_STRING_INNER_PAGE => SettingsPageController::BRAND_INNER_PAGE_EDIT,
                        'action'                                    => 'edit',
                    ]
                ),
            ];
            ?>
            var $url = <?php echo json_encode($prewURLs); ?>;
            $url[1] += "&id=" + $id;

            $("#brand-preview").attr('href', $url[ $id > 0 ? 1 : 0 ]);

            $this.find('option[value="' + $id + '"]')
                    .prop('selected', true)
                    .parent();
        };
    $brand.on('select change', brandCheck);
    <?php endif; ?>


    }(window.jQuery));
</script>
