<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined('ABSPATH') || exit;

/** @var string $currentPluginBootFile */

use GambDuplicaWP\Pro as GambDuplicaWP;

// CHECK IF PLUGIN CAN BE EXECTUED
require_once __DIR__ . '/src/Core/RequirementsInterface.php';
require_once __DIR__ . '/src/Pro/Requirements.php';

if (GambDuplicaWP\Requirements::canRun($currentPluginBootFile) === false) {
    return;
} else {
    // NOTE: Plugin code must be inside a conditional block to prevent functions definition, simple return is not enough
    define('GambDuplicaWP____PATH', dirname($currentPluginBootFile));
    define('GambDuplicaWP____FILE', $currentPluginBootFile);
    define('GambDuplicaWP____PLUGIN_URL', plugins_url('', $currentPluginBootFile));

    require_once GambDuplicaWP____PATH . '/src/Utils/AbstractAutoloader.php';
    require_once GambDuplicaWP____PATH . '/src/Utils/Autoloader.php';
    \GambDuplicaWP\Utils\Autoloader::register();

    require_once GambDuplicaWP____PATH . "/helper.php";
    require_once GambDuplicaWP____PATH . "/define.php";
    \GambDuplicaWP\Core\Bootstrap::init(GambDuplicaWP\Requirements::getAddsHash());
}
