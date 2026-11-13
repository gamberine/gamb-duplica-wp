<?php

namespace gamb-duplica-wp\Utils;

class CronUtils
{
    const INTERVAL_DAILTY  = 'gamb-duplica-wp_daily_cron';
    const INTERVAL_WEEKLY  = 'gamb-duplica-wp_weekly_cron';
    const INTERVAL_MONTHLY = 'gamb-duplica-wp_monthly_cron';

    /**
     * Init WordPress hooks
     *
     * @return void
     */
    public static function init()
    {
        add_filter('cron_schedules', [self::class, 'defaultCronIntervals']);
    }

    /**
     * Add gamb-duplica-wp pro cron schedules
     *
     * @param array<string, array<string,int|string>> $schedules schedules
     *
     * @return array<string, array<string,int|string>>
     */
    public static function defaultCronIntervals($schedules)
    {
        $schedules[self::INTERVAL_DAILTY] = [
            'interval' => DAY_IN_SECONDS,
            'display'  => __('Once a Day', 'gamb-duplica-wp'),
        ];

        $schedules[self::INTERVAL_WEEKLY] = [
            'interval' => WEEK_IN_SECONDS,
            'display'  => __('Once a Week', 'gamb-duplica-wp'),
        ];

        $schedules[self::INTERVAL_MONTHLY] = [
            'interval' => MONTH_IN_SECONDS,
            'display'  => __('Once a Month', 'gamb-duplica-wp'),
        ];

        return $schedules;
    }
}
