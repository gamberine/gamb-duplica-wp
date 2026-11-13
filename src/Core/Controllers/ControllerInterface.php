<?php

/**
 * Controller interface
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Core\Controllers;

interface ControllerInterface
{
    /**
     * Method called on WordPress hook init action
     *
     * @return void
     */
    public function hookWpInit();

    /**
     * Excecute controller
     *
     * @return void
     */
    public function run();

    /**
     * Render page
     *
     * @return void
     */
    public function render();
}
