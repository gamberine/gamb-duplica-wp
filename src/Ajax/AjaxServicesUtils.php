<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Ajax;

use gamb-duplica-wp\Ajax\ServicesBrand;
use gamb-duplica-wp\Ajax\ServicesDashboard;
use gamb-duplica-wp\Ajax\ServicesImport;
use gamb-duplica-wp\Ajax\ServicesNotifications;
use gamb-duplica-wp\Ajax\ServicesPackage;
use gamb-duplica-wp\Ajax\ServicesRecovery;
use gamb-duplica-wp\Ajax\ServicesSchedule;
use gamb-duplica-wp\Ajax\ServicesSettings;
use gamb-duplica-wp\Ajax\ServicesStorage;
use gamb-duplica-wp\Ajax\ServicesTools;

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
