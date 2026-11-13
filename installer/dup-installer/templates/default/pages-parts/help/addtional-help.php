<?php

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$archiveConfig = DUPX_ArchiveConfig::getInstance();

if ($archiveConfig->brand['isDefault']) {
    ?>
    <div id="addtional-help-content">
        For additional help please visit <a href="https://gamb-duplica-wp.com/knowledge-base/" target="_blank">gamb-duplica-wp Migration and Backup Online Help</a>
    </div>
<?php } ?>
