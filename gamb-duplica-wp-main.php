<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined('ABSPATH') || exit;

/** @var string $currentPluginBootFile */

use gamb-duplica-wp\Pro as gamb-duplica-wp;

// CHECK IF PLUGIN CAN BE EXECTUED
require_once __DIR__ . '/src/Core/RequirementsInterface.php';
require_once __DIR__ . '/src/Pro/Requirements.php';

if (gamb-duplica-wp\Requirements::canRun($currentPluginBootFile) === false) {
    return;
} else {
    // NOTE: Plugin code must be inside a conditional block to prevent functions definition, simple return is not enough
    define('gamb-duplica-wp____PATH', dirname($currentPluginBootFile));
    define('gamb-duplica-wp____FILE', $currentPluginBootFile);
    define('gamb-duplica-wp____PLUGIN_URL', plugins_url('', $currentPluginBootFile));

    require_once gamb-duplica-wp____PATH . '/src/Utils/AbstractAutoloader.php';
    require_once gamb-duplica-wp____PATH . '/src/Utils/Autoloader.php';
    \gamb-duplica-wp\Utils\Autoloader::register();

    require_once gamb-duplica-wp____PATH . "/helper.php";
    require_once gamb-duplica-wp____PATH . "/define.php";
    \gamb-duplica-wp\Core\Bootstrap::init(gamb-duplica-wp\Requirements::getAddsHash());
}
