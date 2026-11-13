<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

echo esc_html(
    __('When enabled the archive file will be encrypted with a password that is required to open.', 'gamb-duplica-wp') . ' ' .
    __('The installer will also prompt for the password at install time.', 'gamb-duplica-wp') . ' ' .
    __('Encryption is a general deterrent and should not be substituted for properly keeping your files secure.', 'gamb-duplica-wp') . ' ' .
    __('Be sure to remove all installer files when the install process is completed.', 'gamb-duplica-wp')
);
?>
<ul>
    <li>
        <b>
            <?php esc_html_e('None:', 'gamb-duplica-wp'); ?>
        </b>
        <?php esc_html_e(
            "No protection system activated!  The installer or archive files can be accessed by any resource that knows the full URL to either file.",
            'gamb-duplica-wp'
        ); ?>
    </li>
    <li>
        <b>
            <?php esc_html_e('Installer password:', 'gamb-duplica-wp'); ?>
        </b>
        <?php esc_html_e(
            'The archive is NOT encrypted. When the installer starts, it will prompt for a password to prevent anyone from running the installer.',
            'gamb-duplica-wp'
        ); ?>       
    </li>
    <li>
    <b>
        <?php esc_html_e('Archive encryption:', 'gamb-duplica-wp'); ?>
        </b>
        <?php
        esc_html_e(
            'The archive IS encrypted with a password, and the installer will ask for a password when started.',
            'gamb-duplica-wp'
        );
        esc_html_e(
            'This option is the recommended maximum level of security.',
            'gamb-duplica-wp'
        );
        ?>  
    </li>
</ul>
