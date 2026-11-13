<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Controllers\ImportPageController;
use gamb-duplica-wp\Controllers\SettingsPageController;
use gamb-duplica-wp\Controllers\StoragePageController;
use gamb-duplica-wp\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var ?GAMB_DUPLICA_WP_Package $package
 */

$global               = GAMB_DUPLICA_WP_Global_Entity::getInstance();
$package              = $tplData['package'];
$archive_exists       = ($package->getLocalPackageFilePath(GAMB_DUPLICA_WP_Package_File_Type::Archive) != false);
$installer_exists     = ($package->getLocalPackageFilePath(GAMB_DUPLICA_WP_Package_File_Type::Installer) != false);
$archiveDownloadURL   = $package->getLocalPackageFileURL(GAMB_DUPLICA_WP_Package_File_Type::Archive);
$installerDownloadURL = $package->getLocalPackageFileURL(GAMB_DUPLICA_WP_Package_File_Type::Installer);
$defaultStorageUrl    = StoragePageController::getEditDefaultUrl();

$txt_RequiresRemote = sprintf(
    _x(
        'This option requires the Backup to use the built-in default %1$sstorage location %2$s%3$s',
        '1 and 3 are opening and closing anchor/link tags, 2 is an icon',
        'gamb-duplica-wp'
    ),
    '<a href="' . esc_url($defaultStorageUrl) . '" target="_blank">',
    '<i class="far fa-hdd fa-fw fa-sm"></i>',
    '</a>'
);

?>
<div class="dup-ovr-ctrls-hdrs clearfix">
    <h3 class="font-bold margin-bottom-0">
        <i class="fas fa-link fa-fw"></i> <?php esc_html_e('Install Resources', 'gamb-duplica-wp');?>
    </h3>
    <small class="xsmall dark-gray-color" >
        <i><?php esc_html_e('Links are sensitive. Keep them safe!', 'gamb-duplica-wp');?></i>
    </small>

    <?php if (CapMng::can(CapMng::CAP_STORAGE, false)) { ?>
        <small class="float-right" >
            <a 
                class="dup-ovr-ref-links-more" 
                href="javascript:void(0)"
                onclick="DupPro.Pack.ShowRemote(<?php echo (int) $package->ID; ?>, '<?php echo esc_js($package->getNameHash()); ?>');"
            >
                <i class="fas fa-server fa-xs"></i> <?php esc_html_e('Storages', 'gamb-duplica-wp');?>
            </a>
        </small>
    <?php } ?>
</div>


<!-- =======================
ARCHIVE FILE: -->
<div class="dup-ovr-copy-flex-box">
    <div class="flex-item">
        <i class="far fa-file-archive fa-fw"></i>
        <b class="black-color" >
            <?php esc_html_e('Backup File', 'gamb-duplica-wp');?>
        </b>
        <sup>
            <?php
            $archiveFileToolTipTitle = sprintf(
                esc_html_x(
                    'This link is used with the %1$sImport Link Install%2$s feature. 
                    Use the Copy Link button to copy this URL Backup File link to import on another WordPress site.',
                    '1 and 2 are opening and closing anchor tags',
                    'gamb-duplica-wp'
                ),
                '<a href="' . esc_url(ImportPageController::getInstance()->getMenuLink()) . '">',
                '</a>'
            );
            ?>
            <i class="fas fa-question-circle fa-xs fa-fw dup-archive-help"
                data-tooltip-title="<?php esc_attr_e("Backup File", 'gamb-duplica-wp'); ?>"
                data-tooltip="<?php echo esc_attr($archiveFileToolTipTitle);?>"></i>
        </sup>
    </div>
    <div class="flex-item"></div>
</div>
<div class="dup-ovr-copy-flex-box dup-box-file">
<?php if ($archive_exists) : ?>
    <div class="flex-item">
    <input type="text" class="dup-ovr-ref-links margin-bottom-0" readonly="readonly"
        value="<?php echo esc_attr($archiveDownloadURL); ?>"
        title="<?php echo esc_attr($archiveDownloadURL); ?>"
        onfocus="jQuery(this).select();" />
    <span class="fas fa-arrow-alt-circle-down dup-ovr-ref-links-icon"
            title="<?php esc_attr_e('Archive Import Link (URL)', 'gamb-duplica-wp');?>"></span>
    </div>
    <div class="flex-item">
    <span onclick="jQuery(this).parent().parent().find('.dup-ovr-ref-links').select();">
        <span data-dup-copy-value="<?php echo esc_attr($archiveDownloadURL); ?>"
                class="button hollow small gray dup-ovr-ref-copy no-select">
            <i class='far fa-copy dup-cursor-pointer'></i>
            <?php esc_html_e('Copy Link', 'gamb-duplica-wp');?>
        </span>
    </span>
    <span class="dup-ovr-ref-dwnld button hollow small gray "
        aria-label="<?php esc_html_e("Download Archive", 'gamb-duplica-wp') ?>"
        onclick="DupPro.Pack.DownloadFile('<?php echo esc_attr($archiveDownloadURL); ?>',
                '<?php echo esc_attr($package->get_archive_filename()); ?>');">
        <i class="fas fa-download"></i> <?php esc_html_e('Download', 'gamb-duplica-wp');?>
    </span>
    </div>
