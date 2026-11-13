<?php

/**
 * Interface that collects the functions of initial gamb-duplica-wp Bootstrap
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Core;

use gamb-duplica-wp\MuPlugin\MuGenerator;
use gamb-duplica-wp\Utils\ExpireOptions;
use gamb-duplica-wp\Utils\UsageStatistics\CommStats;
use gamb-duplica-wp\Utils\UsageStatistics\PluginData;

/**
 * Uninstall class
 */
class Unistall
{
    /**
     * Registrer unistall hoosk
     *
     * @return void
     */
    public static function registreHooks()
    {
        if (is_admin()) {
            register_deactivation_hook(gamb-duplica-wp____FILE, [self::class, 'deactivate']);
        }
    }

    /**
     * Deactivation Hook:
     * Hooked into `register_deactivation_hook`.  Routines used to deactivate the plugin
     * For uninstall see uninstall.php  WordPress by default will call the uninstall.php file
     *
     * @return void
     */
    public static function deactivate()
    {
        MigrationMng::renameInstallersPhpFiles();

        //Logic has been added to uninstall.php
        //Force recalculation of next run time on activation
        //see the function \GAMB_DUPLICA_WP_Package_Runner::calculate_earliest_schedule_run_time()
        \GAMB_DUPLICA_WP_Log::trace("Resetting next run time for active schedules");
        $activeSchedules = \GAMB_DUPLICA_WP_Schedule_Entity::get_active();
        ExpireOptions::deleteAll();
        foreach ($activeSchedules as $activeSchedule) {
            $activeSchedule->next_run_time = -1;
            $activeSchedule->save();
        }

        // Unschedule custom cron event for cleanup if it's scheduled
        if (wp_next_scheduled(\GAMB_DUPLICA_WP_Global_Entity::CLEANUP_HOOK)) {
            // Unschedule the hook
            $timestamp = wp_next_scheduled(\GAMB_DUPLICA_WP_Global_Entity::CLEANUP_HOOK);
            wp_unschedule_event($timestamp, \GAMB_DUPLICA_WP_Global_Entity::CLEANUP_HOOK);
        }

        MuGenerator::remove();

        do_action('gamb-duplica-wp_pro_after_deactivation');
    }
}
