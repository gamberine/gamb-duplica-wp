<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

use GambDuplicaWP\Controllers\SettingsPageController;
use GambDuplicaWP\Models\Storages\StoragesUtil;

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

StoragesUtil::renderGlobalOptions();
