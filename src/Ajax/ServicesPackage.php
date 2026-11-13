<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Ajax;

use GAMB_DUPLICA_WP_Archive;
use GAMB_DUPLICA_WP_Archive_Build_Mode;
use GAMB_DUPLICA_WP_DATE;
use GAMB_DUPLICA_WP_Handler;
use GAMB_DUPLICA_WP_Log;
use GAMB_DUPLICA_WP_Package;
use GAMB_DUPLICA_WP_Package_File_Type;
use GAMB_DUPLICA_WP_Package_Runner;
use GAMB_DUPLICA_WP_Package_Template_Entity;
use GAMB_DUPLICA_WP_Package_Upload_Info;
use GAMB_DUPLICA_WP_PackageStatus;
use GAMB_DUPLICA_WP_Tree_files;
use GAMB_DUPLICA_WP_U;
use GAMB_DUPLICA_WP_Upload_Status;
use GAMB_DUPLICA_WP_ZipArchive_Mode;
use gamb-duplica-wp\Addons\ProBase\License\License;
use gamb-duplica-wp\Core\CapMng;
use gamb-duplica-wp\Core\Views\TplMng;
use gamb-duplica-wp\Libs\Snap\SnapUtil;
use gamb-duplica-wp\Models\Storages\AbstractStorageEntity;
use gamb-duplica-wp\Models\Storages\StoragesUtil;
use gamb-duplica-wp\Utils\LockUtil;
use Error;
use Exception;
use stdClass;

class ServicesPackage extends AbstractAjaxService
{
    const EXEC_STATUS_PASS = 1;
    /**
     * @deprecated Never used
     */
    const EXEC_STATUS_WARN = 2;

