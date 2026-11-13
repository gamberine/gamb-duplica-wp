<?php

/**
 * Main page menu controller
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Controllers;

use GambDuplicaWP\Core\CapMng;
use GambDuplicaWP\Core\Controllers\ControllersManager;
use GambDuplicaWP\Core\Controllers\AbstractMenuPageController;

class MainPageController extends AbstractMenuPageController
{
    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->pageSlug     = ControllersManager::MAIN_MENU_SLUG;
        $this->pageTitle    = 'gamb-duplica-wp Plugin';
        $this->menuPos      = 100;
        $this->menuLabel    = apply_filters('gamb-duplica-wp_main_menu_label', 'gamb-duplica-wp');
        $this->capatibility = CapMng::CAP_BASIC;
        $this->iconUrl      = 'data:image/svg+xml;base64,' . base64_encode(
            file_get_contents(GambDuplicaWP____PATH . '/assets/img/gamb-duplica-wp-logo-icon-menu.svg')
        );
    }

    /**
     * Render page
     *
     * @return void
     */
    public function render()
    {
        // This page is empty because WordPress also renders the first secondary page which is the list of Backups.
    }

    /**
     * Excecute controller logic
     *
     * @return void
     */
    public function run()
    {
        // This logic is already run by the submenu controller
    }
}
