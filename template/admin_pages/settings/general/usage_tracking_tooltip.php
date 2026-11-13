<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") || exit;

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<div>
    <b>
        <?php esc_html_e('All information sent to the server is anonymous except the license key and email.', 'gamb-duplica-wp'); ?><br>
        <?php esc_html_e('No information about storage or Backup\'s content are sent.', 'gamb-duplica-wp'); ?>
    </b>
</div>
<br>
<div>
    <?php
        esc_html_e(
            'Usage tracking for gamb-duplica-wp helps us better understand our users and their website needs by looking 
            at a range of server and website environments.',
            'gamb-duplica-wp'
        );
        ?>
    <b>
        <?php esc_html_e('This allows us to continuously improve our product as well as our Q&A / testing process.', 'gamb-duplica-wp'); ?>
    </b>
    <?php esc_html_e('Below is the list of information that gamb-duplica-wp collects as part of the usage tracking:', 'gamb-duplica-wp'); ?>
</div>
<ul>
    <li>
        <?php
        printf(
            esc_html_x(
                '%1$sPHP Version:%2$s So we know which PHP versions we have to test against (no one likes whitescreens or log files full of errors).',
                '%1$s and %2$s are are opening and closing bold (<b></b>) tags',
                'gamb-duplica-wp'
            ),
            '<b>',
            '</b>'
        );
        ?>
    </li>
    <li>
        <?php
        printf(
            esc_html_x(
                '%1$sWordPress Version:%2$s So we know which WordPress versions to support and test against.',
                '%1$s and %2$s are are opening and closing bold (<b></b>) tags',
                'gamb-duplica-wp'
            ),
            '<b>',
            '</b>'
        );
        ?>
    </li>
    <li>
        <?php
        printf(
            esc_html_x(
                '%1$sMySQL Version:%2$s So we know which versions of MySQL to support and test against for our custom tables.',
                '%1$s and %2$s are are opening and closing bold (<b></b>) tags',
                'gamb-duplica-wp'
            ),
            '<b>',
            '</b>'
        );
        ?>
    </li>
    <li>
        <?php
        printf(
            esc_html_x(
                '%1$sgamb-duplica-wp Version:%2$s So we know which versions of gamb-duplica-wp are potentially responsible for issues when we get bug reports, 
                allowing us to identify issues and release solutions much faster.',
                '%1$s and %2$s are are opening and closing bold (<b></b>) tags',
                'gamb-duplica-wp'
            ),
            '<b>',
            '</b>'
        );
        ?>
    </li>
    <li>
        <?php
        printf(
            esc_html_x(
                '%1$sPlugins and Themes infos:%2$s So we can figure out which ones can generate compatibility errors with gamb-duplica-wp.',
                '%1$s and %2$s are are opening and closing bold (<b></b>) tags',
                'gamb-duplica-wp'
            ),
            '<b>',
            '</b>'
        );
        ?>
    </li>
    <li>
        <?php
        printf(
            esc_html_x(
                '%1$sSite info:%2$s General information about the site such as database, file size, number of users, and sites in case it is a multisite. 
                This is useful for us to understand the critical issues of Backup creation.',
                '%1$s and %2$s are are opening and closing bold (<b></b>) tags',
                'gamb-duplica-wp'
            ),
            '<b>',
            '</b>'
        );
        ?>
    </li>
    <li>
        <?php
        printf(
            esc_html_x(
                '%1$sBackups infos:%2$s Information about the Backups created and the type of components included.',
                '%1$s and %2$s are are opening and closing bold (<b></b>) tags',
                'gamb-duplica-wp'
            ),
            '<b>',
            '</b>'
        );
        ?>
    </li>
    <li>
        <?php
        printf(
            esc_html_x(
                '%1$sStorage infos:%2$s Information about the type of storage used, 
                this data is useful for us to understand how to improve our support for external storages.(Only anonymized data is sent).',
                '%1$s and %2$s are are opening and closing bold (<b></b>) tags',
                'gamb-duplica-wp'
            ),
            '<b>',
            '</b>'
        );
        ?>
    </li>
    <li>
        <?php
        printf(
            esc_html_x(
                '%1$sTemplate infos:%2$s Information about the template components.',
                '%1$s and %2$s are are opening and closing bold (<b></b>) tags',
                'gamb-duplica-wp'
            ),
            '<b>',
            '</b>'
        );
        ?>
    </li>
    <li>
        <?php
        printf(
            esc_html_x(
                '%1$sSchedule infos:%2$s Information on how schedules are used.',
                '%1$s and %2$s are are opening and closing bold (<b></b>) tags',
                'gamb-duplica-wp'
            ),
            '<b>',
            '</b>'
        );
        ?>
    </li>
    <li>
        <?php
        printf(
            esc_html_x(
                '%1$sLicense key and email and url:%2$s If you\'re a gamb-duplica-wp customer, then we use this to determine if there\'s an issue 
                with your specific license key, and to link the profile of your site with the configuration of authentication to allow us to 
                determine if there are issues with your gamb-duplica-wp authentication.',
                '%1$s and %2$s are are opening and closing bold (<b></b>) tags',
                'gamb-duplica-wp'
            ),
            '<b>',
            '</b>'
        );
        ?>
    </li>
</ul>