    const EXEC_STATUS_FAIL             = 3;
    const EXEC_STATUS_MORE_TO_SCAN     = 4;
    const EXEC_STATUS_SCHEDULE_RUNNING = 5;

    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init()
    {
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_process_worker', 'processWorker');
        $this->addAjaxCall('wp_ajax_nopriv_gamb-duplica-wp_pro_process_worker', 'processWorker');

        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_download_package_file', 'downloadPackageFile');
        $this->addAjaxCall('wp_ajax_nopriv_gamb-duplica-wp_pro_download_package_file', 'downloadPackageFile');

        if (!License::can(License::CAPABILITY_PRO_BASE)) {
            return;
        }
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_add_quick_filters', 'addQuickFilters');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_package_scan', 'packageScan');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_package_delete', 'packageDelete');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_reset_packages', 'resetPackages');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_get_package_statii', 'packageStatii');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_package_stop_build', 'stopBuild');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_manual_transfer_storage', 'manualTransferStorage');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_packages_details_transfer_get_package_vm', 'detailsTransferGetPackageVM');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_get_folder_children', 'getFolderChildren');
        $this->addAjaxCall("wp_ajax_gamb-duplica-wp_get_remote_restore_download_options", "remoteRestoreDownloadOptions");
    }

    /**
     * Removed all reserved installer files names
     *
     * @return never
     */
    public function addQuickFilters()
    {
        GAMB_DUPLICA_WP_Handler::init_error_handler();
        check_ajax_referer('gamb-duplica-wp_add_quick_filters', 'nonce');
        $inputData = filter_input_array(INPUT_POST, [
            'dir_paths'  => [
                'filter'  => FILTER_DEFAULT,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => ''],
            ],
            'file_paths' => [
                'filter'  => FILTER_DEFAULT,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => ''],
            ],
        ]);
        $result    = [
            'success'      => false,
            'message'      => '',
            'filter-dirs'  => '',
            'filter-files' => '',
            'filter-names' => '',
        ];
        try {
            // CONTROLLER LOGIC
            // Need to update both the template and the temporary Backup because:
            // 1) We need to preserve preferences of this build for future manual builds - the manual template is used for this.
            // 2) Temporary Backup is used during this build - keeps all the settings/storage information.
            // Will be inserted into the Backup table after they ok the scan results.
            $template  = GAMB_DUPLICA_WP_Package_Template_Entity::get_manual_template();
            $dirPaths  = GAMB_DUPLICA_WP_Archive::parseDirectoryFilter(SnapUtil::sanitizeNSChars($inputData['dir_paths']));
            $filePaths = GAMB_DUPLICA_WP_Archive::parseFileFilter(SnapUtil::sanitizeNSChars($inputData['file_paths']));

            // If we are adding a new filter & we have filters disabled, clear out the old filters.
            if (!$template->archive_filter_on && (strlen($dirPaths) > 0 || strlen($filePaths) > 0)) {
                $template->archive_filter_dirs  = '';
                $template->archive_filter_files = '';
            }

            if (strlen($dirPaths) > 0) {
                $template->archive_filter_dirs .= strlen($template->archive_filter_dirs) > 0 ? ';' . $dirPaths : $dirPaths;
            }

            if (strlen($filePaths) > 0) {
                $template->archive_filter_files .= strlen($template->archive_filter_files) > 0 ? ';' . $filePaths : $filePaths;
            }

            if (!$template->archive_filter_on) {
                $template->archive_filter_exts = '';
            }

            $template->archive_filter_on    = 1;
            $template->archive_filter_names = true;
            $template->save();

            $temporary_package                       = GAMB_DUPLICA_WP_Package::get_temporary_package();
            $temporary_package->Status               = GAMB_DUPLICA_WP_PackageStatus::PRE_PROCESS;
            $temporary_package->Archive->FilterDirs  = $template->archive_filter_dirs;
            $temporary_package->Archive->FilterFiles = $template->archive_filter_files;
            $temporary_package->Archive->FilterOn    = true;
            $temporary_package->Archive->FilterNames = $template->archive_filter_names;
            $temporary_package->set_temporary_package();

            $result['success']      = true;
            $result['filter-dirs']  = $temporary_package->Archive->FilterDirs;
            $result['filter-files'] = $temporary_package->Archive->FilterFiles;
            $result['filter-names'] = $temporary_package->Archive->FilterNames;
        } catch (Exception $exc) {
            $result['success'] = false;
            $result['message'] = $exc->getMessage();
        }

        wp_send_json($result);
    }

    /**
     *  gamb-duplica-wp_PRO_PACKAGE_SCAN
     *
     *  @example to test: /wp-admin/admin-ajax.php?action=gamb-duplica-wp_pro_package_scan
     *
     *  @return void
     */
    public function packageScan()
    {
        AjaxWrapper::json(
            [
                self::class,
                'packageScanCallback',
            ],
            'gamb-duplica-wp_pro_package_scan',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     *  gamb-duplica-wp_PRO_PACKAGE_SCAN
     *
     *  @example to test: /wp-admin/admin-ajax.php?action=gamb-duplica-wp_pro_package_scan
     *
     *  @return array<string, mixed>
     */
    public static function packageScanCallback()
    {
        GAMB_DUPLICA_WP_Handler::init_error_handler();
        try {
            // Keep the locking file opening and closing just to avoid adding even more complexity
            if (!LockUtil::lockProcess()) {
                // File is already locked indicating schedule is running
                GAMB_DUPLICA_WP_Log::trace("Already locked when attempting manual build - schedule running");

                return ['Status' => self::EXEC_STATUS_SCHEDULE_RUNNING];
            }

            @set_time_limit(0);
            StoragesUtil::getDefaultStorage()->initStorageDirectory(true);

            $report      = [];
            $package     = GAMB_DUPLICA_WP_Package::get_temporary_package();
            $package->ID = null;

            $firstChunk = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'firstChunk', false);
            GAMB_DUPLICA_WP_Log::trace('First Chunk: ' . ($firstChunk ? 'true' : 'false'));
            if ($firstChunk) {
                GAMB_DUPLICA_WP_Log::trace('First Scan Chunk');
                $fileScanDone = $package->Archive->scanFiles(true);
                GAMB_DUPLICA_WP_Package::set_temporary_package_member('Status', GAMB_DUPLICA_WP_PackageStatus::SCANNING);
            } else {
                GAMB_DUPLICA_WP_Log::trace('Continuing Scan');
                $fileScanDone = $package->Archive->scanFiles();
            }

            $package->set_temporary_package();

            if ($fileScanDone) {
                $report           = $package->createScanReport();
                $report['Status'] = self::EXEC_STATUS_PASS;

                $package->set_temporary_package();

                GAMB_DUPLICA_WP_Package::set_temporary_package_member('ScanFile', $package->ScanFile);
                GAMB_DUPLICA_WP_Package::tmp_cleanup();
                GAMB_DUPLICA_WP_Package::set_temporary_package_member('Status', GAMB_DUPLICA_WP_PackageStatus::AFTER_SCAN);
            } else {
                GAMB_DUPLICA_WP_Log::trace('Scan not done yet');
                $report = ['Status' => self::EXEC_STATUS_MORE_TO_SCAN];
            }

            LockUtil::unlockProcess();
        } catch (Exception $ex) {
            return [
                'Status'  =>  self::EXEC_STATUS_FAIL,
                'Message' => sprintf(__("Exception occurred. Exception message: %s", 'gamb-duplica-wp'), $ex->getMessage()),
                'File'    => $ex->getFile(),
                'Line'    => $ex->getLine(),
                'Trace'   => $ex->getTrace(),
            ];
        } catch (Error $ex) {
            return [
                'Status'  =>  self::EXEC_STATUS_FAIL,
                'Message' =>  sprintf(
                    esc_html__("Fatal Error occurred. Error message: %1\$s<br>\nTrace: %2\$s", 'gamb-duplica-wp'),
                    $ex->getMessage(),
                    $ex->getTraceAsString()
                ),
                'File'    => $ex->getFile(),
                'Line'    => $ex->getLine(),
                'Trace'   => $ex->getTrace(),
            ];
        }

        return $report;
    }

    /**
     * Hook ajax wp_ajax_gamb-duplica-wp_pro_package_delete
     * Deletes the files and database record entries
     *
     * @return void
     */
    public function packageDelete()
    {
        AjaxWrapper::json(
            [
                self::class,
                'packageDeleteCallback',
            ],
            'gamb-duplica-wp_pro_package_delete',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Hook ajax wp_ajax_gamb-duplica-wp_pro_package_delete
     * Deletes the files and database record entries
     *
     * @return array<string, mixed>
     */
    public static function packageDeleteCallback()
    {
        $deletedCount = 0;

        $inputData     = filter_input_array(INPUT_POST, [
            'package_ids' => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => ['default' => false],
            ],
        ]);
        $packageIDList = $inputData['package_ids'];

        if (empty($packageIDList) || in_array(false, $packageIDList)) {
            throw new Exception(__("Invalid request.", 'gamb-duplica-wp'));
        }

        GAMB_DUPLICA_WP_Log::traceObject("Starting deletion of Backups by ids: ", $packageIDList);
        foreach ($packageIDList as $id) {
            if ($package = GAMB_DUPLICA_WP_Package::get_by_id($id)) {
                if ($package->delete()) {
                    $deletedCount++;
                }
            } else {
                throw new Exception("Invalid Backup ID.");
            }
        }

        return [
            'ids'     => $packageIDList,
            'removed' => $deletedCount,
        ];
    }

    /**
     * Hook ajax wp_ajax_gamb-duplica-wp_pro_reset_packages
     *
     * @return never
     */
    public function resetPackages()
    {
        ob_start();
        try {
            GAMB_DUPLICA_WP_Handler::init_error_handler();

            $error  = false;
            $result = [
                'data'    => ['status' => null],
                'html'    => '',
                'message' => '',
            ];

            $nonce = SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce');
            if (!wp_verify_nonce($nonce, 'gamb-duplica-wp_pro_reset_packages')) {
                GAMB_DUPLICA_WP_Log::trace('Security issue');
                throw new Exception('Security issue');
            }
            CapMng::can(CapMng::CAP_SETTINGS);

            // first last Backup id
            $ids = GAMB_DUPLICA_WP_Package::get_ids_by_status(
                [
                    [
                        'op'     => '<',
                        'status' => GAMB_DUPLICA_WP_PackageStatus::COMPLETE,
                    ],
                ],
                0,
                0,
                '`id` DESC'
            );
            foreach ($ids as $id) {
                // A smooth deletion is not performed because it is a forced reset.
                GAMB_DUPLICA_WP_Package::force_delete($id);
            }
        } catch (Exception $e) {
            $error             = true;
            $result['message'] = $e->getMessage();
        }

        $result['html'] = ob_get_clean();
        if ($error) {
            wp_send_json_error($result);
        } else {
            wp_send_json_success($result);
        }
    }

    /**
     * Hook ajax wp_ajax_gamb-duplica-wp_pro_get_package_statii
     *
     * @return void
     */
    public function packageStatii()
    {
        AjaxWrapper::json(
            [
                self::class,
                'packageStatiiCallback',
            ],
            'gamb-duplica-wp_pro_get_package_statii',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * Hook ajax wp_ajax_gamb-duplica-wp_pro_get_package_statii
     *
     * @return array<mixed>
     */
    public static function packageStatiiCallback(): array
    {
        $limit     = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'limit', 0);
        $offset    = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'offset', 0);
        $packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'packageId', -1);

        if ($packageId > 0) {
            if (($package = GAMB_DUPLICA_WP_Package::get_by_id($packageId)) === false) {
                throw new Exception(__('Couldn\'t get Backup.', 'gamb-duplica-wp'));
            }

            return [self::getPackageStatusInfo($package)];
        }

        $resultData = [];
        GAMB_DUPLICA_WP_Package::by_status_callback(
            function (GAMB_DUPLICA_WP_Package $package) use (&$resultData): void {
                $resultData[] = self::getPackageStatusInfo($package);
            },
            [],
            $limit,
            $offset,
            '`id` DESC'
        );

        return $resultData;
    }

    /**
     * Returns the Backup status info
     *
     * @param GAMB_DUPLICA_WP_Package $package The Backup
     *
     * @return array<string, mixed> The status data
     */
    protected static function getPackageStatusInfo(GAMB_DUPLICA_WP_Package $package): array
    {
        $status                         = [];
        $status['ID']                   = $package->ID;
        $status['status']               = self::getAdjustedPackageStatus($package);
        $status['status_progress']      = $package->get_status_progress();
        $status['size']                 = $package->get_display_size();
        $status['status_progress_text'] = '';

        if ($status['status'] < GAMB_DUPLICA_WP_PackageStatus::COMPLETE) {
            $active_storage = $package->get_active_storage();
            if ($active_storage !== false) {
                $isDownload                     = $package->isDownloadInProgress();
                $status['status_progress_text'] = $active_storage->getActionText($isDownload);
            } else {
                $status['status_progress_text'] = '';
            }
        }

        return $status;
    }

    /**
     * Get the Backup status
     *
     * @param GAMB_DUPLICA_WP_Package $package The Backup to get the status for
     *
     * @return int|float
     */
    private static function getAdjustedPackageStatus(GAMB_DUPLICA_WP_Package $package)
    {
        if ($package->is_cancel_pending()) {
            return GAMB_DUPLICA_WP_PackageStatus::PENDING_CANCEL;
        }

        $estimated_progress = ($package->build_progress->current_build_mode == GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec) ||
            ($package->ziparchive_mode == GAMB_DUPLICA_WP_ZipArchive_Mode::SingleThread);

        if (($package->Status == GAMB_DUPLICA_WP_PackageStatus::ARCSTART) && $estimated_progress) {
            // Amount of time passing before we give them a 1%
            $time_per_percent       = 11;
            $thread_age             = time() - $package->build_progress->thread_start_time;
            $total_percentage_delta = GAMB_DUPLICA_WP_PackageStatus::ARCDONE - GAMB_DUPLICA_WP_PackageStatus::ARCSTART;

            if ($thread_age > ($total_percentage_delta * $time_per_percent)) {
                // It's maxed out so just give them the done condition for the rest of the time
                return GAMB_DUPLICA_WP_PackageStatus::ARCDONE;
            } else {
                $percentage_delta = (int) ($thread_age / $time_per_percent);

                return GAMB_DUPLICA_WP_PackageStatus::ARCSTART + $percentage_delta;
            }
        } else {
            return $package->Status;
        }
    }

    /**
     * Hook ajax wp_ajax_gamb-duplica-wp_pro_package_stop_build
     *
     * @return void
     */
    public function stopBuild()
    {
        AjaxWrapper::json(
            [
                self::class,
                'stopBuildCallback',
            ],
            'gamb-duplica-wp_pro_package_stop_build',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Stop build callback
     *
     * @return array<string, mixed>
     */
    public static function stopBuildCallback(): array
    {
        $inputData = filter_input_array(INPUT_POST, [
            'package_id'  => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
            'stop_active' => [
                'filter'  => FILTER_VALIDATE_BOOLEAN,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
        ]);

        $packageId  = $inputData['package_id'];
        $stopActive = $inputData['stop_active'];

        try {
            $package = null;
            if ($stopActive) {
                GAMB_DUPLICA_WP_Log::trace("Web service stop build of $packageId");
                $package = GAMB_DUPLICA_WP_Package::get_next_active_package();
            } elseif ($packageId != false) {
                GAMB_DUPLICA_WP_Log::trace("Web service stop build of $packageId");
                $package = GAMB_DUPLICA_WP_Package::get_by_id($packageId);
            }

            if ($package == null && $stopActive !== true) {
                GAMB_DUPLICA_WP_Log::trace(
                    "Could not find Backup so attempting hard delete. 
                    Old files may end up sticking around although chances are there isnt much if we couldnt nicely cancel it."
                );
                $result = GAMB_DUPLICA_WP_Package::force_delete($packageId);

                if (!$result) {
                    throw new Exception('Hard delete failure');
                }

                return [
                    'success' => true,
                    'message' => 'Hard delete success',
                ];
            } else {
                GAMB_DUPLICA_WP_Log::trace("set $package->ID for cancel");
                $package->set_for_cancel();
            }
        } catch (Exception $ex) {
            GAMB_DUPLICA_WP_Log::trace($ex->getMessage());
            throw $ex;
        }

        return ['success' => true];
    }

    /**
     * Hook ajax process worker
     *
     * @return never
     */
    public function processWorker()
    {
        GAMB_DUPLICA_WP_Handler::init_error_handler();
        GAMB_DUPLICA_WP_U::checkAjax();
        header("HTTP/1.1 200 OK");

        GAMB_DUPLICA_WP_Log::trace("Process worker request");
        GAMB_DUPLICA_WP_Package_Runner::process();
        GAMB_DUPLICA_WP_Log::trace("Exiting process worker request");

        echo 'ok';
        exit();
    }

    /**
     * Hook ajax wp_ajax_gamb-duplica-wp_pro_download_package_file
     *
     * @return never
     */
    public function downloadPackageFile()
    {
        GAMB_DUPLICA_WP_Handler::init_error_handler();
        $inputData = filter_input_array(INPUT_GET, [
            'fileType' => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
            'hash'     => [
                'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
            'token'    => [
                'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
        ]);

        try {
            if (
                $inputData['token'] === false ||
                $inputData['hash'] === false ||
                $inputData["fileType"] === false ||
                GAMB_DUPLICA_WP_Package::getLocalPackageAjaxDownloadToken($inputData['hash']) !== $inputData['token'] ||
                ($package = GAMB_DUPLICA_WP_Package::get_by_hash($inputData['hash'])) == false
            ) {
                throw new Exception(__("Invalid request.", 'gamb-duplica-wp'));
            }

            switch ($inputData['fileType']) {
                case GAMB_DUPLICA_WP_Package_File_Type::Installer:
                    $filePath = $package->getLocalPackageFilePath(GAMB_DUPLICA_WP_Package_File_Type::Installer);
                    $fileName = $package->Installer->getDownloadName();
                    break;
                case GAMB_DUPLICA_WP_Package_File_Type::Archive:
                    $filePath = $package->getLocalPackageFilePath(GAMB_DUPLICA_WP_Package_File_Type::Archive);
                    $fileName = basename($filePath);
                    break;
                case GAMB_DUPLICA_WP_Package_File_Type::Log:
                    $filePath = $package->getLocalPackageFilePath(GAMB_DUPLICA_WP_Package_File_Type::Log);
                    $fileName = basename($filePath);
                    break;
                default:
                    throw new Exception(__("File type not supported.", 'gamb-duplica-wp'));
            }

            if ($filePath == false) {
                throw new Exception(__("File don\'t exists", 'gamb-duplica-wp'));
            }

            \gamb-duplica-wp\Libs\Snap\SnapIO::serveFileForDownload($filePath, $fileName, gamb-duplica-wp_PRO_BUFFER_DOWNLOAD_SIZE);
        } catch (Exception $ex) {
            GAMB_DUPLICA_WP_Log::trace('Unable to download Backup file: ' . $ex->getMessage());
            wp_die(esc_html($ex->getMessage()));
        }
    }

    /**
     * Hook ajax transfer data
     *
     * @return void
     */
    public function detailsTransferGetPackageVM()
    {
        AjaxWrapper::json(
            [
                self::class,
                'detailsTransferGetPackageVMCallback',
            ],
            'gamb-duplica-wp_pro_packages_details_transfer_get_package_vm',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Hook ajax handler for packages_details_transfer_get_package_vm
     * Retrieve view model for the Packages/Details/Transfer screen
     * active_package_id: true/false
     * percent_text: Percent through the current transfer
     * text: Text to display
     * transfer_logs: array of transfer request vms (start, stop, status, message)
     *
     * @return array<string, mixed>
     */
    public static function detailsTransferGetPackageVMCallback()
    {
        $inputData = filter_input_array(INPUT_POST, [
            'package_id' => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
        ]);

        $package_id = $inputData['package_id'];
        if (!$package_id) {
            throw new Exception(__("Invalid request.", 'gamb-duplica-wp'));
        }

        if (!CapMng::can(CapMng::CAP_STORAGE, false)) {
            throw new Exception('Security issue.');
        }

        $package = GAMB_DUPLICA_WP_Package::get_by_id($package_id);
        if (!$package) {
            $msg = sprintf(__('Could not get Backup by ID %s', 'gamb-duplica-wp'), $package_id);
            throw new Exception($msg);
        }

        $vm = new stdClass();

        /* -- First populate the transfer log information -- */

        // If this is the Backup being requested include the transfer details
        $vm->transfer_logs = [];

        $active_upload_info = null;

        $storages = AbstractStorageEntity::getAll();

        foreach ($package->upload_infos as &$upload_info) {
            if ($upload_info->getStorageId() === StoragesUtil::getDefaultStorageId()) {
                continue;
            }

            $status      = $upload_info->get_status();
            $status_text = $upload_info->get_status_text();

            $transfer_log = new stdClass();

            if ($upload_info->get_started_timestamp() == null) {
                $transfer_log->started = __('N/A', 'gamb-duplica-wp');
            } else {
                $transfer_log->started = GAMB_DUPLICA_WP_DATE::getLocalTimeFromGMTTicks($upload_info->get_started_timestamp());
            }

            if ($upload_info->get_stopped_timestamp() == null) {
                $transfer_log->stopped = __('N/A', 'gamb-duplica-wp');
            } else {
                $transfer_log->stopped = GAMB_DUPLICA_WP_DATE::getLocalTimeFromGMTTicks($upload_info->get_stopped_timestamp());
            }

            $transfer_log->status_text = $status_text;
            $transfer_log->message     = $upload_info->get_status_message();

            $transfer_log->storage_type_text = __('Unknown', 'gamb-duplica-wp');
            foreach ($storages as $storage) {
                if ($storage->getId() == $upload_info->getStorageId()) {
                    $transfer_log->storage_type_text = $storage->getStypeName();
                    // break;
                }
            }

            array_unshift($vm->transfer_logs, $transfer_log);

            if ($status == GAMB_DUPLICA_WP_Upload_Status::Running) {
                if ($active_upload_info != null) {
                    GAMB_DUPLICA_WP_Log::trace("More than one upload info is running at the same time for Backup {$package->ID}");
                }

                $active_upload_info = &$upload_info;
            }
        }

        /* -- Now populate the activa Backup information -- */
        $active_package = GAMB_DUPLICA_WP_Package::get_next_active_package();

        if ($active_package == null) {
            // No active Backup
            $vm->active_package_id = -1;
            $vm->text              = __('No Backup is building.', 'gamb-duplica-wp');
        } else {
            $vm->active_package_id = $active_package->ID;

            if ($active_package->ID == $package_id) {
                if ($active_upload_info != null) {
                    $vm->percent_text = "{$active_upload_info->progress}%";
                    $vm->text         = $active_upload_info->get_status_message();
                } else {
                    // We see this condition at the beginning and end of the transfer so throw up a generic message
                    $vm->percent_text = "";
                    $vm->text         = __("Synchronizing with server...", 'gamb-duplica-wp');
                }
            } else {
                $vm->text = __("Another Backup is presently running.", 'gamb-duplica-wp');
            }

            if ($active_package->is_cancel_pending()) {
                // If it's getting cancelled override the normal text
                $vm->text = __("Cancellation pending...", 'gamb-duplica-wp');
            }
        }

        return [
            'success' => true,
            'vm'      => $vm,
        ];
    }

    /**
     * Hook ajax manual transfer storage
     *
     * @return void
     */
    public function manualTransferStorage()
    {
        AjaxWrapper::json(
            [
                self::class,
                'manualTransferStorageCallback',
            ],
            'gamb-duplica-wp_pro_manual_transfer_storage',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Manual transfer storage callback
     *
     * @return array<string, mixed>
     */
    public static function manualTransferStorageCallback()
    {
        $isValid   = true;
        $inputData = filter_input_array(INPUT_POST, [
            'package_id'  => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
            'storage_ids' => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => ['default' => false],
            ],
            'download'    => [
                'filter'  => FILTER_VALIDATE_BOOLEAN,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
        ]);

        $package_id  = $inputData['package_id'];
        $storage_ids = $inputData['storage_ids'];
        $isDownload  = $inputData['download'];
        $isValid     = $package_id !== false && $storage_ids !== false;

        try {
            if (!$isValid) {
                throw new Exception(__("Invalid request.", 'gamb-duplica-wp'));
            }

            GAMB_DUPLICA_WP_Log::trace("Test if Backup is Running");
            if (GAMB_DUPLICA_WP_Package::isPackageRunning()) {
                GAMB_DUPLICA_WP_Log::trace("Package running.");
                $msg = sprintf(__('Trying to queue a transfer for Backup %d but a Backup is already active!', 'gamb-duplica-wp'), $package_id);
                throw new Exception($msg);
            } else {
                GAMB_DUPLICA_WP_Log::trace("Package not running.");
            }

            $package = GAMB_DUPLICA_WP_Package::get_by_id($package_id);
            GAMB_DUPLICA_WP_Log::open($package->getNameHash());

            if (!$package) {
                throw new Exception(sprintf(esc_html__('Could not find Backup ID %d!', 'gamb-duplica-wp'), $package_id));
            }

            if (empty($storage_ids)) {
                throw new Exception("Please select a storage.");
            }

            $info  = "\n";
            $info .= "********************************************************************************\n";
            $info .= "********************************************************************************\n";
            $info .= "PACKAGE MANUAL TRANSFER REQUESTED: " . @date("Y-m-d H:i:s") . "\n";
            $info .= "********************************************************************************\n";
            $info .= "********************************************************************************\n\n";
            GAMB_DUPLICA_WP_Log::infoTrace($info);

            foreach ($storage_ids as $storage_id) {
                if (($storage = AbstractStorageEntity::getById($storage_id)) === false) {
                    throw new Exception(sprintf(__('Could not find storage ID %d!', 'gamb-duplica-wp'), $storage_id));
                }

                GAMB_DUPLICA_WP_Log::infoTrace(
                    'Storage adding to the Backup "' . $package->getName() .
                    ' [Package Id: ' . $package_id . ']":: Storage Id: "' . $storage_id .
                    '" Storage Name: "' . esc_html($storage->getName()) .
                    '" Storage Type: "' . esc_html($storage->getStypeName()) . '"'
                );

                $upload_info = new GAMB_DUPLICA_WP_Package_Upload_Info($storage_id);
                $upload_info->setDownloadFromRemote($isDownload);
                array_push($package->upload_infos, $upload_info);
            }

            $package->set_status(GAMB_DUPLICA_WP_PackageStatus::STORAGE_PROCESSING);
            $package->timer_start = GAMB_DUPLICA_WP_U::getMicrotime();

            $package->update();
        } catch (Exception $ex) {
            GAMB_DUPLICA_WP_Log::trace($ex->getMessage());
            throw $ex;
        }

        return ['success' => true];
    }

    /**
     * Hook ajax wp_ajax_gamb-duplica-wp_pro_get_folder_children
     *
     * @return never
     */
    public function getFolderChildren()
    {
        GAMB_DUPLICA_WP_Handler::init_error_handler();
        check_ajax_referer('gamb-duplica-wp_pro_get_folder_children', 'nonce');

        $json      = [];
        $isValid   = true;
        $inputData = filter_input_array(INPUT_GET, [
            'folder'  => [
                'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => ['default' => false],
            ],
            'exclude' => [
                'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => [
                    'default' => [],
                ],
            ],
        ]);
        $folder    = $inputData['folder'];
        $exclude   = $inputData['exclude'];

        if ($folder === false) {
            $isValid = false;
        }

        ob_start();
        try {
            CapMng::can(CapMng::CAP_BASIC);

            if (!$isValid) {
                throw new Exception(__('Invalid request.', 'gamb-duplica-wp'));
            }
            if (is_dir($folder)) {
                try {
                    $Package = GAMB_DUPLICA_WP_Package::get_temporary_package();
                } catch (Exception $e) {
                    $Package = null;
                }

                $treeObj = new GAMB_DUPLICA_WP_Tree_files($folder, true, $exclude);
                $treeObj->uasort(['GAMB_DUPLICA_WP_Archive', 'sortTreeByFolderWarningName']);
                if (!is_null($Package)) {
                    $treeObj->treeTraverseCallback([$Package->Archive, 'checkTreeNodesFolder']);
                }

                $jsTreeData = GAMB_DUPLICA_WP_Archive::getJsTreeStructure($treeObj, '', false);
                $json       = $jsTreeData['children'];
            }
        } catch (Exception $e) {
            GAMB_DUPLICA_WP_Log::trace($e->getMessage());
            $json['message'] = $e->getMessage();
        }
        ob_clean();
        wp_send_json($json);
    }

    /**
     * Show remote storage options from where the Backup can be downloaded
     *
     * @return void
     */
    public function remoteRestoreDownloadOptions()
    {
        AjaxWrapper::json(
            [
                self::class,
                'remoteRestoreDownloadOptionsCallback',
            ],
            'gamb-duplica-wp_get_remote_restore_download_options',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            [
                CapMng::CAP_BACKUP_RESTORE,
                CapMng::CAP_STORAGE,
            ]
        );
    }

    /**
     * Show remote storage options from where the Backup can be downloaded
     *
     * @return array<string,mixed>
     */
    public static function remoteRestoreDownloadOptionsCallback(): array
    {
        $result = [
            'success'       => true,
            'alreadyInUse'  => false,
            'cancelNeeded'  => false,
            'packageExists' => true,
            'message'       => '',
            'content'       => '',
        ];
        try {
            $packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'packageId', -1);

            switch (SnapUtil::sanitizeStrictInput(SnapUtil::INPUT_REQUEST, 'remoteAction')) {
                case 'download':
                    $action = 'download';
                    break;
                case 'restore':
                    $action = 'restore';
                    break;
                default:
                    throw new Exception(__('Invalid action.', 'gamb-duplica-wp'));
            }

            if ($packageId < 0 || ($package = GAMB_DUPLICA_WP_Package::get_by_id($packageId)) === false) {
                throw new Exception(__('Invalid Backup ID.', 'gamb-duplica-wp'));
            }

            if ($package->haveLocalStorage()) {
                throw new Exception(__('Backup already exists locally.', 'gamb-duplica-wp'));
            }

            if (GAMB_DUPLICA_WP_Package::isPackageRunning()) {
                $result['cancelNeeded'] = true;
                $activePackage          = GAMB_DUPLICA_WP_Package::get_next_active_package();

                if ($activePackage !== null && $packageId === $activePackage->ID) {
                    $result['alreadyInUse'] = true;
                }

                return $result;
            }

            $storages = $package->getValidStorages(true);

            if (count($storages) === 0) {
                $result['packageExists'] = false;
                $result['message']       = __('Backup does not exist in any remote storage.', 'gamb-duplica-wp');
                return $result;
            }

            if ($action === 'restore') {
                $template = 'admin_pages/packages/remote_download/remote_restore_options';
            } else {
                $template = 'admin_pages/packages/remote_download/remote_download_options';
            }

            $result['content'] = TplMng::getInstance()->render($template, [
                'packageId'     => $package->ID,
                'packageName'   => $package->getName(),
                'isStorageFull' => StoragesUtil::getDefaultStorage()->isFull(),
                'storages'      => $storages,
            ], false);
        } catch (Exception $ex) {
            GAMB_DUPLICA_WP_Log::trace($ex->getMessage());
            throw $ex;
        }

        return $result;
    }
}
