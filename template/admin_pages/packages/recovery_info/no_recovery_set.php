<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Views\ViewHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<h3 class="dup-title maroon">
    <?php ViewHelper::disasterIcon(); ?>&nbsp;<?php esc_html_e('Disaster Recovery - None Set', 'gamb-duplica-wp'); ?>
</h3>
<?php
    esc_html_e(
        'The recovery point can quickly restore a site to a prior state for any reason. To activate a recovery point follow these steps:',
        'gamb-duplica-wp'
    );
    ?>
<ol>
    <li>
        <?php
            printf(
                esc_html__('Select a Recovery Backup with the icon %s displayed*.', 'gamb-duplica-wp'),
                wp_kses(
                    ViewHelper::disasterIcon(false),
                    ['i' => ['class' => []]]
                )
            );
            ?>
    </li>
    <li>
        <?php
            printf(
                esc_html_x(
                    'Open details area %1$s and click the "Recover Backup ..." button.',
                    '%1$s represents an icon',
                    'gamb-duplica-wp'
                ),
                '<i class="fas fa-chevron-left fa-sm fa-fw"></i>'
            );
            ?>
    </li>
    <li>
        <?php esc_html_e('Follow the prompts and choose the action to perform.', 'gamb-duplica-wp'); ?>
    </li>
</ol>
<hr/>
<p>
    <b><?php esc_html_e('Additional Details:', 'gamb-duplica-wp'); ?></b>
    <?php
    esc_html_e(
        'Once a recovery point is set you can save the "Recovery Key" URL in a safe place for restoration later in the event your site goes down, gets 
        hacked or basically any reason you need to restore a site. In the event you still have access to your site you can also launch the recover 
        wizard from the details menu.',
        'gamb-duplica-wp'
    );
    ?>
</p>
<small>
    <i>
        <?php
        printf(
            esc_html__(
                '*Note: If you do not see a Recovery Backup %s icon in the Backups List. 
                Then be sure to build a full Backup that does not exclude any of the core WordPress files or database tables. 
                These core files and tables are required to build a valid recovery point.',
                'gamb-duplica-wp'
            ),
            wp_kses(
                ViewHelper::disasterIcon(false),
                ['i' => ['class' => []]]
            )
        );
        ?>
    </i>
</small>