<?php else : ?>
    <div class="flex-item maroon">
        <?php
        echo wp_kses(
            $txt_RequiresRemote,
            [
                'a' => [
                    'href'   => [],
                    'target' => [],
                ],
                'i' => [
                    'class' => [],
                ],
            ]
        );
        ?>.
    </div>
<?php endif; ?>
</div>
<!-- =======================
ARCHIVE INSTALLER: -->
<?php
switch ($global->installer_name_mode) {
    case GAMB_DUPLICA_WP_Global_Entity::INSTALLER_NAME_MODE_SIMPLE:
        $settingsPackageUrl    = SettingsPageController::getInstance()->getMenuLink(SettingsPageController::L2_SLUG_PACKAGE);
        $lockIcon              = 'fa-lock-open';
        $installerToolTipTitle = sprintf(
            __(
                'Using standard installer name. To improve security, switch to hashed change in %1$sSettings%2$s',
                'gamb-duplica-wp'
            ),
            '<a href="' . esc_url($settingsPackageUrl) . '" >',
            '</a>'
        );
        break;

    case GAMB_DUPLICA_WP_Global_Entity::INSTALLER_NAME_MODE_WITH_HASH:
    default:
        $lockIcon              = 'fa-lock';
        $installerToolTipTitle = __('Using more secure, hashed installer name.', 'gamb-duplica-wp');
        break;
}
$installerName = $package->Installer->getDownloadName();
?>

<i class="fas fa-bolt fa-fw"></i>
<b class="black-color" >
    <?php esc_html_e('Backup Installer', 'gamb-duplica-wp');?>
</b>
<sup>
<i class="fas <?php echo esc_attr($lockIcon); ?> dup-cursor-pointer fa-fw fa-xs dup-installer-help"
style="padding-left:3px"
data-tooltip="<?php echo esc_html($installerToolTipTitle); ?>"></i>
</sup>
<div class="dup-ovr-copy-flex-box dup-box-installer">
<?php if ($installer_exists) : ?>
    <div class="flex-item">
        <input type="text" class="dup-ovr-ref-links margin-bottom-0" readonly="readonly"
            value="<?php echo esc_attr($installerName); ?>"
            title="<?php echo esc_attr($installerName); ?>"
            onfocus="jQuery(this).select();" 
        >
        <small class="dup-info-msg01 xsmall dark-gray-color">
            <i><?php esc_html_e('These links contain highly sensitive data. Share with extra caution!', 'gamb-duplica-wp');?></i>
        </small>
    </div>
    <div class="flex-item">
        <span onclick="jQuery(this).parent().parent().find('.dup-ovr-ref-links').select();">
            <span data-dup-copy-value="<?php echo esc_attr($installerName); ?>" 
                class="dup-ovr-ref-copy no-select button hollow small gray">
                <i class='far fa-copy dup-cursor-pointer'></i>
                <?php esc_html_e('Copy Name', 'gamb-duplica-wp');?>
            </span>
        </span>
        <span class="dup-ovr-ref-dwnld button hollow small gray "
            aria-label="<?php esc_html_e("Download Installer", 'gamb-duplica-wp') ?>"
            onclick="DupPro.Pack.DownloadFile('<?php echo esc_attr($installerDownloadURL); ?>');">
            <i class="fas fa-download"></i> <?php esc_html_e('Download', 'gamb-duplica-wp');?>
        </span>
    </div>
<?php else : ?>
    <div class="flex-item maroon">
        <?php
        echo wp_kses(
            $txt_RequiresRemote,
            [
                'a' => [
                    'href'   => [],
                    'target' => [],
                ],
                'i' => [
                    'class' => [],
                ],
            ]
        );
        ?>.
    </div>
<?php endif; ?>
</div>
