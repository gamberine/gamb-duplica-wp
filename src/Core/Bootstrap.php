<?php

/**
 * Interface that collects the functions of initial gamb-duplica-wp Bootstrap
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Core;

use GAMB_DUPLICA_WP_Constants;
use GAMB_DUPLICA_WP_Global_Entity;
use GAMB_DUPLICA_WP_Log;
use gamb-duplica-wp\Core\MigrationMng;
use GAMB_DUPLICA_WP_Package;
use GAMB_DUPLICA_WP_Package_Runner;
use gamb-duplica-wp\Addons\ProBase\License\License;
use gamb-duplica-wp\Ajax\AjaxServicesUtils;
use gamb-duplica-wp\Controllers\SettingsPageController;
use gamb-duplica-wp\Core\Addons\AddonsManager;
use gamb-duplica-wp\Core\Controllers\ControllersManager;
use gamb-duplica-wp\Controllers\ToolsPageController;
use gamb-duplica-wp\Core\Views\Notifications;
use gamb-duplica-wp\Core\REST\RESTManager;
use gamb-duplica-wp\Core\Upgrade\UpgradePlugin;
use gamb-duplica-wp\Libs\Snap\SnapLog;
use gamb-duplica-wp\Libs\Snap\SnapUtil;
use gamb-duplica-wp\Models\Storages\StoragesUtil;
use gamb-duplica-wp\Models\SystemGlobalEntity;
use gamb-duplica-wp\Package\Import\PackageImporter;
use gamb-duplica-wp\Utils\CronUtils;
use gamb-duplica-wp\Utils\Email\EmailSummaryBootstrap;
use gamb-duplica-wp\Utils\ExpireOptions;
use gamb-duplica-wp\Utils\Translations;
use gamb-duplica-wp\Utils\UsageStatistics\StatsBootstrap;
use gamb-duplica-wp\Views\AdminNotices;
use gamb-duplica-wp\Views\DashboardWidget;
use gamb-duplica-wp\Views\PackageScreen;
use gamb-duplica-wp\Views\ScreenBase;
use gamb-duplica-wp\Views\ViewHelper;
use Error;
use Exception;

class Bootstrap
{
    /**
     *
     * @var string
     */
    private static $addsHash = '';

    /**
     * Init plugin
     *
     * @param string $addsHash pugin hash
     *
     * @return void
     */
    public static function init($addsHash)
    {
        self::$addsHash = $addsHash;

        register_activation_hook(gamb-duplica-wp____FILE, [UpgradePlugin::class, 'onActivationAction']);
        Unistall::registreHooks();

        CapMng::getInstance(); // init capabilties
        AddonsManager::getInstance()->inizializeAddons();
        StoragesUtil::registerTypes();
        add_action('plugins_loaded', [self::class, 'pluginsLoaded']);


        if (defined('WP_CLI') && WP_CLI) {
            // If WP CLI is running, we don't want to load unnecessary resources
            return;
        }

        ControllersManager::getInstance();
        RESTManager::getInstance();

        if (is_admin()) {
            AdminNotices::init();
            MigrationMng::init();
            DashboardWidget::init();
        }

        add_action('init', [self::class, 'hookWpInit']);
        add_action('init', [self::class, 'renameInstallerFile'], 20);
        add_action('init', function (): void {
            load_plugin_textdomain(
                GAMB_DUPLICA_WP_Constants::PLUGIN_SLUG,
                false,
                dirname(plugin_basename(gamb-duplica-wp____FILE)) . '/lang/'
            );
        });

        CronUtils::init();
        StatsBootstrap::init();
        EmailSummaryBootstrap::init();

        // These are necessary for cron job for cleanup of installer files
        add_action(GAMB_DUPLICA_WP_Global_Entity::CLEANUP_HOOK, [GAMB_DUPLICA_WP_Global_Entity::class, 'cleanupCronJob']);
        add_filter('cron_schedules', [GAMB_DUPLICA_WP_Global_Entity::class, 'customCleanupCronInterval']);
    }

    /**
     * Method called on WordPress hook init action
     *
     * @return void
     */
    public static function hookWpInit()
    {
        if (!AddonsManager::getInstance()->isAddonsReady()) {
            return;
        }

        if (ControllersManager::getInstance()->isgamb-duplica-wpPage()) {
            add_filter('admin_footer_text', [self::class, 'adminFooterText'], 1);
            add_filter('update_footer', [self::class, 'adminFooterVersion'], 99999);
        }

        AjaxServicesUtils::loadServices();
        Notifications::init();

        self::initialChecks();

        add_action('admin_init', [self::class, 'adminInit']);
        add_action('admin_footer', [self::class, 'adminFooter']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueGlobalScripts']);

        add_action('wp_ajax_GAMB_DUPLICA_WP_UI_ViewState_SaveByPost', ['\GAMB_DUPLICA_WP_UI_ViewState', 'saveByPost']);

        if (is_multisite()) {
            add_action('network_admin_menu', [self::class, 'menu']);
            add_filter('network_admin_plugin_action_links', [self::class, 'manageLink'], 10, 2);
            add_filter('network_admin_plugin_row_meta', [self::class, 'metaLinks'], 10, 2);
        } else {
            add_action('admin_menu', [self::class, 'menu']);
            add_filter('plugin_action_links', [self::class, 'manageLink'], 10, 2);
            add_filter('plugin_row_meta', [self::class, 'metaLinks'], 10, 2);
        }
    }

    /**
     * This function is executed on both frontend and backend side.
     * It is in this function that is tested if the plugin should be updated or a schedule should be started.
     *
     * @return void
     */
    public static function initialChecks()
    {
        $exception = null;
        try {
            // Only start the Backup runner and tracing once it's been confirmed that everything has been installed
            if (get_option(UpgradePlugin::DUP_VERSION_OPT_KEY) != gamb-duplica-wp_PRO_VERSION) {
                return;
            }

            if (
                !is_admin() &&
                ExpireOptions::getUpdate(
                    gamb-duplica-wp_PRO_FRONTEND_TRANSIENT,
                    true,
                    gamb-duplica-wp_PRO_FRONTEND_ACTION_DELAY
                ) !== false
            ) {
                return;
            }

            if (MigrationMng::isFirstLoginAfterInstallOption()) {
                // Skip initial check on migration
                return;
            }

            if (
                ExpireOptions::getUpdate(
                    gamb-duplica-wp_TMP_CLEANUP_CHECK_KEY,
                    true,
                    gamb-duplica-wp_TMP_CLEANUP_CHECK_DELAY
                ) === false &&
                GAMB_DUPLICA_WP_Package::isPackageRunning() === false
            ) {
                GAMB_DUPLICA_WP_Package::safe_tmp_cleanup();
            }

            self::dailyActions();
            GAMB_DUPLICA_WP_Package_Runner::init();
        } catch (Exception | Error $e) {
            $exception = $e;
        }

        if (!is_null($exception)) {
            $msg = "Initial checks error " . $exception->getMessage() . "\n" . SnapLog::getTextException($exception);
            SnapUtil::errorLog($msg);
            \GAMB_DUPLICA_WP_Log::trace($msg);
        }
    }

    /**
     * Rename old installer file
     *
     * @return void
     */
    public static function renameInstallerFile()
    {
        $exception = null;
        try {
            if (ExpireOptions::getUpdate(gamb-duplica-wp_PRO_INSTALLER_RENAME_KEY, true, gamb-duplica-wp_PRO_INSTALLER_RENAME_DELAY) !== false) {
                return;
            }

            MigrationMng::renameInstallersPhpFiles(gamb-duplica-wp_PRO_INSTALLER_RENAME_DELAY);
        } catch (Exception | Error $e) {
            $exception = $e;
        }

        if (!is_null($exception)) {
            $msg = "Installer rename error " . $exception->getMessage() . "\n" . SnapLog::getTextException($exception);
            SnapUtil::errorLog($msg);
            \GAMB_DUPLICA_WP_Log::trace($msg);
        }
    }

    /**
     * Return plugin hash
     *
     * @return string
     */
    public static function getAddsHash()
    {
        return self::$addsHash;
    }

    /**
     * Method called on admin_init hook
     *
     * @return void
     */
    public static function adminInit()
    {
        self::startInitSettings();

        // custom host init
        \GAMB_DUPLICA_WP_Custom_Host_Manager::getInstance()->init();

        self::registerJsCss();
        self::registerTranslations();

        $global = \GAMB_DUPLICA_WP_Global_Entity::getInstance();
        if ($global->unhook_third_party_js || $global->unhook_third_party_css) {
            add_action('admin_enqueue_scripts', [self::class, 'unhookThirdPartyAssets'], 99999, 1);
        }

        add_action('in_admin_header', [ViewHelper::class, 'adminLogoHeader'], 100);
        add_filter('admin_body_class', [ViewHelper::class, 'addBodyClass'], 100, 1);
        add_action('admin_head', [ScreenBase::class, 'getCustomCss']);

        if (gamb-duplica-wp_CAPABILITIES_RESET) { // @phpstan-ignore-line
            CapMng::getInstance()->hardReset();
        }
    }

    /**
     * Daily gamb-duplica-wp actions
     *
     * @return void
     */
    protected static function dailyActions()
    {
        if (
            ExpireOptions::getUpdate(
                'daily_bootstrap_actions',
                true,
                DAY_IN_SECONDS
            ) !== false
        ) {
            return;
        }

        try {
            GAMB_DUPLICA_WP_Log::trace("Doing daily actions");
            PackageImporter::purgeOldImports();
            do_action('gamb-duplica-wp_pro_daily_actions');
        } catch (Exception | Error $e) {
            GAMB_DUPLICA_WP_Log::trace("DAILY BOOTSTRAP ACTIONS ERROR\n" . SnapLog::getTextException($e));
        }
    }

    /**
     * Check if is debug mode
     *
     * @return bool
     */
    public static function isDebug()
    {
        return defined('gamb-duplica-wp_DEBUG') && gamb-duplica-wp_DEBUG; // @phpstan-ignore-line
    }

    /**
     * Get min prefix
     *
     * @return string
     */
    public static function getMinPrefix()
    {
        return self::isDebug() ? '' : '.min';
    }

    /**
     * Register styles and scripts
     *
     * @return void
     */
    protected static function registerJsCss()
    {
        if (wp_doing_ajax()) {
            return;
        }

        $min = self::getMinPrefix();

        wp_register_style(
            'gamb-duplica-wp-font-awesome',
            gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/css/font-awesome/css/all.min.css',
            [],
            '6.4.2'
        );
        wp_register_style(
            'formstone',
            gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/formstone/bundle.css',
            [],
            'v1.4.16-1'
        );
        wp_register_style(
            'jstree',
            gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/jstree/themes/snap/style.css',
            [],
            '3.8.1'
        );
        wp_register_style(
            'dup-pro-select2',
            gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/select2/css/select2.min.css',
            [],
            gamb-duplica-wp_PRO_VERSION
        );
        wp_register_style(
            'gamb-duplica-wp-main',
            gamb-duplica-wp_PRO_PLUGIN_URL . "assets/css/gamb-duplica-wp{$min}.css",
            [
                'gamb-duplica-wp-font-awesome',
                'jstree',
                'formstone',
                'dup-pro-select2',
            ],
            gamb-duplica-wp_PRO_VERSION
        );
        wp_register_style(
            'dup-plugin-global-style',
            gamb-duplica-wp_PRO_PLUGIN_URL . "assets/css/gamb-duplica-wp-global{$min}.css",
            [],
            gamb-duplica-wp_PRO_VERSION
        );

        //JS
        wp_register_script('dup-pro-handlebars', gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/handlebars.min.js', ['jquery'], '4.0.10');
        wp_register_script('parsley', gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/parsleyjs/parsley.min.js', ['jquery'], '2.9.2');
        wp_register_script('popper', gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/popper/popper.min.js', [], '2.4.4');
        wp_register_script('dup-pro-tippy', gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/tippy/tippy-bundle.umd.min.js', ['popper'], '6.2.6');
        wp_register_script('formstone', gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/formstone/bundle.js', ['jquery'], 'v1.4.16-1');
        wp_register_script('jstree', gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/jstree/jstree.min.js', [], '3.3.7');
        wp_register_script('jscookie', gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/jscookie/js.cookie.min.js', [], '3.0.0');
        wp_register_script(
            'dup-pro-select2',
            gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/select2/js/select2.js',
            ['jquery'],
            gamb-duplica-wp_PRO_VERSION,
            true
        );
        wp_register_script(
            'gamb-duplica-wp-tooltip',
            gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/gamb-duplica-wp-tooltip.js',
            ['dup-pro-tippy'],
            gamb-duplica-wp_PRO_VERSION
        );
        wp_register_script(
            'dup-pro-import-installer',
            gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/import-installer.js',
            ['jquery'],
            gamb-duplica-wp_PRO_VERSION,
            true
        );
        wp_register_script(
            'dup-pro-dynamic-help',
            gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/dynamic-help.js',
            ['jquery'],
            gamb-duplica-wp_PRO_VERSION,
            true
        );
    }

    /**
     * Register translations
     *
     * @return void
     */
    protected static function registerTranslations()
    {
        $translations = new Translations(
            GAMB_DUPLICA_WP_Constants::PLUGIN_SLUG,
            GAMB_DUPLICA_WP_Constants::TRANSLATIONS_API_URL,
            'plugin'
        );
        $translations->init();
    }

    /**
     * Enqueue CSS Styles:
     * Loads all CSS style libs/source for DupPro
     *
     * @return void
     */
    public static function enqueueStyles()
    {
        wp_enqueue_style('gamb-duplica-wp-main');
    }

    /**
     * Enqueue Global CSS Styles
     *
     * @return void
     */
    public static function enqueueGlobalStyles()
    {
        wp_enqueue_style('dup-plugin-global-style');
    }

    /**
     * Hooked into `admin_enqueue_scripts`.  Init routines for all admin pages
     *
     * @return void
     */
    public static function enqueueGlobalScripts()
    {
        wp_enqueue_script(
            'dup-pro-global-script',
            gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/js/global-admin-script.js',
            ['jquery'],
            gamb-duplica-wp_PRO_VERSION,
            true
        );
        wp_localize_script(
            'dup-pro-global-script',
            'dup_pro_global_script_data',
            [
                'nonce_admin_notice_to_dismiss'              => wp_create_nonce('gamb-duplica-wp_pro_admin_notice_to_dismiss'),
                'nonce_dashboard_widged_info'                =>  wp_create_nonce("gamb-duplica-wp_pro_dashboad_widget_info"),
                'nonce_dashboard_widged_dismiss_recommended' => wp_create_nonce("gamb-duplica-wp_pro_dashboad_widget_dismiss_recommended"),
                'ajaxurl'                                    => admin_url('admin-ajax.php'),
            ]
        );
    }

    /**
     * Enqueue Scripts:
     * Loads all required javascript libs/source for DupPro
     *
     * @return void
     */
    public static function enqueueScripts()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-color');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-progressbar');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('parsley');
        wp_enqueue_script('accordion');
        wp_enqueue_script('gamb-duplica-wp-tooltip');
        wp_enqueue_script('formstone');
        wp_enqueue_script('jstree');
        wp_enqueue_script('jscookie');
        wp_enqueue_script('dup-pro-select2');
        wp_enqueue_script('dup-pro-dynamic-help');

        wp_localize_script(
            'gamb-duplica-wp-tooltip',
            'l10nDupTooltip',
            [
                'copy'       => esc_html__('Copy to clipboard', 'gamb-duplica-wp'),
                'copied'     => esc_html__('Copied to Clipboard', 'gamb-duplica-wp'),
                'copyUnable' => esc_html__('Unable to copy', 'gamb-duplica-wp'),
            ]
        );
        wp_localize_script(
            'dup-pro-dynamic-help',
            'l10nDupDynamicHelp',
            [
                'failedLoad' => esc_html__('Failed to load help content!', 'gamb-duplica-wp'),
            ]
        );
    }

    /**
     * Plugins Loaded:
     * Hooked into `plugin_loaded`.  Called once any activated plugins have been loaded.
     *
     * @return void
     */
    public static function pluginsLoaded()
    {
        if (!is_admin()) {
            return;
        }

        if (gamb-duplica-wp_PRO_VERSION != get_option(UpgradePlugin::DUP_VERSION_OPT_KEY)) {
            UpgradePlugin::onActivationAction();
        }

        try {
            self::patchedDataInitialization();
        } catch (\Exception $ex) {
            \GAMB_DUPLICA_WP_Log::traceError("Could not do data initialization. " . $ex->getMessage());
        }
    }

    /**
     * Init settings check
     *
     * @return void
     */
    public static function startInitSettings()
    {
        $nonce                = SnapUtil::sanitizeTextInput(INPUT_GET, 'nonce');
        $clearScheduleFailure = SnapUtil::sanitizeIntInput(INPUT_GET, 'dup_pro_clear_schedule_failure', 0);
        if (wp_verify_nonce($nonce, 'dup_pro_clear_schedule_failure') && $clearScheduleFailure === 1) {
            $system_global                  = SystemGlobalEntity::getInstance();
            $system_global->schedule_failed = false;
            $system_global->save();
        }

        if (!defined('WP_MAX_MEMORY_LIMIT')) {
            define('WP_MAX_MEMORY_LIMIT', '256M');
        }

        if (SnapUtil::isIniValChangeable('memory_limit')) {
            @ini_set('memory_limit', WP_MAX_MEMORY_LIMIT);
        }
    }

    /**
     * Action Hook:
     * Hooked into `admin_menu`.  Loads all of the admin menus for DupPro
     *
     * @return void
     */
    public static function menu()
    {
        ControllersManager::getInstance()->registerMenu();

        $page_packages = \gamb-duplica-wp\Controllers\PackagesPageController::getInstance()->getMenuHookSuffix();
        if (($page_packages = \gamb-duplica-wp\Controllers\PackagesPageController::getInstance()->getMenuHookSuffix())  != false) {
            add_action('admin_print_scripts-' . $page_packages, [self::class, 'enqueueScripts']);
            add_action('admin_print_styles-' . $page_packages, [self::class, 'enqueueStyles']);
            $packageScreen = new PackageScreen($page_packages); // Init hook on constructor
        }

        if (($page_import = \gamb-duplica-wp\Controllers\ImportPageController::getInstance()->getMenuHookSuffix())  != false) {
            add_action('admin_print_scripts-' . $page_import, [self::class, 'enqueueScripts']);
            add_action('admin_print_styles-' . $page_import, [self::class, 'enqueueStyles']);
        }
        if (($page_schedules = \gamb-duplica-wp\Controllers\SchedulePageController::getInstance()->getMenuHookSuffix())  != false) {
            add_action('admin_print_scripts-' . $page_schedules, [self::class, 'enqueueScripts']);
            add_action('admin_print_styles-' . $page_schedules, [self::class, 'enqueueStyles']);
        }
        if (($page_storage = \gamb-duplica-wp\Controllers\StoragePageController::getInstance()->getMenuHookSuffix())  != false) {
            add_action('admin_print_scripts-' . $page_storage, [self::class, 'enqueueScripts']);
            add_action('admin_print_styles-' . $page_storage, [self::class, 'enqueueStyles']);
        }
        if (($page_settings = \gamb-duplica-wp\Controllers\SettingsPageController::getInstance()->getMenuHookSuffix())  != false) {
            add_action('admin_print_scripts-' . $page_settings, [self::class, 'enqueueScripts']);
            add_action('admin_print_styles-' . $page_settings, [self::class, 'enqueueStyles']);
        }
        if (($page_tools = \gamb-duplica-wp\Controllers\ToolsPageController::getInstance()->getMenuHookSuffix())  != false) {
            add_action('admin_print_scripts-' . $page_tools, [self::class, 'enqueueScripts']);
            add_action('admin_print_styles-' . $page_tools, [self::class, 'enqueueStyles']);
        }
        $page_installer = \gamb-duplica-wp\Controllers\ImportInstallerPageController::getInstance()->getMenuHookSuffix();

        //Init Blank Pages
        \gamb-duplica-wp\Controllers\HelpPageController::getInstance();

        add_action('admin_print_styles', [self::class, 'enqueueGlobalStyles']);
    }

    /**
     * Data Patches:
     * Handles data that needs to be initialized because of fixes etc
     *
     * @return void
     */
    protected static function patchedDataInitialization()
    {
        $global = \GAMB_DUPLICA_WP_Global_Entity::getInstance();
        $global->configure_dropbox_transfer_mode();

        if ($global->initial_activation_timestamp == 0) {
            $global->initial_activation_timestamp = time();
            $global->save();
        }
    }

    /**
     * Remove all external styles and scripts coming from other plugins
     * which may cause compatibility issue, especially with React
     *
     * @param string $hook Hook string
     *
     * @return void
     */
    public static function unhookThirdPartyAssets($hook)
    {
        if (!ControllersManager::getInstance()->isgamb-duplica-wpPage()) {
            return;
        }

        $global = \GAMB_DUPLICA_WP_Global_Entity::getInstance();
        $assets = [];

        if ($global->unhook_third_party_css) {
            $assets['styles'] = wp_styles();
        }

        if ($global->unhook_third_party_js) {
            $assets['scripts'] = wp_scripts();
        }

        foreach ($assets as $type => $asset) {
            foreach ($asset->registered as $handle => $dep) {
                $src = $dep->src;
                // test if the src is coming from /wp-admin/ or /wp-includes/ or /wp-fsqm-pro/.
                if (
                    is_string($src) && // For some built-ins, $src is true|false
                    strpos($src, 'wp-admin') === false &&
                    strpos($src, 'wp-include') === false &&
                    // things below are specific to your plugin, so change them
                    strpos($src, 'gamb-duplica-wp') === false &&
                    strpos($src, 'woocommerce') === false &&
                    strpos($src, 'jetpack') === false &&
                    strpos($src, 'debug-bar') === false
                ) {
                    'scripts' === $type ? wp_dequeue_script($handle) : wp_dequeue_style($handle);
                }
            }
        }
    }

    /**
     * Plugin MetaData:
     * Adds the manage link in the plugins list
     *
     * @param string[] $links links list
     * @param string   $file  plugin file
     *
     * @return string[] The manage link in the plugins list
     */
    public static function manageLink($links, $file)
    {
        static $this_plugin;

        if (!$this_plugin) {
            $this_plugin = plugin_basename(gamb-duplica-wp____FILE);
        }

        if ($file == $this_plugin) {
            $url           = ControllersManager::getMenuLink(ControllersManager::PACKAGES_SUBMENU_SLUG);
            $settings_link = "<a href='$url'>" . __('Manage', 'gamb-duplica-wp') . '</a>';
            array_unshift($links, $settings_link);
        }
        return $links;
    }

    /**
     * Plugin MetaData:
     * Adds links to the plugins manager page
     *
     * @param string[] $links links list
     * @param string   $file  plugin file
     *
     * @return string[] The meta help link data for the plugins manager
     */
    public static function metaLinks($links, $file)
    {
        $plugin = plugin_basename(gamb-duplica-wp____FILE);
        if ($file == $plugin) {
            $help_url = ControllersManager::getMenuLink(ControllersManager::TOOLS_SUBMENU_SLUG);
            $links[]  = sprintf('<a href="%1$s" title="%2$s">%3$s</a>', esc_url($help_url), __('Get Help', 'gamb-duplica-wp'), __('Help', 'gamb-duplica-wp'));

            return $links;
        }
        return $links;
    }

    /**
     * When user is on a gamb-duplica-wp related admin pages, display admin footer text
     * that graciously asks them to rate us.
     *
     * @return string
     */
    public static function adminFooterText()
    {
        $text  = '';
        $text .= '<span class="dup-styles" ><i>';
        $text .= sprintf(
            wp_kses(
                _x(
                    'Please rate <strong>gamb-duplica-wp</strong> %1$s on %2$sWordPress.org%3$s to help us spread the word.',
                    '%1$s represents 5 start symbols linked to wordpress.org review page, %2$s,%2$s represents one,close link',
                    'gamb-duplica-wp'
                ),
                [
                    'a'      => [
                        'href'   => [],
                        'target' => [],
                        'rel'    => [],
                    ],
                    'strong' => [],
                ]
            ),
            '<a href="https://wordpress.org/support/plugin/gamb-duplica-wp/reviews/?filter=5#new-post" 
            class="link-style" target="_blank" rel="noopener noreferrer">' .
            '&#9733;&#9733;&#9733;&#9733;&#9733;</a>',
            '<a href="https://wordpress.org/support/plugin/gamb-duplica-wp/reviews/?filter=5#new-post" 
            class="link-style" target="_blank" rel="noopener">',
            '</a>'
        );
        return $text . '</i></span>';
    }

    /**
     * Updates admin footer text by adding gamb-duplica-wp version
     *
     * @param string $defaultText Default WP footer text
     *
     * @return string Modified version text
     */
    public static function adminFooterVersion($defaultText)
    {
        if (!ControllersManager::getInstance()->isgamb-duplica-wpPage()) {
            return $defaultText;
        }

        return sprintf(
            '%1$s | gamb-duplica-wp Pro %2$s',
            $defaultText,
            esc_html(gamb-duplica-wp_PRO_VERSION)
        );
    }

    /**
     * Footer Hook:
     * Hooked into `admin_footer`.  Returns display elements for the admin footer area
     *
     * @return void
     */
    public static function adminFooter()
    {
        if (
            !ControllersManager::getInstance()->isgamb-duplica-wpPage() ||
            !get_option('gamb-duplica-wp_pro_trace_log_enabled', false)
        ) {
            return;
        }

        if (!CapMng::can(CapMng::CAP_SETTINGS, false) && !CapMng::can(CapMng::CAP_CREATE, false)) {
            return;
        }

        $txt_trace_zero = esc_html__('Download', 'gamb-duplica-wp') . ' (0B)';
        $turnOffUrl     = SettingsPageController::getInstance()->getTraceActionUrl(false);
        $traceLogUrl    = ControllersManager::getMenuLink(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            ToolsPageController::L2_SLUG_LOGS
        );

        $ajaxGetTraceUrl = admin_url('admin-ajax.php') . '?' . http_build_query([
            'action' => 'gamb-duplica-wp_pro_get_trace_log',
            'nonce'  => wp_create_nonce('gamb-duplica-wp_pro_get_trace_log'),
        ]);

        if (
            ControllersManager::isCurrentPage(
                ControllersManager::TOOLS_SUBMENU_SLUG,
                ToolsPageController::L2_SLUG_LOGS
            )
        ) {
            $clear_trace_log_js = 'DupPro.UI.ClearTraceLog(1);';
        } else {
            $clear_trace_log_js = 'DupPro.UI.ClearTraceLog(0); jQuery("#dup_pro_trace_txt").html(' . json_encode($txt_trace_zero) . '); ';
        }
        ?>
        <style>
            p#footer-upgrade {
                display: none
            }
        </style>
        <div class="dup-styles" >
            <div id="dpro-monitor-trace-area">
                <b><?php esc_html_e('TRACE LOG OPTIONS', 'gamb-duplica-wp'); ?></b><br />
                <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
                    <a class="button tiny hollow gray margin-bottom-0" href="<?php echo esc_url($traceLogUrl); ?>" target="_duptracelog">
                        <i class="fa fa-file-alt"></i> <?php esc_html_e('View', 'gamb-duplica-wp'); ?>
                    </a>
                    <a class="button tiny hollow gray margin-bottom-0" onclick="<?php echo esc_attr($clear_trace_log_js); ?>">
                        <i class="fa fa-times"></i> <?php esc_html_e('Clear', 'gamb-duplica-wp'); ?>
                    </a>
                    <a 
                        class="button tiny hollow gray margin-bottom-0" 
                        onclick="<?php echo esc_attr('location.href = ' . json_encode($ajaxGetTraceUrl) . ';'); ?>"
                    >
                        <i class="fa fa-download"></i> <span id="dup_pro_trace_txt">
                            <?php echo esc_html__('Download', 'gamb-duplica-wp') . ' (' . esc_html(\GAMB_DUPLICA_WP_Log::getTraceStatus()) . ')'; ?>
                        </span>
                    </a>
                <?php } ?>
                <?php if (CapMng::can(CapMng::CAP_SETTINGS, false)) { ?>
                    <a class="button tiny hollow gray margin-bottom-0" href="<?php echo esc_url($turnOffUrl); ?>">
                        <i class="fa fa-power-off"></i> <?php echo esc_html__('Turn Off', 'gamb-duplica-wp'); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
        <?php
    }
}
