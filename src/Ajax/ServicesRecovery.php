<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Ajax;

use GAMB_DUPLICA_WP_Archive;
use GAMB_DUPLICA_WP_Log;
use GAMB_DUPLICA_WP_Package;
use GAMB_DUPLICA_WP_Package_File_Type;
use GambDuplicaWP\Ajax\AbstractAjaxService;
use GambDuplicaWP\Ajax\AjaxWrapper;
use GambDuplicaWP\Controllers\RecoveryController;
use GambDuplicaWP\Controllers\SettingsPageController;
use GambDuplicaWP\Core\Controllers\ControllersManager;
use GambDuplicaWP\Controllers\ToolsPageController;
use GambDuplicaWP\Core\CapMng;
use GambDuplicaWP\Core\Views\TplMng;
use GambDuplicaWP\Libs\Snap\SnapJson;
use GambDuplicaWP\Libs\Snap\SnapUtil;
use GambDuplicaWP\Package\Recovery\BackupPackage;
use GambDuplicaWP\Package\Recovery\RecoveryPackage;
use Exception;

class ServicesRecovery extends AbstractAjaxService
{
    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init()
    {
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_get_recovery_widget', 'getWidget');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_set_recovery', 'setRecovery');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_reset_recovery', 'resetRecovery');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_backup_redirect', 'restoreBackupRedirect');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_disaster_launcher_download', 'launcherDownload');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_get_recovery_box_content', 'recoveryBoxContent');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_restore_backup_prepare', 'restoreBackupPrepare');
    }

    /**
     * Get recovery widget detail elements
     *
     * @param string $fromPageTab from page/tab unique id
     *
     * @return bool[]
     */
    protected static function getRecoveryDetailsOptions($fromPageTab)
    {
        if ($fromPageTab == ControllersManager::getPageUniqueId(ControllersManager::TOOLS_SUBMENU_SLUG, ToolsPageController::L2_SLUG_RECOVERY)) {
            $detailsOptions = [
                'selector'   => true,
                'copyLink'   => true,
                'copyButton' => true,
                'launch'     => true,
                'download'   => true,
                'info'       => true,
            ];
        } elseif ($fromPageTab == ControllersManager::getPageUniqueId(ControllersManager::IMPORT_SUBMENU_SLUG)) {
            $detailsOptions = [
                'selector'   => true,
                'launch'     => false,
                'download'   => true,
                'copyLink'   => true,
                'copyButton' => true,
                'info'       => true,
            ];
        } else {
            $detailsOptions = [];
        }

        return $detailsOptions;
    }

    /**
     * Set recovery callback
     *
     * @return array<string, mixed>
     */
    public static function setRecoveryCallback()
    {
        $recPackageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'recovery_package', -1);
        GAMB_DUPLICA_WP_Log::trace("SET RECOVERY PACKAGE ID {$recPackageId}");
        if ($recPackageId !== RecoveryPackage::getRecoverPackageId()) {
            GAMB_DUPLICA_WP_Log::trace("RESET OLD RECORY PACKAGE ID " . RecoveryPackage::getRecoverPackageId());
            RecoveryPackage::removeRecoveryFolder();

            $errorMessage = '';
            if (!RecoveryPackage::setRecoveablePackage($recPackageId, $errorMessage)) {
                $urlImport = ControllersManager::getMenuLink(ControllersManager::SETTINGS_SUBMENU_SLUG, SettingsPageController::L2_SLUG_IMPORT);

                $msg  = sprintf(__("Error: <b>%s</b>", 'gamb-duplica-wp'), $errorMessage) . '<br><br>';
                $msg .= __("The old Recovery Point was removed but this Backup can’t be set as the Recovery Point.", 'gamb-duplica-wp') . '<br>';
                $msg .= __("Possible solutions:", 'gamb-duplica-wp') . '<br>';
                $msg .= sprintf(
                    _x(
                        '- In some hosting the execution of PHP scripts are blocked in the wp-content folder, %1$s[try set a custom recovery path]%2$s',
                        '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                        'gamb-duplica-wp'
                    ),
                    '<a href="' . esc_url($urlImport) . '" target="_blank">',
                    '</a>'
                ) . '<br>';
                $msg .= __(
                    "- you may still be able to to download the Backup manually and perform an import or a classic backup installation.
                    If you wish to install the Backup on the site where it was create the restore backup mode should be activated.",
                    'gamb-duplica-wp'
                );
                throw new Exception($msg);
            }
            GAMB_DUPLICA_WP_Log::trace("RECOVER PACKAGE SET");
        }

        $recoverPackage = RecoveryPackage::getRecoverPackage();
        GAMB_DUPLICA_WP_Log::trace("RECOVER PACKAGE READED");
        if (!$recoverPackage instanceof RecoveryPackage) {
            throw new Exception(esc_html__('Can\'t get recover Backup', 'gamb-duplica-wp'));
        }
        $fromPageTab    = SnapUtil::sanitizeDefaultInput(INPUT_POST, 'fromPageTab', false);
        $detailsOptions = self::getRecoveryDetailsOptions($fromPageTab);
        GAMB_DUPLICA_WP_Log::trace("RECOVER PACKAGE DETAILS OPTIONS READED");

        $subtitle = __('Copy the Link and keep it in case of need or download Disaster Recovery Launcher.', 'gamb-duplica-wp');
        return [
            'id'             => $recoverPackage->getPackageId(),
            'name'           => $recoverPackage->getPackageName(),
            'recoveryLink'   => $recoverPackage->getInstallLink(),
            'adminMessage'   => RecoveryController::renderRecoveryWidged([
                'selector'   => false,
                'subtitle'   => $subtitle,
                'copyLink'   => false,
                'copyButton' => true,
                'launch'     => false,
                'download'   => true,
                'info'       => true,
            ], false),
            'packageDetails' => RecoveryController::renderRecoveryWidged($detailsOptions, false),
        ];
    }

    /**
     * Set recovery action
     *
     * @return void
     */
    public function setRecovery()
    {
        AjaxWrapper::json(
            [
                self::class,
                'setRecoveryCallback',
            ],
            'gamb-duplica-wp_pro_set_recovery',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce', ''),
            CapMng::CAP_BACKUP_RESTORE
        );
    }

    /**
     * Get widget callback
     *
     * @return string[]
     */
    public static function getWidgetCallback()
    {
        $fromPageTab    = SnapUtil::sanitizeDefaultInput(INPUT_POST, 'fromPageTab', false);
        $detailsOptions = self::getRecoveryDetailsOptions($fromPageTab);

        return [
            'widget' => RecoveryController::renderRecoveryWidged($detailsOptions, false),
        ];
    }

    /**
     * Get widget action
     *
     * @return void
     */
    public function getWidget()
    {
        AjaxWrapper::json(
            [
                self::class,
                'getWidgetCallback',
            ],
            'gamb-duplica-wp_pro_get_recovery_widget',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce', ''),
            CapMng::CAP_BACKUP_RESTORE
        );
    }

    /**
     * Reset recovery callback
     *
     * @return string[]
     */
    public static function resetRecoveryCallback()
    {
        if (RecoveryController::actionResetRecoveryPoint() === false) {
            throw new Exception(RecoveryController::getErrorMessage());
        }

        $fromPageTab    = SnapUtil::sanitizeDefaultInput(INPUT_POST, 'fromPageTab', false);
        $detailsOptions = self::getRecoveryDetailsOptions($fromPageTab);

        return [
            'adminMessage'   => RecoveryController::renderRecoveryWidged([], false),
            'packageDetails' => RecoveryController::renderRecoveryWidged($detailsOptions, false),
        ];
    }

    /**
     * Reset recovery action
     *
     * @return void
     */
    public function resetRecovery()
    {
        AjaxWrapper::json(
            [
                self::class,
                'resetRecoveryCallback',
            ],
            'gamb-duplica-wp_pro_reset_recovery',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce', ''),
            CapMng::CAP_BACKUP_RESTORE
        );
    }

    /**
     * Prepare restore backup and redirect to the installer URL
     *
     * @return array<string,scalar>
     */
    public static function restoreBackupRedirectCallback(): array
    {
        $result = [
            'success'      => false,
            'message'      => '',
            'redirect_url' => '',
        ];

        try {
            $packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'packageId', 0);

            if (($package = GAMB_DUPLICA_WP_Package::get_by_id($packageId)) === false) {
                throw new Exception(__('Backup is invalid', 'gamb-duplica-wp'));
            }

            if (!$package->haveLocalStorage()) {
                throw new Exception(__('Backup isn\'t local', 'gamb-duplica-wp'));
            }

            $arachivePath = $package->getLocalPackageFilePath(GAMB_DUPLICA_WP_Package_File_Type::Archive);
            if (!file_exists($arachivePath)) {
                throw new Exception(__('Backup archive file doesn\'t exist', 'gamb-duplica-wp'));
            }

            $restore = new BackupPackage($arachivePath, $package);

            $result['redirect_url'] = $restore->prepareToInstall();
            $result['success']      = true;
        } catch (Exception $ex) {
            $result['success'] = false;
            $result['message'] = $ex->getMessage();
            GAMB_DUPLICA_WP_Log::traceError($ex->getMessage());
        }

        return $result;
    }

    /**
     * Reset recovery action
     *
     * @return void
     */
    public function restoreBackupRedirect()
    {
        AjaxWrapper::json(
            [
                self::class,
                'restoreBackupRedirectCallback',
            ],
            'gamb-duplica-wp_pro_backup_redirect',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce', ''),
            CapMng::CAP_BACKUP_RESTORE
        );
    }


    /**
     * Launcher download callback
     *
     * @return array<string,scalar>
     */
    public static function launcherDownloadCallback(): array
    {
        $result = [
            'success'     => false,
            'message'     => '',
            'fileContent' => '',
            'fileName'    => '',
        ];

        try {
            if (($recoverPackage = RecoveryPackage::getRecoverPackage()) == false) {
                throw new Exception(__('Can\'t get recover Backup', 'gamb-duplica-wp'));
            }

            $result['fileContent'] = TplMng::getInstance()->render(
                'parts/recovery/launcher_content',
                ['recoverPackage' => $recoverPackage],
                false
            );

            $result['fileName'] = $recoverPackage->getLauncherFileName();
            $result['success']  = true;
        } catch (Exception $ex) {
            $result['success'] = false;
            $result['message'] = $ex->getMessage();
            GAMB_DUPLICA_WP_Log::traceError($ex->getMessage());
        }

        return $result;
    }

    /**
     * Reset recovery action
     *
     * @return void
     */
    public function launcherDownload()
    {
        AjaxWrapper::json(
            [
                self::class,
                'launcherDownloadCallback',
            ],
            'gamb-duplica-wp_pro_disaster_launcher_download',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce', ''),
            CapMng::CAP_BACKUP_RESTORE
        );
    }


    /**
     * Prepare restore backup and redirect to the installer URL
     *
     * @return array<string,scalar>
     */
    public static function recoveryBoxContentCallback(): array
    {
        $result = [
            'success'      => false,
            'message'      => '',
            'content'      => '',
            'isRecoveable' => false,
        ];

        try {
            $packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'packageId', 0);

            if (($package = GAMB_DUPLICA_WP_Package::get_by_id($packageId)) === false) {
                throw new Exception(__('Backup is invalid', 'gamb-duplica-wp'));
            }

            $result['content']      = TplMng::getInstance()->render(
                'admin_pages/packages/recovery_info/row_recovery_box',
                ['package' => $package],
                false
            );
            $result['isRecoveable'] = RecoveryPackage::isPackageIdRecoveable($package->ID);
            $result['success']      = true;
        } catch (Exception $ex) {
            $result['success'] = false;
            $result['message'] = $ex->getMessage();
            GAMB_DUPLICA_WP_Log::traceError($ex->getMessage());
        }

        return $result;
    }

    /**
     * Reset recovery action
     *
     * @return void
     */
    public function recoveryBoxContent()
    {
        AjaxWrapper::json(
            [
                self::class,
                'recoveryBoxContentCallback',
            ],
            'gamb-duplica-wp_pro_get_recovery_box_content',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce', ''),
            CapMng::CAP_BACKUP_RESTORE
        );
    }

    /**
     * Restore backup prepare callback
     *
     * @return string
     */
    public static function restoreBackupPrepareCallback()
    {
        $packageId = filter_input(INPUT_POST, 'packageId', FILTER_VALIDATE_INT);
        if (!$packageId) {
            throw new Exception('Invalid Backup ID in request.');
        }
        $result = [];

        if (($package = GAMB_DUPLICA_WP_Package::get_by_id($packageId)) === false) {
            throw new Exception(esc_html__('Invalid Backup ID', 'gamb-duplica-wp'));
        }
        $updDirs = wp_upload_dir();

        $result = GambDuplicaWP_PRO_SSDIR_URL . '/' . $package->Installer->getInstallerName() . '?dup_folder=dupinst_' . $package->getHash();

        $installerParams = [
            'inst_mode'              => ['value' => 2 ], // mode restore backup
            'url_old'                => ['formStatus' => "st_skip"],
            'url_new'                => [
                'value'      => GAMB_DUPLICA_WP_Archive::getOriginalUrls('home'),
                'formStatus' => "st_infoonly",
            ],
            'path_old'               => ['formStatus' => "st_skip"],
            'path_new'               => [
                'value'      => GambDuplicaWP_pro_get_home_path(),
                'formStatus' => "st_infoonly",
            ],
            'dbaction'               => [
                'value'      => 'empty',
                'formStatus' => "st_infoonly",
            ],
            'dbhost'                 => [
                'value'      => DB_HOST,
                'formStatus' => "st_infoonly",
            ],
            'dbname'                 => [
                'value'      => DB_NAME,
                'formStatus' => "st_infoonly",
            ],
            'dbuser'                 => [
                'value'      => DB_USER,
                'formStatus' => "st_infoonly",
            ],
            'dbpass'                 => [
                'value'      => DB_PASSWORD,
                'formStatus' => "st_infoonly",
            ],
            'dbtest_ok'              => ['value' => true],
            'siteurl_old'            => ['formStatus' => "st_skip"],
            'siteurl'                => [
                'value'      => 'site_url',
                'formStatus' => "st_skip",
            ],
            'path_cont_old'          => ['formStatus' => "st_skip"],
            'path_cont_new'          => [
                'value'      => WP_CONTENT_DIR,
                'formStatus' => "st_skip",
            ],
            'path_upl_old'           => ['formStatus' => "st_skip"],
            'path_upl_new'           => [
                'value'      => $updDirs['basedir'],
                'formStatus' => "st_skip",
            ],
            'url_cont_old'           => ['formStatus' => "st_skip"],
            'url_cont_new'           => [
                'value'      => content_url(),
                'formStatus' => "st_skip",
            ],
            'url_upl_old'            => ['formStatus' => "st_skip"],
            'url_upl_new'            => [
                'value'      => $updDirs['baseurl'],
                'formStatus' => "st_skip",
            ],
            'exe_safe_mode'          => ['formStatus' => "st_skip"],
            'remove-redundant'       => ['formStatus' => "st_skip"],
            'blogname'               => ['formStatus' => "st_infoonly"],
            'replace_mode'           => ['formStatus' => "st_skip"],
            'empty_schedule_storage' => [
                'value'      => false,
                'formStatus' => "st_skip",
            ],
            'wp_config'              => [
                'value'      => 'original',
                'formStatus' => "st_infoonly",
            ],
            'ht_config'              => [
                'value'      => 'original',
                'formStatus' => "st_infoonly",
            ],
            'other_config'           => [
                'value'      => 'original',
                'formStatus' => "st_infoonly",
            ],
            'zip_filetime'           => [
                'value'      => 'original',
                'formStatus' => "st_infoonly",
            ],
            'mode_chunking'          => [
                'value'      => 3,
                'formStatus' => "st_infoonly",
            ],
        ];
        $localParamsFile = GambDuplicaWP_PRO_SSDIR_PATH . '/' . GambDuplicaWP_PRO_LOCAL_OVERWRITE_PARAMS . '_' . $package->getPackageHash() . '.json';
        file_put_contents($localParamsFile, SnapJson::jsonEncodePPrint($installerParams));

        return $result;
    }

    /**
     * Hook ajax restore backup prepare
     *
     * @return void
     */
    public function restoreBackupPrepare()
    {
        AjaxWrapper::json(
            [
                self::class,
                'restoreBackupPrepareCallback',
            ],
            'gamb-duplica-wp_pro_restore_backup_prepare',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BACKUP_RESTORE
        );
    }
}
