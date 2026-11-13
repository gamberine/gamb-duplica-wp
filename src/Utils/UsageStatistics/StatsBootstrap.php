<?php

namespace gamb-duplica-wp\Utils\UsageStatistics;

use GAMB_DUPLICA_WP_Global_Entity;
use GAMB_DUPLICA_WP_Log;
use GAMB_DUPLICA_WP_Package;
use gamb-duplica-wp\Package\Create\BuildComponents;
use gamb-duplica-wp\Utils\CronUtils;

/**
 * StatsBootstrap
 */
class StatsBootstrap
{
    const USAGE_TRACKING_CRON_HOOK = 'gamb-duplica-wp_usage_tracking_cron';

    /**
     * Init WordPress hooks
     *
     * @return void
     */
    public static function init()
    {
        add_action('gamb-duplica-wp_pro_after_activation', [self::class, 'activationAction']);
        add_action('gamb-duplica-wp_pro_after_deactivation', [self::class, 'deactivationAction']);
        add_action('gamb-duplica-wp_pro_package_transfer_completed', [self::class, 'addPackageBuild']);
        add_action('gamb-duplica-wp_pro_build_fail', [self::class, 'addPackageBuild']);
        add_action('gamb-duplica-wp_after_scan_report', [self::class, 'addSiteSizes'], 10, 2);
        add_action('gamb-duplica-wp_usage_tracking_cron', [self::class, 'sendPluginStatCron']);
    }

    /**
     * Activation action
     *
     * @return void
     */
    public static function activationAction()
    {
        // Set cron
        if (!wp_next_scheduled(self::USAGE_TRACKING_CRON_HOOK)) {
            $randomTracking = wp_rand(0, WEEK_IN_SECONDS);
            $timeToStart    = strtotime('next sunday') + $randomTracking;
            wp_schedule_event($timeToStart, CronUtils::INTERVAL_WEEKLY, self::USAGE_TRACKING_CRON_HOOK);
        }

        if (PluginData::getInstance()->getStatus() !== PluginData::PLUGIN_STATUS_ACTIVE) {
            PluginData::getInstance()->setStatus(PluginData::PLUGIN_STATUS_ACTIVE);
            CommStats::pluginSend();
        }
    }

    /**
     * Deactivation action
     *
     * @return void
     */
    public static function deactivationAction()
    {
        // Unschedule custom cron event for cleanup if it's scheduled
        if (wp_next_scheduled(self::USAGE_TRACKING_CRON_HOOK)) {
            $timestamp = wp_next_scheduled(self::USAGE_TRACKING_CRON_HOOK);
            wp_unschedule_event($timestamp, self::USAGE_TRACKING_CRON_HOOK);
        }

        PluginData::getInstance()->setStatus(PluginData::PLUGIN_STATUS_INACTIVE);
        CommStats::pluginSend();
    }

    /**
     * Add Backup build,
     * don't use PluginData::getInstance()->addPackageBuild() directly in hook to avoid useless init
     *
     * @param GAMB_DUPLICA_WP_Package $package Backup
     *
     * @return void
     */
    public static function addPackageBuild(GAMB_DUPLICA_WP_Package $package)
    {
        PluginData::getInstance()->addPackageBuild($package);
    }

    /**
     * Add site size statistics
     *
     * @param GAMB_DUPLICA_WP_Package      $package Backup
     * @param array<string, mixed> $report  Scan report
     *
     * @return void
     */
    public static function addSiteSizes(GAMB_DUPLICA_WP_Package $package, $report)
    {
        $minComponents = [
            BuildComponents::COMP_DB,
            BuildComponents::COMP_CORE,
            BuildComponents::COMP_PLUGINS,
            BuildComponents::COMP_THEMES,
            BuildComponents::COMP_UPLOADS,
            BuildComponents::COMP_OTHER,
        ];

        $componentes = array_intersect($minComponents, $package->components);
        if (array_diff($minComponents, $componentes) !== []) {
            return;
        }

        PluginData::getInstance()->setSiteSize(
            $report['ARC']['USize'],
            $report['ARC']['UFullCount'],
            $report['DB']['SizeInBytes'],
            $report['DB']['TableCount']
        );
    }

    /**
     * Is tracking allowed
     *
     * @return bool
     */
    public static function isTrackingAllowed()
    {
        if (gamb-duplica-wp_USTATS_DISALLOW) { // @phpstan-ignore-line
            return false;
        }

        return GAMB_DUPLICA_WP_Global_Entity::getInstance()->getUsageTracking();
    }

    /**
     * Send plugin statistics
     *
     * @return void
     */
    public static function sendPluginStatCron()
    {
        if (!self::isTrackingAllowed()) {
            return;
        }

        GAMB_DUPLICA_WP_Log::trace("CRON: Sending plugin statistics");
        CommStats::pluginSend();
    }
}
