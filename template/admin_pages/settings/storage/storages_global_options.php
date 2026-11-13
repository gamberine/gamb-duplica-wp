<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

use gamb-duplica-wp\Controllers\SettingsPageController;
use gamb-duplica-wp\Models\Storages\StoragesUtil;

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

StoragesUtil::renderGlobalOptions();
