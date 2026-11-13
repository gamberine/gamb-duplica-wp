<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Utils\Help\Help;
use GambDuplicaWP\Libs\Snap\SnapJson;

defined("ABSPATH") || exit;

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$helpPageUrl = SnapJson::jsonEncode(Help::getHelpPageUrl());
?>
<div id="dup-meta-screen" class="dup-styles"></div>
<div class="dup-header dup-styles">
    <img src="<?php echo esc_url(GambDuplicaWP_PRO_PLUGIN_URL . 'assets/img/gamb-duplica-wp-header-logo.svg'); ?>" alt="Gamb Duplica WP" width="320" height="50">
    <a class="dup-global-help" href="https://github.com/gamberine/gamb-duplica-wp/blob/main/README.md" target="_blank" rel="noopener noreferrer">Documenta??o</a>
</div>
