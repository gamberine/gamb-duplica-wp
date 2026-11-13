<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Package\NameFormat;
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
<p>
    <?php esc_html_e(
        'It is possible to customize the name of the backups using a fixed part and dynamic parts through tags. 
        The available tags are as follows:',
        'gamb-duplica-wp'
    ); ?>

    <ul>
        <?php foreach (NameFormat::getTagsDescriptions() as $tag => $description) : ?>
            <li>
                <strong>%<?php echo esc_html($tag); ?>%</strong> - <?php echo esc_html($description); ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <p>
        <?php
        echo wp_kses(
            __(
                'Important: <b>Backup date and time expressed in UTC</b> (Coordinated Universal Time). 
                The displayed date corresponds to the server\'s international time, independent of local time zones.',
                'gamb-duplica-wp'
            ),
            ViewHelper::GEN_KSES_TAGS
        );
        ?>
    </p>

    <p>
        <?php esc_html_e(
            'Here are some examples of name formats:',
            'gamb-duplica-wp'
        ); ?>
    </p>

    <ul>
        <li>
            <strong>%year%%month%%day%_%sitetitle%</strong> - 
            <?php esc_html_e('Backup name with date and site title (\'It\'s the default)', 'gamb-duplica-wp'); ?>            
        </li>
        <li>
            <strong>%year%%month%%day%_%hour%%minute%%second%_mytext_</strong> - 
            <?php esc_html_e('Backup name with date and time and fixed text', 'gamb-duplica-wp'); ?>
        </li>
        <li>
            <strong>%year%%month%%day%_%schedulename%</strong> - 
            <?php esc_html_e('Backup name with date and schedule name', 'gamb-duplica-wp'); ?>
        </li>
    </ul>
</p>