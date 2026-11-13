<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<div class="dup-pro-recovery-message" >
    <p class="recovery-set-message-error">
        <i class="fa fa-exclamation-triangle"></i>&nbsp;<b><?php _e('Recovery Backup Issue Detected!', 'gamb-duplica-wp'); ?></b>
    <p>
    <p class="recovery-error-message">
        <!-- here is set the message received from the server -->
    </p>
    <p>
        <?php
        printf(
            _x(
                'For more information see %1$s[the documentation]%2$s',
                '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                'gamb-duplica-wp'
            ),
            '<a href="' . GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . 'how-to-handle-recovery-install-setup-launch-issues" target="_blank">',
            '</a>'
        );
        ?>
    </p>
</div>
