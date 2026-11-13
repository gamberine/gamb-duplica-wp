<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Ajax;

use gamb-duplica-wp\Addons\ProBase\License\License;
use gamb-duplica-wp\Ajax\AjaxWrapper;
use gamb-duplica-wp\Ajax\FileTransfer\ImportUpload;
use gamb-duplica-wp\Controllers\ImportPageController;
use gamb-duplica-wp\Core\CapMng;
use gamb-duplica-wp\Libs\Snap\SnapUtil;
use gamb-duplica-wp\Package\Import\PackageImporter;
use Exception;

class ServicesImport extends AbstractAjaxService
{
    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init()
    {
        if (!License::can(License::CAPABILITY_PRO_BASE)) {
            return;
        }
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_import_upload', 'importUpload');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_import_package_delete', 'deletePackage');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_import_set_view_mode', 'setViewMode');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_import_remote_download', 'remoteDownload');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_import_set_archive_password', 'setArchivePassword');
    }

    /**
     * Import upload callback logic
     *
     * @return mixed[]
     */
    public static function importUploadCallback()
    {
        $uploader = new ImportUpload(ImportUpload::MODE_UPLOAD_LOCAL);
        return $uploader->exec();
    }

    /**
     * Import upload action
     *
     * @return void
     */
    public function importUpload()
    {
        AjaxWrapper::json(
            [
                self::class,
                'importUploadCallback',
            ],
            'gamb-duplica-wp_pro_import_upload',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_IMPORT
        );
    }

    /**
     * Import download remote callback logic
     *
     * @return mixed[]
     */
    public static function remoteDownloadCallback()
    {
        $uploader = new ImportUpload(ImportUpload::MODE_DOWNLOAD_REMOTE);
        return $uploader->exec();
    }

    /**
     * Import download remote action
     *
     * @return void
     */
    public function remoteDownload()
    {
        AjaxWrapper::json(
            [
                self::class,
                'remoteDownloadCallback',
            ],
            'gamb-duplica-wp_pro_remote_download',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_IMPORT
        );
    }

    /**
     * Import delete Backup callback
     *
     * @return bool
     */
    public static function deletePackageCallback(): bool
    {
        $inputData = filter_input_array(INPUT_POST, [
            'path' => [
                'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => ''],
            ],
        ]);

        if (empty($inputData['path'])) {
            throw new Exception(__("Invalid Request!", 'gamb-duplica-wp'));
        }

        if (in_array($inputData['path'], PackageImporter::getArchiveList())) {
            if (unlink($inputData['path']) == false) {
                throw new Exception(__("Can\'t remove archive!", 'gamb-duplica-wp'));
            }
            PackageImporter::cleanFolder();
        }

        return true;
    }

    /**
     * Import delete backage action
     *
     * @return void
     */
    public function deletePackage()
    {
        AjaxWrapper::json(
            [
                self::class,
                'deletePackageCallback',
            ],
            'gamb-duplica-wp_pro_import_package_delete',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_IMPORT
        );
    }

    /**
     * Set import view mode callback
     *
     * @return string
     */
    public static function setViewModeCallback()
    {
        $viewMode = filter_input(INPUT_POST, 'view_mode', FILTER_SANITIZE_SPECIAL_CHARS);

        switch ($viewMode) {
            case ImportPageController::VIEW_MODE_ADVANCED:
            case ImportPageController::VIEW_MODE_BASIC:
                break;
            default:
                throw new Exception(__('Invalid view mode', 'gamb-duplica-wp'));
        }

        if (!($userId = get_current_user_id())) {
            throw new Exception(__('Invalid current user id', 'gamb-duplica-wp'));
        }

        $archives = PackageImporter::getArchiveList();
        if ($viewMode == ImportPageController::VIEW_MODE_BASIC && count($archives) > 1) {
            update_user_meta($userId, ImportPageController::USER_META_VIEW_MODE, ImportPageController::VIEW_MODE_ADVANCED);
            $message = __(
                'It is not possible to set the view mode to basic if the number of Backups is more than one.',
                'gamb-duplica-wp'
            ) . ' ' .
            __('Remove Backups before performing this action.', 'gamb-duplica-wp');
            throw new Exception($message);
        }

        if ($viewMode != ImportPageController::getViewMode()) {
            if (update_user_meta($userId, ImportPageController::USER_META_VIEW_MODE, $viewMode) == false) {
                throw new Exception(__('Can\'t update user meta value', 'gamb-duplica-wp'));
            }
        }

        return ImportPageController::getViewMode();
    }

    /**
     * Set import view mode action
     *
     * @return void
     */
    public function setViewMode()
    {
        AjaxWrapper::json(
            [
                self::class,
                'setViewModeCallback',
            ],
            'gamb-duplica-wp_pro_import_set_view_mode',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_IMPORT
        );
    }

    /**
     * Set import view mode callback
     *
     * @return mixed[]
     */
    public static function setArchivePasswordCallback()
    {
        $result = [];

        $archiveFile = filter_input(INPUT_POST, 'archive', FILTER_CALLBACK, ['options' => [SnapUtil::class, 'sanitizeNSCharsNewlineTabs']]);
        $password    = filter_input(INPUT_POST, 'password', FILTER_CALLBACK, ['options' => [SnapUtil::class, 'sanitizeNSCharsNewlineTrim']]);

        if (preg_match('/^.*\.(zip|daf)$/', $archiveFile) !== 1) {
            throw new Exception('Invalid archive name "' . $archiveFile . '"');
        }

        $importObj = new PackageImporter($archiveFile);
        $errMsg    = '';
        if (!$importObj->encryptCheck($errMs)) {
            throw new Exception($errMsg);
        } elseif ($importObj->passwordCheck($password)) {
            $importObj->updatePasswordCookie();
            $uploader = new ImportUpload(ImportUpload::MODE_UPLOADED, $archiveFile);
            $result   = $uploader->exec();
        } else {
            throw new Exception('Invalid password');
        }

        return $result;
    }

    /**
     * Set import view mode action
     *
     * @return void
     */
    public function setArchivePassword()
    {
        AjaxWrapper::json(
            [
                self::class,
                'setArchivePasswordCallback',
            ],
            'gamb-duplica-wp_pro_import_set_archive_password',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_IMPORT
        );
    }
}
