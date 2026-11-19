<?php

/**
 * Plugin Name: Gamb Duplica WP
 * Plugin URI: https://github.com/gamberine/gamb-duplica-wp
 * Description: Cópias de segurança e migração WordPress, com geração de arquivos, banco de dados e instalador de recuperação.
 * Version: 5.1.0
 * Requires at least: 5.3
 * Tested up to: 7.1
 * Requires PHP: 8.1
 * Author: Gamb
 * Author URI: https://github.com/gamberine
 * Network: true
 * Update URI: https://github.com/gamberine/gamb-duplica-wp
 * Text Domain: gamb-duplica-wp
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * Copyright 2011-2022  Snapcreek LLC
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

defined('ABSPATH') || exit;

define('GambDuplicaWP_USTATS_DISALLOW', true );
require_once __DIR__ . '/src/ProjectPolicy.php';
\GambDuplicaWP\ProjectPolicy::register();
// CHECK PHP VERSION
define('GambDuplicaWP_PRO_PHP_MINIMUM_VERSION', '8.1');
define('GambDuplicaWP_PRO_PHP_SUGGESTED_VERSION', '8.3');
require_once dirname(__FILE__) . "/src/Utils/GambDuplicaWPPhpVersionCheck.php";
if (GambDuplicaWPPhpVersionCheck::check(GambDuplicaWP_PRO_PHP_MINIMUM_VERSION, GambDuplicaWP_PRO_PHP_SUGGESTED_VERSION) === false) {
    return;
}
$currentPluginBootFile = __FILE__;

require_once dirname(__FILE__) . '/gamb-duplica-wp-main.php';
