<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");

use GambDuplicaWP\Addons\ProBase\License\License;
use GambDuplicaWP\Controllers\StoragePageController;
use GambDuplicaWP\Core\CapMng;
use GambDuplicaWP\Installer\Package\ArchiveDescriptor;
use GambDuplicaWP\Libs\Snap\SnapJson;
use GambDuplicaWP\Models\Storages\AbstractStorageEntity;
use GambDuplicaWP\Package\Create\BuildComponents;

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var GAMB_DUPLICA_WP_Package $package
 */

$package = $tplData['package'];
$global  = GAMB_DUPLICA_WP_Global_Entity::getInstance();

$ui_css_general = (GAMB_DUPLICA_WP_UI_ViewState::getValue('dup-package-dtl-general-panel') ? 'display:block' : 'display:none');
$ui_css_storage = (GAMB_DUPLICA_WP_UI_ViewState::getValue('dup-package-dtl-storage-panel') ? 'display:block' : 'display:none');
$ui_css_archive = (GAMB_DUPLICA_WP_UI_ViewState::getValue('dup-package-dtl-archive-panel') ? 'display:block' : 'display:none');
$ui_css_install = (GAMB_DUPLICA_WP_UI_ViewState::getValue('dup-package-dtl-install-panel') ? 'display:block' : 'display:none');

$archiveDownloadURL    = $package->getLocalPackageFileURL(GAMB_DUPLICA_WP_Package_File_Type::Archive);
$logDownloadURL        = $package->getLocalPackageFileURL(GAMB_DUPLICA_WP_Package_File_Type::Log);
$installerDownloadURL  = $package->getLocalPackageFileURL(GAMB_DUPLICA_WP_Package_File_Type::Installer);
$showLinksDialogJson   = SnapJson::jsonEncodeEscAttr([
    "archive"   => $archiveDownloadURL,
    "log"       => $logDownloadURL,
    "installer" => $installerDownloadURL,
]);
$tooltopCreatedContent = __(
    'Backup date and time expressed in UTC (Coordinated Universal Time). 
    The displayed date corresponds to the server\'s international time, independent of local time zones.',
    'gamb-duplica-wp'
);

