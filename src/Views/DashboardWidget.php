<?php

namespace gamb-duplica-wp\Views;

use GAMB_DUPLICA_WP_Package;
use GAMB_DUPLICA_WP_Package_Template_Entity;
use GAMB_DUPLICA_WP_PackageStatus;
use GAMB_DUPLICA_WP_Schedule_Entity;
use gamb-duplica-wp\Core\CapMng;
use gamb-duplica-wp\Core\Views\TplMng;
use gamb-duplica-wp\Models\Storages\AbstractStorageEntity;
use gamb-duplica-wp\Package\Recovery\RecoveryPackage;

/**
 * Dashboard widget
 */
class DashboardWidget
{
    const LAST_PACKAGE_TIME_WARNING            = 86400; // 24 hours
    const LAST_PACKAGES_LIMIT                  = 3;
    const RECOMMENDED_PLUGIN_ENABLED           = false;
    const RECOMMENDED_PLUGIN_DISMISSED_OPT_KEY = 'gamb-duplica-wp_recommended_plugin_dismissed';

    /**
     * Add the dashboard widget
     *
     * @return void
     */
    public static function init()
    {
        if (is_multisite()) {
            add_action('wp_network_dashboard_setup', [self::class, 'addDashboardWidget']);
        } else {
            add_action('wp_dashboard_setup', [self::class, 'addDashboardWidget']);
        }
    }

    /**
     * Render the dashboard widget
     *
     * @return void
     */
    public static function addDashboardWidget()
    {
        if (!CapMng::can(CapMng::CAP_BASIC, false)) {
            return;
        }

        wp_add_dashboard_widget(
            'gamb-duplica-wp_dashboard_widget',
            __('gamb-duplica-wp', 'gamb-duplica-wp'),
            [
                self::class,
                'renderContent',
            ]
        );
    }

    /**
     * Render the dashboard widget content
     *
     * @return void
     */
    public static function renderContent()
    {
        TplMng::getInstance()->setStripSpaces(true);
        ?>
        <div class="dup-dashboard-widget-content">
            <?php
            self::renderPackageCreate();
            self::renderRecentlyPackages();
            self::renderSections();
            if (self::RECOMMENDED_PLUGIN_ENABLED) { // @phpstan-ignore-line
                self::renderRecommendedPluginSection();
            }
            ?>
        </div>
        <?php
    }

    /**
     * Render the Backup create button
     *
     * @return void
     */
    protected static function renderPackageCreate()
    {
        TplMng::getInstance()->render(
            'parts/DashboardWidget/package-create-section',
            [
                'lastBackupString' => self::getLastBackupString(),
            ]
        );
    }

    /**
     * Render the last Backups
     *
     * @return void
     */
    protected static function renderRecentlyPackages()
    {
        /** @var GAMB_DUPLICA_WP_Package[] */
        $packages = GAMB_DUPLICA_WP_Package::get_packages_by_status(
            [
                [
                    'op'     => '>=',
                    'status' => GAMB_DUPLICA_WP_PackageStatus::COMPLETE,
                ],
            ],
            self::LAST_PACKAGES_LIMIT,
            0,
            'created DESC'
        );

        $totalsIds = GAMB_DUPLICA_WP_Package::get_ids_by_status(
            [
                [
                    'op'     => '>=',
                    'status' => GAMB_DUPLICA_WP_PackageStatus::COMPLETE,
                ],
            ]
        );

        $failuresIds = GAMB_DUPLICA_WP_Package::get_ids_by_status(
            [
                [
                    'op'     => '<',
                    'status' => 0,
                ],
            ]
        );

        TplMng::getInstance()->render(
            'parts/DashboardWidget/recently-packages',
            [
                'packages'      => $packages,
                'totalPackages' => count($totalsIds),
                'totalFailures' => count($failuresIds),
            ]
        );
    }

    /**
     * Render Duplicate sections
     *
     * @return void
     */
    protected static function renderSections()
    {
        if (($storages = AbstractStorageEntity::getIds()) === false) {
            $storages = [];
        }
        if (($templates = GAMB_DUPLICA_WP_Package_Template_Entity::getAllWithoutManualMode()) === false) {
            $templates = [];
        }
        if (($schedules = GAMB_DUPLICA_WP_Schedule_Entity::getIds()) === false) {
            $schedules = [];
        }
        $schedulesEnabled = GAMB_DUPLICA_WP_Schedule_Entity::get_active();

        $nextRunTime = -1;
        foreach ($schedulesEnabled as $schedule) {
            if ($schedule->next_run_time < $nextRunTime || $nextRunTime === -1) {
                $nextRunTime = $schedule->next_run_time;
            }
        }
        $nextDate  = date_i18n(get_option('date_format'), $nextRunTime);
        $nextHours = date_i18n(get_option('time_format'), $nextRunTime);

        if (
            ($recoverId = RecoveryPackage::getRecoverPackageId()) !== false &&
            ($recoverPackage = GAMB_DUPLICA_WP_Package::get_by_id($recoverId)) !== false
        ) {
            $recoverTime       = (int) strtotime($recoverPackage->getCreated());
            $recoverDate       = date_i18n(get_option('date_format'), $recoverTime);
            $recoverHours      = date_i18n(get_option('time_format'), $recoverTime);
            $recoverDateString = $recoverDate . ' ' . $recoverHours;
        } else {
            $recoverDateString = '';
        }

        TplMng::getInstance()->render(
            'parts/DashboardWidget/sections-section',
            [
                'numSchedules'        => count($schedules),
                'numSchedulesEnabled' => count($schedulesEnabled),
                'numTemplates'        => count($templates),
                'numStorages'         => count($storages),
                'nextScheduleString'  => ($nextRunTime >= 0 ? $nextDate . ' ' . $nextHours : ''),
                'recoverDateString'   => $recoverDateString,
            ]
        );
    }

