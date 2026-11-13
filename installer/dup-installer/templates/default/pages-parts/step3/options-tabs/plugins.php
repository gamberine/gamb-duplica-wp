<?php

/**
 *
 * @package templates/default
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use GambDuplicaWP\Installer\Core\InstState;
use GambDuplicaWP\Installer\Core\Params\PrmMng;

$paramsManager = PrmMng::getInstance();
?>

<div class="hdr-sub3"> <b>Activate <?php echo InstState::isNewSiteIsMultisite() ? ' Network ' : ' '; ?> Plugins Settings</b></div>
<?php
if (InstState::isRestoreBackup()) {
    dupxTplRender('parts/restore-backup-mode-notice');
}

$paramsManager->getHtmlFormParam(PrmMng::PARAM_PLUGINS);