$lang_notset = __("- not set -", 'gamb-duplica-wp');
?>
<div class="dup-package-details-wrapper">
    <div class="toggle-box">
        <a href="javascript:void(0)" onclick="DupPro.Pack.OpenAll()">
            [<?php esc_html_e('open all', 'gamb-duplica-wp'); ?>]
        </a> &nbsp;
        <a href="javascript:void(0)" onclick="DupPro.Pack.CloseAll()">
            [<?php esc_html_e('close all', 'gamb-duplica-wp'); ?>]
        </a>
    </div>

    <!-- ===============================
    GENERAL -->
    <div class="dup-box dup-box-general">
    <div class="dup-box-title">
        <i class="fa fa-archive fa-sm"></i> <?php esc_html_e('General', 'gamb-duplica-wp') ?>
        <button class="dup-box-arrow">
            <span class="screen-reader-text"><?php esc_html_e('Toggle panel:', 'gamb-duplica-wp') ?> <?php esc_html_e('General', 'gamb-duplica-wp') ?></span>
        </button>
    </div>          
    <div class="dup-box-panel" id="dup-package-dtl-general-panel" style="<?php echo esc_attr($ui_css_general) ?>">
        <table class='dup-dtl-data'>
            <tr>
                <td><?php esc_html_e("Name", 'gamb-duplica-wp') ?>:</td>
                <td>
                    <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
                        <a href="javascript:void(0);" onclick="jQuery(this).parent().find('.dup-link-data').toggle()" class="dup-toggle-name">
                            <?php echo esc_html($package->getName()) ?>
                        </a> 
                        <div class="dup-link-data">
                            <b><?php esc_html_e("ID", 'gamb-duplica-wp') ?>:</b> <?php echo absint($package->ID); ?><br/>
                            <b><?php esc_html_e("Hash", 'gamb-duplica-wp') ?>:</b> <?php echo esc_html($package->getHash()); ?><br/>
                            <b><?php esc_html_e("Full Name", 'gamb-duplica-wp') ?>:</b> <?php echo esc_html($package->getNameHash()); ?><br/>
                        </div>
                    <?php } else { ?>
                        <?php echo esc_html($package->getName()) ?>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td><?php esc_html_e("Notes", 'gamb-duplica-wp') ?>:</td>
                <td><?php echo strlen($package->notes) ? esc_html($package->notes) : esc_html__("- no notes -", 'gamb-duplica-wp') ?></td>
            </tr>
            <tr>
                <td>
                    <?php esc_html_e("Created", 'gamb-duplica-wp') ?>:&nbsp;
                    <i 
                        class="fa-solid fa-circle-info"
                        data-tooltip-title="<?php esc_attr_e('Backup Date/Time', 'gamb-duplica-wp'); ?>"
                        data-tooltip="<?php echo esc_attr($tooltopCreatedContent); ?>"
                    ></i>
                </td>
                <td>
                    <?php if (strlen($package->getCreated())) : ?>
                        <a href="javascript:void(0);" onclick="jQuery(this).parent().find('.dup-link-data').toggle()" class="dup-toggle-created">
                            <?php echo esc_html(get_date_from_gmt($package->getCreated())) ?>
                        </a>

                        <div class="dup-link-data dup-link-data-created">
                            <?php
                            $datetime1 = new DateTime($package->getCreated());
                            $datetime2 = new DateTime(date("Y-m-d H:i:s"));
                            $diff      = $datetime1->diff($datetime2);

                            $fulldate = $diff->y . _n(' year, ', ' years, ', $diff->y, 'gamb-duplica-wp') .
                                $diff->m . _n(' month, ', ' months, ', $diff->m, 'gamb-duplica-wp') .
                                $diff->d . _n(' day', ' days', $diff->d, 'gamb-duplica-wp');
                            $fulldays = $diff->format('%a') . _n(' day', ' days', (int) $diff->format('%a'), 'gamb-duplica-wp');
                            ?>
                            <b><?php esc_html_e("Full Age", 'gamb-duplica-wp'); ?>: </b> <?php echo esc_html($fulldate); ?> <br/>
                            <b><?php esc_html_e("Days Old", 'gamb-duplica-wp'); ?>: </b> <?php echo esc_html($fulldays); ?> <br/>
                        </div>
                    <?php else : ?>
                        <?php esc_html_e("- not set in this version -", 'gamb-duplica-wp'); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><?php esc_html_e("Versions", 'gamb-duplica-wp') ?>:</td>
                <td>
                    <a href="javascript:void(0);" onclick="jQuery(this).parent().find('.dup-link-data').toggle()" class="dup-toggle-versions">
                        <?php echo esc_html($package->getVersion()) ?>
                    </a>
                    <div class="dup-link-data dup-link-data-versions">
                        <b><?php esc_html_e("WordPress", 'gamb-duplica-wp') ?>:</b> 
                            <?php echo strlen($package->VersionWP) ? esc_html($package->VersionWP) : esc_html__("- unknown -", 'gamb-duplica-wp') ?><br/>
                        <b><?php esc_html_e("PHP", 'gamb-duplica-wp') ?>:</b> 
                            <?php echo strlen($package->VersionPHP) ? esc_html($package->VersionPHP) : esc_html__("- unknown -", 'gamb-duplica-wp') ?><br/>
                        <b><?php esc_html_e("OS", 'gamb-duplica-wp') ?>:</b> 
                            <?php echo strlen($package->VersionOS) ? esc_html($package->VersionOS) : esc_html__("- unknown -", 'gamb-duplica-wp') ?><br/>
                        <b><?php esc_html_e("Mysql", 'gamb-duplica-wp') ?>:</b> 
                        <?php echo strlen($package->VersionDB) ? esc_html($package->VersionDB) : esc_html__("- unknown -", 'gamb-duplica-wp') ?> |
                        <?php
                            echo strlen($package->Database->Comments) ?
                            esc_html($package->Database->Comments) :
                            esc_html__('- unknown -', 'gamb-duplica-wp');
                        ?>
                        <br/>
                    </div>
                </td>
            </tr>       
            <tr>
                <td><?php esc_html_e("Runtime", 'gamb-duplica-wp') ?>:</td>
                <td>
                    <?php
                    $search_types = [
                        'sec.',
                        ',',
                    ];
                    $minute_view  = trim(str_replace($search_types, '', $package->Runtime));
                    if (is_numeric($minute_view)) {
                        $minute_view = gmdate("H:i:s", (int) $minute_view);
                    }
                    echo strlen($package->Runtime) ?
                    esc_html($package->Runtime) . ' &nbsp; <i>(' . esc_html($minute_view) . ')</i>' :
                    esc_html__('error running', 'gamb-duplica-wp');
                    ?>
                </td>
            </tr>
            <tr>
                <td><?php esc_html_e("Type", 'gamb-duplica-wp') ?>:</td>
                <td><?php echo esc_html($package->get_type_string()); ?></td>
            </tr>   
            <?php if (CapMng::can(CapMng::CAP_EXPORT, false)) { ?>       
            <tr>
                <td><?php esc_html_e("Files", 'gamb-duplica-wp') ?>:</td>
                <td>
                <div id="dpro-downloads-area">
                <?php if ($package->Status != GAMB_DUPLICA_WP_PackageStatus::ERROR) : ?>
                    <?php if ($package->haveLocalStorage()) : ?>
                        <button 
                            class="button hollow secondary small dup-downloads-installer" 
                            onclick="DupPro.Pack.DownloadFile('<?php echo esc_attr($installerDownloadURL); ?>');return false;"
                        >
                            <i class="fa fa-bolt fa-sm"></i> <?php esc_html_e('Installer', 'gamb-duplica-wp') ?>
                        </button>&nbsp;
                        <button 
                            class="button hollow secondary small dup-downloads-archive" 
                            onclick="DupPro.Pack.DownloadFile('<?php echo esc_attr($archiveDownloadURL); ?>');return false;"
                        >
                            <i class="far fa-file-archive fa-sm"></i>
                            <?php esc_html_e('Archive', 'gamb-duplica-wp') ?> - <?php echo esc_html($package->ZipSize) ?>
                        </button>&nbsp;
                        <button 
                            class="button hollow secondary small thickbox dup-downloads-share-links" 
                            onclick="DupPro.Pack.ShowLinksDialog(<?php echo esc_js($showLinksDialogJson); ?>);"
                        >
                            <i class="fas fa-share-alt fa-sm"></i>
                            <?php esc_html_e("Share File Links", 'gamb-duplica-wp') ?>
                        </button>
                        <table class="dup-pack-dtls-sublist">
                            <tr>
                                <td><?php esc_html_e("Archive", 'gamb-duplica-wp') ?>: </td>
                                <td>
                                    <a 
                                        href="<?php echo esc_attr($archiveDownloadURL); ?>" 
                                        target="file_results" class="dup-link-archive" 
                                        download="<?php echo esc_attr($package->Archive->File) ?>"
                                    >
                                        <?php echo esc_html($package->Archive->File) ?> 
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e("Installer", 'gamb-duplica-wp') ?>: </td>
                                <td>
                                    <a class="dup-link-installer" href="<?php echo esc_url($installerDownloadURL); ?>">
                                        <?php echo esc_html($package->Installer->getInstallerName()); ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e("Build Log", 'gamb-duplica-wp') ?>: </td>
                                <td>
                                    <a class="dup-link-build-log" href="<?php echo esc_attr($logDownloadURL); ?>" target="file_results">
                                        <?php echo esc_html($package->get_log_filename()); ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="sub-notes">
                                    <i class="fas fa-download"></i> <?php esc_html_e("Click links to download", 'gamb-duplica-wp') ?>
                                </td>
                            </tr>
                        </table>
                    <?php else : ?>
                        <!-- CLOUD ONLY FILES -->
                        <div id="dpro-downloads-msg">
                            <i class="fas fa-server"></i>
                            <?php
                            esc_html_e(
                                "The Backup files are in remote storage location(s).  Please visit the storage provider to download.",
                                'gamb-duplica-wp'
                            );
                            ?>
                        </div> <br/>
                        <button class="button button hollow secondary small" disabled="true">
                            <i class="fa fa-exclamation-triangle fa-sm"></i>
                            <?php esc_html_e('Installer', 'gamb-duplica-wp') ?> - <?php echo esc_html(GAMB_DUPLICA_WP_U::byteSize($package->Installer->Size)) ?>
                        </button>
                        <button class="button button hollow secondary small" disabled="true">
                            <i class="fa fa-exclamation-triangle fa-sm"></i>
                            <?php esc_html_e('Archive', 'gamb-duplica-wp') ?> - <?php echo esc_html($package->ZipSize) ?>
                        </button>
                        <div class="margin-top-1">
                            <b><?php esc_html_e("Build Log", 'gamb-duplica-wp') ?>:</b>&nbsp;
                            <a href="<?php echo esc_url($logDownloadURL); ?>" target="file_results"><?php echo esc_html($package->get_log_filename()); ?></a>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="maroon">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php esc_html_e("Backup files were not created successfully.  Please see the build log for more details.", 'gamb-duplica-wp') ?>
                    </div><br/>
                    <b><?php esc_html_e("Build Log", 'gamb-duplica-wp') ?>:</b>&nbsp;
                    <a href="<?php echo esc_attr($logDownloadURL); ?>" target="file_results"><?php echo esc_html($package->get_log_filename()); ?></a>
                <?php endif; ?>
                </div>
                </td>
            </tr>  
            <?php } ?> 
        </table>
    </div>
    </div>

    <!-- ==========================================
    DIALOG: SHARE LINKS -->
    <?php add_thickbox(); ?>
    <div id="dup-dlg-quick-path" title="<?php esc_attr_e('Download Links', 'gamb-duplica-wp'); ?>" style="display:none">
        <p class="maroon">
            <i class="fa fa-lock fa-sm"></i>
            <?php esc_html_e("The following links contain sensitive data.  Please share with caution!", 'gamb-duplica-wp'); ?>
        </p>
        
        <div style="padding: 0px 15px 15px 15px;">
            <a 
                href="javascript:void(0)" 
                style="display:inline-block; text-align:right" 
                onclick="DupPro.Pack.GetLinksText()"
            >
                [<?php esc_html_e('Select & Copy', 'gamb-duplica-wp'); ?>]
            </a><br/>
            <textarea id="dpro-dlg-quick-path-data" style='border:1px solid silver; border-radius:3px; width:99%; height:230px; font-size:11px'></textarea><br/>
            <i style='font-size:11px'>
                <?php
                    printf(
                        "%s <a href='" . esc_url(GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . "how-to-work-with-daf-files-and-the-duparchive-extraction-tool") .
                        "' target='_blank'>%s</a>",
                        esc_html__(
                            "An exact copy of the database SQL and installer file can both be found inside of the archive.zip/daf file. 
                            Download and extract the archive file to get a copy of the installer which will be named 'installer-backup.php'.
                            For details on how to extract a archive.daf file please see: ",
                            "gamb-duplica-wp"
                        ),
                        esc_html__("How do I work with DAF files and the DupArchive extraction tool?", "gamb-duplica-wp")
                    );
                    ?>
            </i>
        </div>
    </div>

    <!-- ===============================
    STORAGE -->
    <div class="dup-box dup-box-storage">
        <div class="dup-box-title">
            <i class="fas fa-server fa-sm"></i> <?php esc_html_e('Storage', 'gamb-duplica-wp') ?>
            <button class="dup-box-arrow">
                <span class="screen-reader-text">
                    <?php esc_html_e('Toggle panel:', 'gamb-duplica-wp') ?> <?php esc_html_e('Storage Options', 'gamb-duplica-wp') ?>
                </span>
            </button>
        </div>          
        <div class="dup-box-panel" id="dup-package-dtl-storage-panel" style="<?php echo esc_attr($ui_css_storage) ?>">
            <table class="widefat dup-table-list small package-tbl">
                <thead>
                    <tr>
                        <th style='width:175px'><?php esc_html_e('Type', 'gamb-duplica-wp') ?></th>
                        <th style='width:275px'><?php esc_html_e('Name', 'gamb-duplica-wp') ?></th>
                        <th style="white-space: nowrap"><?php esc_html_e('Location', 'gamb-duplica-wp') ?></th>
                    </tr>
                </thead>
                <?php
                $i                   = 0;
                $latest_upload_infos = $package->get_latest_upload_infos();
                foreach ($latest_upload_infos as $upload_info) :
                    if ($upload_info->has_completed(true) == false) {
                        // For now not displaying any cancelled or failed storages
                        continue;
                    }
                    if (($store = AbstractStorageEntity::getById($upload_info->getStorageId())) === false) {
                        continue;
                    }

                    $i++;
                    $store_type     = $store->getStypeName();
                    $store_id       = $store->getStypeName();
                    $store_location = $store->getLocationString();
                    $row_style      = ($i % 2) ? 'alternate' : '';
                    ?>
                    <tr class="package-row <?php echo esc_attr($row_style) ?>">
                        <td>
                            <?php
                            echo wp_kses(
                                $store->getSTypeIcon(),
                                [
                                    'i'   => [
                                        'class' => [],
                                    ],
                                    'img' => [
                                        'src'   => [],
                                        'class' => [],
                                        'alt'   => [],
                                    ],
                                ]
                            ),
                            '&nbsp;',
                            esc_html($store->getName());
                            ?>
                        </td>
                        <td>
                            <?php
                            if (CapMng::can(CapMng::CAP_STORAGE, false)) {
                                $editUrl = StoragePageController::getEditUrl($store)
                                ?>
                                <a href="<?php echo esc_url($editUrl); ?>" target="_blank">
                                    <?php  echo esc_html($store->getName()); ?>
                                </a>
                            <?php } else {
                                echo esc_html($store->getName());
                            } ?>
                        </td>
                        <td>
                            <?php
                            echo wp_kses(
                                $store->getHtmlLocationLink(),
                                [
                                    'a'    => [
                                        'href'   => [],
                                        'target' => [],
                                    ],
                                    'span' => [],
                                ]
                            );
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>    
                <?php if ($i == 0) : ?>
                    <tr>
                        <td colspan="3" style="text-align: center">
                            <?php esc_html_e('- No storage locations associated with this Backup -', 'gamb-duplica-wp'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>


    <!-- ===============================
    ARCHIVE -->
    <div class="dup-box dup-box-archive">
        <div class="dup-box-title">
            <i class="far fa-file-archive fa-sm"></i> <?php esc_html_e('Archive', 'gamb-duplica-wp') ?>
            <button class="dup-box-arrow">
                <span class="screen-reader-text"><?php esc_html_e('Toggle panel:', 'gamb-duplica-wp') ?> <?php esc_html_e('Archive', 'gamb-duplica-wp') ?></span>
            </button>
        </div>          
        <div class="dup-box-panel" id="dup-package-dtl-archive-panel" style="<?php echo esc_attr($ui_css_archive) ?>">

            <!-- FILES -->
            <div class="section-hdr">
                <i class="fas fa-folder-open fa-sm"></i>
                <?php esc_html_e('FILES', 'gamb-duplica-wp'); ?>
            </div>
            <table class='dup-dtl-data'>
                <tr>
                    <td><?php esc_html_e("Engine", 'gamb-duplica-wp') ?>: </td>
                    <td class="dup-archive-engine">
                        <?php
                        $zip_mode_string = __('Unknown', 'gamb-duplica-wp');

                        if ($package->build_progress->current_build_mode == GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive) {
                            $zip_mode_string = __("ZipArchive", 'gamb-duplica-wp');

                            if ($package->ziparchive_mode === GAMB_DUPLICA_WP_ZipArchive_Mode::SingleThread) {
                                $zip_mode_string = __("ZipArchive ST", 'gamb-duplica-wp');
                            }
                        } elseif ($package->build_progress->current_build_mode == GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec) {
                            $zip_mode_string = __("Shell Exec", 'gamb-duplica-wp');
                        } else {
                            $zip_mode_string = __("DupArchive", 'gamb-duplica-wp');
                        }

                        echo esc_html($zip_mode_string);
                        ?>
                    </td>
                </tr>           
                <tr>
                    <td><?php esc_html_e("Filters", 'gamb-duplica-wp') ?>: </td>
                    <td class="dup-file-filters">
                        <?php echo $package->Archive->FilterOn == 1 ? esc_html__('On', 'gamb-duplica-wp') : esc_html__('Off', 'gamb-duplica-wp'); ?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <div class="sub-filter-hdr">
                            <i class="far fa-folder-open"></i>
                            <?php esc_html_e("Directories", 'gamb-duplica-wp') ?>
                        </div>

                        <div class="sub-filter-data sub-filter-data-directories">
                            <?php
                                //CUSTOM
                                $title = __("User defined filtered directories", 'gamb-duplica-wp');
                                $count = count($package->Archive->FilterInfo->Dirs->Instance);
                            ?>
                            <a
                             href="javascript:void(0)"
                             onclick="jQuery(this).parent().children('.filter-info').eq(0).toggle(200)"
                             title="<?php echo esc_attr($title) ?>"
                            >
                            <i class="fa fa-filter fa-fw fa-xs"></i><?php esc_html_e('User Defined', 'gamb-duplica-wp') ?>
                            </a>
                            <sup>(<?php echo (int) $count ?>)</sup>
                            <br/>
                            <div class="filter-info">
                                <?php
                                if ($count == 0) {
                                    esc_html_e('- filter type not found -', 'gamb-duplica-wp');
                                } else {
                                    foreach ($package->Archive->FilterInfo->Dirs->Instance as $dir) {
                                        echo esc_html($dir) . ";<br/>";
                                    }
                                }
                                ?>
                            </div>

                            <?php
                                //UNREADABLE
                                $title = __("These paths are filtered because they are unreadable by the system", 'gamb-duplica-wp');
                                $count = count($package->Archive->FilterInfo->Dirs->Unreadable);
                            ?>
                            <a
                             href="javascript:void(0)"
                             onclick="jQuery(this).parent().children('.filter-info').eq(1).toggle(200)"
                             title="<?php esc_attr($title) ?>"
                            >
                            <i class="fa fa-filter fa-fw fa-xs"></i><?php esc_html_e('Unreadable', 'gamb-duplica-wp') ?>
                            </a>
                            <sup>(<?php echo (int) $count ?>)</sup>
                            <br/>
                            <div class='filter-info'>
                                <?php
                                if ($count == 0) {
                                    esc_html_e('- filter type not found -', 'gamb-duplica-wp');
                                } else {
                                    foreach ($package->Archive->FilterInfo->Dirs->Unreadable as $dir) {
                                        echo esc_html($dir) . ";<br/>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <div class="sub-filter-hdr">
                            <i class="far fa-file"></i>
                            <?php esc_html_e("Files", 'gamb-duplica-wp') ?>
                        </div>

                        <div class="sub-filter-data sub-filter-data-files">
                            <?php
                                //CUSTOM
                                $title = __("User defined filtered files", 'gamb-duplica-wp');
                                $count = count($package->Archive->FilterInfo->Files->Instance);
                            ?>
                            <a 
                            href="javascript:void(0)" 
                            onclick="jQuery(this).parent().children('.filter-info').eq(0).toggle(200)" 
                            title="<?php echo esc_attr($title) ?>"
                            >
                            <i class="fa fa-filter fa-fw fa-xs"></i><?php esc_html_e('User Defined', 'gamb-duplica-wp') ?>
                            </a>
                            <sup>(<?php echo (int) $count ?>)</sup>
                            <br/>

                            <div class="filter-info">
                                <?php
                                if ($count == 0) {
                                    esc_html_e('- filter type not found -', 'gamb-duplica-wp');
                                } else {
                                    foreach ($package->Archive->FilterInfo->Files->Instance as $file) {
                                        echo esc_html($file) . ";<br/>";
                                    }
                                }
                                ?>
                            </div>

                            <?php
                                //UNREADABLE
                                $title = __("These paths are filtered because they are unreadable by the system", 'gamb-duplica-wp');
                                $count = count($package->Archive->FilterInfo->Files->Unreadable);
                            ?>
                            <a 
                            href="javascript:void(0)" 
                            onclick="jQuery(this).parent().children('.filter-info').eq(1).toggle(200)" 
                            title="<?php echo esc_attr($title) ?>"
                            >
                            <i class='fa fa-filter fa-fw fa-xs'></i><?php esc_html_e('Unreadable', 'gamb-duplica-wp') ?>
                            </a>
                            <sup>(<?php echo (int) $count ?>)</sup>
                            <br/>
                            <div class="filter-info">
                                <?php
                                if ($count == 0) {
                                    esc_html_e('- filter type not found -', 'gamb-duplica-wp');
                                } else {
                                    foreach ($package->Archive->FilterInfo->Files->Unreadable as $file) {
                                        echo esc_html($file) . ";<br/>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>       
                <tr>
                    <td></td>
                    <td>
                        <div class="sub-filter-hdr">
                            <i class="far fa-sticky-note"></i>
                            <?php esc_html_e("Extensions", 'gamb-duplica-wp') ?>
                        </div>
                        
                        <div class="sub-filter-data sub-filter-data-extensions">
                            <?php
                            if (count($package->Archive->FilterExtsAll) > 0) {
                                $filter_ext = implode(',', $package->Archive->FilterExtsAll);
                                echo esc_html($filter_ext);
                            } else {
                                esc_html_e('- no filters -', 'gamb-duplica-wp');
                            }
                            ?>
                        </div>

                    </td>
                </tr>
                <tr>
                    <td><?php esc_html_e('Components: ', 'gamb-duplica-wp'); ?></td>
                    <td class="dup-backup-components">
                        <?php
                        echo wp_kses(
                            BuildComponents::displayComponentsList($package->components, "</br>"),
                            [
                                'br' => [],
                            ]
                        );
                        ?>
                    </td>
                </tr>
            </table><br/>

            <!-- DATABASE -->
            <div class="section-hdr">
                <i class="fas fa-database"></i>
                <?php esc_html_e('DATABASE', 'gamb-duplica-wp'); ?>
            </div>
            <?php if (!BuildComponents::isDBExcluded($package->components)) : ?>
            <table class='dup-dtl-data'>
                <tr>
                    <td><?php esc_html_e("Name", 'gamb-duplica-wp') ?>: </td>
                    <td class="dup-db-name"><?php echo esc_html($package->Database->info->name) ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e("Type", 'gamb-duplica-wp') ?>: </td>
                    <td class="dup-db-type"><?php echo esc_html($package->Database->Type) ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e("Engine", 'gamb-duplica-wp') ?>: </td>
                    <td class="dup-db-engine"><?php echo esc_html($package->Database->info->dbEngine) ?></td>
                </tr>            
                <tr>
                    <td><?php esc_html_e("SQL Mode", 'gamb-duplica-wp') ?>: </td>
                    <td class="dup-sql-mode"><?php echo esc_html($package->Database->DBMode) ?></td>
                </tr>           
                <tr>
                    <td><?php esc_html_e("Filters", 'gamb-duplica-wp') ?>: </td>
                    <td class="dup-db-filters">
                        <?php echo $package->Database->FilterOn == 1 ? esc_html__('On', 'gamb-duplica-wp') : esc_html__('Off', 'gamb-duplica-wp'); ?>
                    </td>
                </tr>
                <tr>
                    <td> </td>
                    <td>
                        <?php
                            $title = __('User defined table filters.', 'gamb-duplica-wp');
                            $count = (strlen($package->Database->FilterTables))
                                ? count(explode(',', $package->Database->FilterTables))
                                : 0;
                        ?>
                        
                        <div class="sub-filter-hdr">
                            <i class="fas fa-table"></i>
                            <?php esc_html_e('Tables', 'gamb-duplica-wp'); ?>
                        </div>
                        
                        <div class="sub-filter-data sub-filter-data-tables">
                            <a 
                                href='javascript:void(0)' 
                                onclick="jQuery(this).parent().children('.filter-info').eq(0).toggle(200)" title="<?php echo esc_attr($title); ?>"
                            >
                                <i class='fa fa-filter fa-fw fa-xs'></i><?php esc_html_e('User Defined', 'gamb-duplica-wp'); ?></a>  
                            <sup>(<?php echo (int) $count; ?>)</sup>

                            <div id="dup-filter-tables" class="filter-info">
                                <?php
                                if (strlen($package->Database->FilterTables)) {
                                    $filterTables = trim(str_replace(',', "<br/>", $package->Database->FilterTables));
                                    echo wp_kses($filterTables, ['br' => []]);
                                } else {
                                    esc_html_e('- no filters -', 'gamb-duplica-wp');
                                }
                                ?>
                            </div>
                            
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><?php esc_html_e('Size', 'gamb-duplica-wp') ?>: </td>
                    <td class="dup-db-size"><?php echo esc_html(GAMB_DUPLICA_WP_U::byteSize($package->Database->info->tablesSizeOnDisk));?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e('Collations', 'gamb-duplica-wp') ?>: </td>
                    <td class="dup-db-collations">
                        <?php
                        foreach ($package->Database->info->collationList as $collation) {
                            echo esc_html($collation) . "<br/>";
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <?php else : ?>
            <p>
                <?php esc_html_e("The Database was excluded from the Backup.", "gamb-duplica-wp"); ?>
            </p>
            <?php endif; ?>
            <br/>

            <!-- SETUP -->
            <div class="section-hdr">
                <i class="fas fa-sliders-h"></i>
                <?php esc_html_e('SETUP', 'gamb-duplica-wp'); ?>
            </div>
            <table class='dup-dtl-data'>
                <tr>
                    <td><?php esc_html_e("Security", 'gamb-duplica-wp'); ?>:</td>
                    <td class="dup-installer-security">
                    <?php
                    switch ($package->Installer->OptsSecureOn) {
                        case ArchiveDescriptor::SECURE_MODE_NONE:
                            esc_html_e('None', 'gamb-duplica-wp');
                            break;
                        case ArchiveDescriptor::SECURE_MODE_INST_PWD:
                            esc_html_e('Installer password', 'gamb-duplica-wp');
                            break;
                        case ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT:
                            esc_html_e('Archive encryption', 'gamb-duplica-wp');
                            break;
                        default:
                            throw new Exception('Invalid secure mode');
                    }
                    ?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="sub-notes">
                        <?php
                            esc_html_e('Lost passwords cannot be recovered. A new archive will need to be created.', 'gamb-duplica-wp');
                        ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>


    <!-- ===============================
    INSTALLER -->
    <div class="dup-box dup-box-installer" style="margin-bottom: 50px">
        <div class="dup-box-title">
            <i class="fa fa-bolt fa-sm"></i> <?php esc_html_e('Installer', 'gamb-duplica-wp') ?>
            <?php if ($package->Installer->isSecure()) { ?>
                <span id="dpro-install-secure-lock" title="<?php esc_attr_e('Installer password protection is on for this Backup.', 'gamb-duplica-wp') ?>">
                    <i class="fa fa-lock fa-sm"></i>
                </span>
            <?php } else { ?>
                <span id="dpro-install-secure-unlock" title="<?php esc_attr_e('Installer password protection is off for this Backup.', 'gamb-duplica-wp') ?>">
                    <i class="fa fa-unlock-alt"></i>
                </span>
            <?php } ?>
            <button class="dup-box-arrow">
                <span class="screen-reader-text">
                    <?php esc_html_e('Toggle panel:', 'gamb-duplica-wp') ?> <?php esc_html_e('Installer', 'gamb-duplica-wp') ?>
                </span>
            </button>
        </div>          
        <div class="dup-box-panel" id="dup-package-dtl-install-panel" style="<?php echo esc_attr($ui_css_install) ?>">
            <br/>

            <table class='dup-dtl-data'>
                <tr>
                    <td colspan="2"><div class="dup-package-hdr-1"><?php esc_html_e("SETUP", 'gamb-duplica-wp') ?></div></td>
                </tr>
                <?php if (License::can(License::CAPABILITY_BRAND)) : ?>
                    <tr>
                        <td><?php esc_html_e("Brand", 'gamb-duplica-wp'); ?>:</td>
                        <td>
                            <span style="color:#AF5E52; font-weight: bold">
                                <?php echo esc_html($package->Brand) ?>
                            </span>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td><?php esc_html_e("Security", 'gamb-duplica-wp'); ?>:</td>
                    <td>
                        <?php echo $package->Installer->isSecure() ? esc_html__("On", 'gamb-duplica-wp') : esc_html__("Off", 'gamb-duplica-wp'); ?>
                    </td>
                </tr>          
            </table><br/><br/>

            <table style="width:100%">
                <tr>
                    <td colspan="2"><div class="dup-package-hdr-1"><?php esc_html_e("PREFILLS", 'gamb-duplica-wp') ?></div></td>
                </tr>
            </table>

            <!-- ===================
            STEP1 TABS -->
            <div data-dpro-tabs="true">
                <ul>
                    <li>&nbsp; <?php esc_html_e('Basic', 'gamb-duplica-wp') ?> &nbsp;</li>
                    <li id="dpro-cpnl-tab-lbl"><?php esc_html_e('cPanel', 'gamb-duplica-wp') ?></li>
                </ul>

                <!-- ===================
                TAB1: Basic -->
                <div>
                    <table class='dup-dtl-data dup-dtl-basic'>
                        <tr>
                            <td><?php esc_html_e("Host", 'gamb-duplica-wp') ?>:</td>
                            <td><?php echo strlen($package->Installer->OptsDBHost) ? esc_html($package->Installer->OptsDBHost) : esc_html($lang_notset) ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e("Database", 'gamb-duplica-wp') ?>:</td>
                            <td>
                                <?php
                                echo strlen($package->Installer->OptsDBName) ?
                                esc_html($package->Installer->OptsDBName) :
                                esc_html($lang_notset);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e("User", 'gamb-duplica-wp') ?>:</td>
                            <td><?php echo strlen($package->Installer->OptsDBUser) ? esc_html($package->Installer->OptsDBUser) : esc_html($lang_notset) ?></td>
                        </tr>
                    </table><br/>
                </div>

                <!-- ===================
                TAB2: cPanel -->
                <div style="max-height: 250px" class="dup-dtl-cpanel">
                    <table class='dup-dtl-data'>
                        <tr>
                            <td colspan="2" class="sub-section">&nbsp; <b><?php esc_html_e("cPanel Login", 'gamb-duplica-wp') ?></b> &nbsp;</td>
                        </tr>
                        <tr class="sub-item">
                            <td><?php esc_html_e("Automation", 'gamb-duplica-wp') ?>:</td>
                            <td>
                                <?php echo ($package->Installer->OptsCPNLEnable) ? esc_html__('On', 'gamb-duplica-wp') : esc_html__('Off', 'gamb-duplica-wp') ?>
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td><?php esc_html_e("Host", 'gamb-duplica-wp') ?>:</td>
                            <td>
                                <?php
                                echo strlen($package->Installer->OptsCPNLHost) ?
                                esc_html($package->Installer->OptsCPNLHost) :
                                esc_html($lang_notset);
                                ?>
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td><?php esc_html_e("User", 'gamb-duplica-wp') ?>:</td>
                            <td>
                                <?php
                                echo strlen($package->Installer->OptsCPNLUser) ?
                                esc_html($package->Installer->OptsCPNLUser) :
                                esc_html($lang_notset);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="sub-section"><b><?php esc_html_e("MySQL Server", 'gamb-duplica-wp') ?></b></td>
                        </tr>
                        <tr class="sub-item">
                            <td><?php esc_html_e("Action", 'gamb-duplica-wp') ?>:</td>
                            <td>
                                <?php
                                echo ($package->Installer->OptsCPNLDBAction == 'create') ?
                                esc_html__("Create A New Database", 'gamb-duplica-wp') :
                                esc_html__("Connect to Existing Database and Remove All Data", 'gamb-duplica-wp');
                                ?>
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td><?php esc_html_e("Host", 'gamb-duplica-wp') ?>:</td>
                            <td>
                                <?php
                                echo strlen($package->Installer->OptsCPNLDBHost) ?
                                esc_html($package->Installer->OptsCPNLDBHost) :
                                esc_html($lang_notset);
                                ?>
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td><?php esc_html_e("Database", 'gamb-duplica-wp') ?>:</td>
                            <td>
                                <?php
                                echo strlen($package->Installer->OptsCPNLDBName) ?
                                esc_html($package->Installer->OptsCPNLDBName) :
                                esc_html($lang_notset);
                                ?>
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td><?php esc_html_e("User", 'gamb-duplica-wp') ?>:</td>
                            <td>
                                <?php
                                echo strlen($package->Installer->OptsCPNLDBUser) ?
                                esc_html($package->Installer->OptsCPNLDBUser) :
                                esc_html($lang_notset);
                                ?>
                            </td>
                        </tr>
                    </table><br/>

                </div>
            </div><br/>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) 
    {
        /*  Shows the Share 'Download Links' dialog
         *  @param json     JSON containing all links
         */
        DupPro.Pack.ShowLinksDialog = function(json)
        {
            var url = '#TB_inline?width=650&height=400&inlineId=dup-dlg-quick-path';
            tb_show("<?php esc_html_e('Backup File Links', 'gamb-duplica-wp') ?>", url);
            jQuery('#TB_window').addClass(<?php echo json_encode(GAMB_DUPLICA_WP_UI_Dialog::TB_WINDOW_CLASS); ?>);

            var msg = <?php printf(
                '"%s" + "\n\n%s:\n" + json.archive + "\n\n%s:\n" + json.installer + "\n\n%s:\n" + json.log + "\n\n%s";',
                '=========== SENSITIVE INFORMATION START ===========',
                esc_html__("ARCHIVE", 'gamb-duplica-wp'),
                esc_html__("INSTALLER", 'gamb-duplica-wp'),
                esc_html__("LOG", 'gamb-duplica-wp'),
                '=========== SENSITIVE INFORMATION END ==========='
            );
                                            ?>
            $("#dpro-dlg-quick-path-data").val(msg);
            return false;
        }

        /*  Open all Panels  */
        DupPro.Pack.OpenAll = function () {
            DupPro.UI.IsSaveViewState = false;
            var states = [];
            $("div.dup-box").each(function () {
                var pan = $(this).find('div.dup-box-panel');
                var panel_open = pan.is(':visible');
                if (!panel_open)
                    $(this).find('div.dup-box-title').trigger("click");
                states.push({
                    key: pan.attr('id'),
                    value: 1
                });
            });
            DupPro.UI.SaveMulViewStatesByPost(states);
            DupPro.UI.IsSaveViewState = true;
        };

        /*  Close all Panels */
        DupPro.Pack.CloseAll = function () {
            DupPro.UI.IsSaveViewState = false;
            var states = [];
            $("div.dup-box").each(function () {
                var pan = $(this).find('div.dup-box-panel');
                var panel_open = pan.is(':visible');
                if (panel_open)
                    $(this).find('div.dup-box-title').trigger("click");
                states.push({
                    key: pan.attr('id'),
                    value: 0
                });
            });
            DupPro.UI.SaveMulViewStatesByPost(states);
            DupPro.UI.IsSaveViewState = true;
        };

        /** 
         * Submits the password for validation
         */
        DupPro.togglePassword = function ()
        {
            var $input = $('#secure-pass');
            var $button = $('#secure-btn');
            if (($input).attr('type') == 'text') {
                $input.attr('type', 'password');
                $button.html('<i class="fas fa-eye fa-sm"></i>');
            } else {
                $input.attr('type', 'text');
                $button.html('<i class="fas fa-eye-slash fa-sm"></i>');
            }
        }

        /*  Selects all text in share dialog */
        DupPro.Pack.GetLinksText = function () {
            $('#dpro-dlg-quick-path-data').select();
            document.execCommand('copy');
        };


    });
</script>
