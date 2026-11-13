<?php

/**
 * Main page menu controller
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Controllers;

use gamb-duplica-wp\Core\CapMng;
use gamb-duplica-wp\Core\Controllers\ControllersManager;
use gamb-duplica-wp\Core\Controllers\AbstractMenuPageController;

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
            file_get_contents(gamb-duplica-wp____PATH . '/assets/img/gamb-duplica-wp-logo-icon-menu.svg')
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