    /**
     * Get the last backup string
     *
     * @return string HTML string
     */
    public static function getLastBackupString()
    {
        if (GAMB_DUPLICA_WP_Package::isPackageRunning()) {
            return '<span class="spinner"></span> <b>' . esc_html__('A Backup Is Currently Running.', 'gamb-duplica-wp') . '</b>';
        }

        /** @var GAMB_DUPLICA_WP_Package[] */
        $lastPackage = GAMB_DUPLICA_WP_Package::get_packages_by_status(
            [
                [
                    'op'     => '>=',
                    'status' => GAMB_DUPLICA_WP_PackageStatus::COMPLETE,
                ],
            ],
            1,
            0,
            'created DESC'
        );

        if (empty($lastPackage)) {
            return '<b>' . esc_html__('No backups have been created yet.', 'gamb-duplica-wp') . '</b>';
        }

        $createdTime = date_i18n(get_option('date_format'), (int) strtotime($lastPackage[0]->getCreated()));

        if ($lastPackage[0]->getPackageLife() > self::LAST_PACKAGE_TIME_WARNING) {
            $timeDiffClass = 'maroon';
        } else {
            $timeDiffClass = 'green';
        }

        $timeDiff = sprintf(
            _x('%s ago', '%s represents the time diff, eg. 2 days', 'gamb-duplica-wp'),
            $lastPackage[0]->getPackageLife('human')
        );

        return '<b>' . $createdTime . '</b> ' .
            " (" . '<span class="' . $timeDiffClass . '"><b>' .
            $timeDiff .
            '</b></span>' . ")";
    }

    /**
     * Return randomly chosen one of recommended plugins.
     *
     * @return false|array{name: string,slug: string,more: string,pro: array{file: string}}
     */
    protected static function getRecommendedPluginData()
    {
        $plugins = [
            'google-analytics-for-wordpress/googleanalytics.php' => [
                'name' => __('MonsterInsights', 'gamb-duplica-wp'),
                'slug' => 'google-analytics-for-wordpress',
                'more' => 'https://www.monsterinsights.com/',
                'pro'  => ['file' => 'google-analytics-premium/googleanalytics-premium.php'],
            ],
            'all-in-one-seo-pack/all_in_one_seo_pack.php'        => [
                'name' => __('AIOSEO', 'gamb-duplica-wp'),
                'slug' => 'all-in-one-seo-pack',
                'more' => 'https://aioseo.com/',
                'pro'  => ['file' => 'all-in-one-seo-pack-pro/all_in_one_seo_pack.php'],
            ],
            'coming-soon/coming-soon.php'                        => [
                'name' => __('SeedProd', 'gamb-duplica-wp'),
                'slug' => 'coming-soon',
                'more' => 'https://www.seedprod.com/',
                'pro'  => ['file' => 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php'],
            ],
            'wp-mail-smtp/wp_mail_smtp.php'                      => [
                'name' => __('WP Mail SMTP', 'gamb-duplica-wp'),
                'slug' => 'wp-mail-smtp',
                'more' => 'https://wpmailsmtp.com/',
                'pro'  => ['file' => 'wp-mail-smtp-pro/wp_mail_smtp.php'],
            ],
        ];

        $installed = get_plugins();

        foreach ($plugins as $id => $plugin) {
            if (isset($installed[$id])) {
                unset($plugins[$id]);
            }

            if (isset($installed[$plugin['pro']['file']])) {
                unset($plugins[$id]);
            }
        }
        return ($plugins ? $plugins[ array_rand($plugins) ] : false);
    }

    /**
     * Recommended plugin block HTML.
     *
     * @return void
     */
    public static function renderRecommendedPluginSection()
    {
        if (get_user_meta(get_current_user_id(), self::RECOMMENDED_PLUGIN_DISMISSED_OPT_KEY, true) != false) {
            return;
        }

        $plugin = self::getRecommendedPluginData();

        if (empty($plugin)) {
            return;
        }

        $installUrl = wp_nonce_url(
            self_admin_url('update.php?action=install-plugin&plugin=' . rawurlencode($plugin['slug'])),
            'install-plugin_' . $plugin['slug']
        );

        TplMng::getInstance()->render(
            'parts/DashboardWidget/recommended-section',
            [
                'plugin'     => $plugin,
                'installUrl' => $installUrl,
            ]
        );
    }
}
