<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Ajax;

use GAMB_DUPLICA_WP_Archive;
use GAMB_DUPLICA_WP_Handler;
use GAMB_DUPLICA_WP_Package;
use GAMB_DUPLICA_WP_PackageStatus;
use GAMB_DUPLICA_WP_ScanValidator;
use gamb-duplica-wp\Addons\ProBase\License\License;
use gamb-duplica-wp\Core\CapMng;
use gamb-duplica-wp\Libs\Snap\SnapURL;
use gamb-duplica-wp\Libs\Snap\SnapUtil;
use gamb-duplica-wp\Utils\Support\SupportToolkit;
use Exception;

class ServicesTools extends AbstractAjaxService
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
        $this->addAjaxCall('wp_ajax_GAMB_DUPLICA_WP_CTRL_Tools_runScanValidator', 'runScanValidator');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_download_support_toolkit', 'downloadSupportToolkit');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_purge_backup_records', 'purgeBackupRecords');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_get_invalid_backup_records', 'getInvalidBackupRecords');
    }

    /**
     * Calls the ScanValidator and returns display JSON result
     *
     * @return void
     */
    public function runScanValidator()
    {
        GAMB_DUPLICA_WP_Handler::init_error_handler();
        check_ajax_referer('GAMB_DUPLICA_WP_CTRL_Tools_runScanValidator', 'nonce');

        // Let's setup execution time on proper way (multiserver supported)
        try {
            if (function_exists('set_time_limit')) {
                set_time_limit(0); // unlimited
            } else {
                if (function_exists('ini_set') && SnapUtil::isIniValChangeable('max_execution_time')) {
                    ini_set('max_execution_time', '0'); // unlimited
                }
            }

            // there is error inside PHP because of PHP versions and server setup,
            // let's try to made small hack and set some "normal" value if is possible
        } catch (Exception $ex) {
            if (function_exists('set_time_limit')) {
                @set_time_limit(3600); // 60 minutes
            } else {
                if (function_exists('ini_set') && SnapUtil::isIniValChangeable('max_execution_time')) {
                    @ini_set('max_execution_time', '3600'); //  60 minutes
                }
            }
        }

        //scan-recursive
        $isValid   = true;
        $inputData = filter_input_array(INPUT_POST, [
            'scan-recursive' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'flags'  => FILTER_NULL_ON_FAILURE,
            ],
        ]);

        if (is_null($inputData['scan-recursive'])) {
            $isValid = false;
        }

        $result = [
            'success'  => false,
            'message'  => '',
            'scanData' => null,
        ];

        try {
            if (!$isValid) {
                throw new Exception(__("Invalid Request.", 'gamb-duplica-wp'));
            }

            $scanner            = new GAMB_DUPLICA_WP_ScanValidator();
            $scanner->recursion = $inputData['scan-recursive'];
            $result['scanData'] = $scanner->run(GAMB_DUPLICA_WP_Archive::getScanPaths());
            $result['success']  = ($result['scanData']->fileCount > 0);
        } catch (Exception $exc) {
            $result['success'] = false;
            $result['message'] = $exc->getMessage();
        }

        wp_send_json($result);
    }

    /**
     * Function to download diagnostic data
     *
     * @return never
     */
    public function downloadSupportToolkit()
    {
        AjaxWrapper::fileDownload(
            [
                self::class,
                'downloadSupportToolkitCallback',
            ],
            'gamb-duplica-wp_download_support_toolkit',
            SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * Function to create diagnostic data
     *
     * @return false|array{path:string,name:string}
     */
    public static function downloadSupportToolkitCallback()
    {
        $domain = SnapURL::wwwRemove(SnapURL::parseUrl(network_home_url(), PHP_URL_HOST));

        return [
            'path' => SupportToolkit::getToolkit(),
            'name' => SupportToolkit::SUPPORT_TOOLKIT_PREFIX .
                substr(sanitize_file_name($domain), 0, 12) . '_' .
                date(GAMB_DUPLICA_WP_Package::PACKAGE_HASH_DATE_FORMAT) . '.zip',
        ];
    }

    /**
     * Returns json info about the number of invalid backup and total backup records
     *
     * @return void
     */
    public function getInvalidBackupRecords()
    {
        AjaxWrapper::json(
            [
                self::class,
                'getInvalidBackupRecordsCallback',
            ],
            'gamb-duplica-wp_pro_get_invalid_backup_records',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Returns info about the number of invalid backup and total backup records
     *
     * @return array{message:string,stats:array{total:int,invalid:int}}
     */
    public static function getInvalidBackupRecordsCallback()
    {
        $stats = [
            'total'   => 0,
            'invalid' => 0,
        ];
        GAMB_DUPLICA_WP_Package::by_status_callback(
            function (GAMB_DUPLICA_WP_Package $package) use (&$stats): void {
                $stats['total']++;
                if ($package->hasValidStorage()) {
                    return;
                }

                $stats['invalid']++;
            },
            [
                [
                    'op'     => '>=',
                    'status' => GAMB_DUPLICA_WP_PackageStatus::COMPLETE,
                ],
            ]
        );

        return [
            'message' => sprintf(
                _x(
                    '%1$d of %2$d backup records don\'t exist in any of their storages.',
                    '%1$d is the number of invalid records, %2$d is the total number of records',
                    'gamb-duplica-wp'
                ),
                $stats['invalid'],
                $stats['total']
            ),
            'stats'   => $stats,
        ];
    }

    /**
     * Purges backup records that have no valid storage and returns a message
     *
     * @return void
     */
    public function purgeBackupRecords()
    {
        AjaxWrapper::json(
            [
                self::class,
                'purgeBackupRecordsCallback',
            ],
            'gamb-duplica-wp_purge_backup_records',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_CREATE
        );
    }

    /**
     * Purges backup records that have no valid storage and returns a message
     *
     * @return array{message:string}
     */
    public static function purgeBackupRecordsCallback()
    {
        $stats = [
            'deleted' => 0,
            'failed'  => 0,
        ];
        GAMB_DUPLICA_WP_Package::by_status_callback(
            function (GAMB_DUPLICA_WP_Package $package) use (&$stats): void {
                if ($package->hasValidStorage()) {
                    return;
                }

                if (!$package->delete()) {
                    \GAMB_DUPLICA_WP_Log::info("Purging backup records: Failed to delete package with ID: {$package->ID}");
                    $stats['failed']++;
                } else {
                    $stats['deleted']++;
                }
            },
            [
                [
                    'op'     => '>=',
                    'status' => GAMB_DUPLICA_WP_PackageStatus::COMPLETE,
                ],
            ]
        );

        if ($stats['failed'] > 0) {
            $message = sprintf(
                _n(
                    '%1$d backup record was deleted. Failed to delete %2$d.',
                    '%1$d backup records were deleted. Failed to delete %2$d.',
                    $stats['deleted'],
                    'gamb-duplica-wp'
                ),
                $stats['deleted'],
                $stats['failed']
            );
        } else {
            $message = sprintf(
                _n(
                    '%d backup record was deleted.',
                    '%d backup records were deleted.',
                    $stats['deleted'],
                    'gamb-duplica-wp'
                ),
                $stats['deleted']
            );
        }

        return ['message' => $message];
    }
}
