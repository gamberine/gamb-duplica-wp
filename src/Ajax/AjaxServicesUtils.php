<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Ajax;

use GambDuplicaWP\Ajax\ServicesBrand;
use GambDuplicaWP\Ajax\ServicesDashboard;
use GambDuplicaWP\Ajax\ServicesImport;
use GambDuplicaWP\Ajax\ServicesNotifications;
use GambDuplicaWP\Ajax\ServicesPackage;
use GambDuplicaWP\Ajax\ServicesRecovery;
use GambDuplicaWP\Ajax\ServicesSchedule;
use GambDuplicaWP\Ajax\ServicesSettings;
use GambDuplicaWP\Ajax\ServicesStorage;
use GambDuplicaWP\Ajax\ServicesTools;

class AjaxServicesUtils
{
    /**
     * Init ajax hooks
     *
     * @return void
     */
    public static function loadServices()
    {
        (new ServicesImport())->init();
        (new ServicesRecovery())->init();
        (new ServicesSchedule())->init();
        (new ServicesStorage())->init();
        (new ServicesDashboard())->init();
        (new ServicesSettings())->init();
        (new ServicesNotifications())->init();
        (new ServicesPackage())->init();
        (new ServicesTools())->init();
        (new ServicesBrand())->init();
    }
}
