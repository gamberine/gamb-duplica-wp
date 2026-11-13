<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Addons\ProBase\License\License;
use GambDuplicaWP\Core\MigrationMng;
use GambDuplicaWP\Core\Models\AbstractEntitySingleton;
use GambDuplicaWP\Libs\Snap\SnapIO;
use GambDuplicaWP\Libs\Snap\SnapLog;
use GambDuplicaWP\Libs\Snap\SnapURL;
use GambDuplicaWP\Libs\Snap\SnapWP;
use GambDuplicaWP\Libs\Snap\SnapUtil;
use GambDuplicaWP\Models\DynamicGlobalEntity;
use GambDuplicaWP\Models\Storages\AbstractStorageEntity;
use GambDuplicaWP\Models\Storages\Local\LocalStorage;
use GambDuplicaWP\Models\Storages\StoragesUtil;
use GambDuplicaWP\Utils\Crypt\CryptBlowfish;
use GambDuplicaWP\Utils\Email\EmailSummaryBootstrap;
use GambDuplicaWP\Utils\Email\EmailSummary;
use GambDuplicaWP\Utils\ZipArchiveExtended;
use VendorGambDuplicaWP\Amk\JsonSerialize\JsonSerialize;
use GambDuplicaWP\Utils\GroupOptions;
use GambDuplicaWP\Utils\LockUtil;
use GambDuplicaWP\Utils\PathUtil;
use GambDuplicaWP\Utils\Settings\ModelMigrateSettingsInterface;
use GambDuplicaWP\Utils\UsageStatistics\CommStats;

abstract class GAMB_DUPLICA_WP_Dropbox_Transfer_Mode
{
    const Unconfigured = -1;
    const Disabled     = 0;
    const cURL         = 1;
    const FOpen_URL    = 2;
}

abstract class GAMB_DUPLICA_WP_Google_Drive_Transfer_Mode
{
    const Unconfigured = -1;
    const Auto         = 0;
    const FOpen_URL    = 1;
}

abstract class GAMB_DUPLICA_WP_Email_Build_Mode
{
    const No_Emails           = 0;
    const Email_On_Failure    = 1;
    const Email_On_All_Builds = 2;
}

abstract class GAMB_DUPLICA_WP_Archive_Build_Mode
{
    const Unconfigured = -1;
    const Shell_Exec   = 1;
    const ZipArchive   = 2;
    const DupArchive   = 3;
}

class GAMB_DUPLICA_WP_Server_Load_Reduction
{
    const None  = 0;
    const A_Bit = 1;
    const More  = 2;
    const A_Lot = 3;

    /**
     * @param int $reduction ENUM::Server_Load_Reduction
     *
     * @return int<0,max> microseconds
     */
    public static function microseconds_from_reduction($reduction)
    {
        switch ($reduction) {
            case self::A_Bit:
                return 20;
            case self::More:
                return 100;
            case self::A_Lot:
                return 500;
            case self::None:
            default:
                return 0;
        }
    }
}

abstract class GAMB_DUPLICA_WP_ZipArchive_Mode
{
    const Multithreaded = 0;
    const SingleThread  = 1;
}

class GAMB_DUPLICA_WP_Global_Entity extends AbstractEntitySingleton implements ModelMigrateSettingsInterface
{
    const INSTALLER_NAME_MODE_WITH_HASH = 'withhash';
    const INSTALLER_NAME_MODE_SIMPLE    = 'simple';

    const CLEANUP_HOOK                  = 'dup_pro_cleanup_hook';
    const CLEANUP_INTERVAL_NAME         = 'dup_pro_custom_interval';
    const CLEANUP_FILE_TIME_DELAY       = 81000; // In seconds, 22.5 hours
    const CLEANUP_EMAIL_NOTICE_INTERVAL = 24; // In hours

    const CLEANUP_MODE_OFF  = 0;
    const CLEANUP_MODE_MAIL = 1;
    const CLEANUP_MODE_AUTO = 2;

    const UNINSTALL_PACKAGE_OPTION_KEY  = 'gamb-duplica-wp_pro_uninstall_package';
    const UNINSTALL_SETTINGS_OPTION_KEY = 'gamb-duplica-wp_pro_uninstall_settings';

    const INPUT_MYSQLDUMP_OPTION_PREFIX = 'package_mysqldump_';

    //GENERAL
    /** @var bool */
    public $uninstall_settings = false;
    /** @var bool */
    public $uninstall_packages = false;
    /** @var bool */
    private $crypt = true;
    /** @var string email summary frequency */
    private $email_summary_frequency = EmailSummary::SEND_FREQ_WEEKLY;
    /** @var string[] email summary recipients */
    private $email_summary_recipients = [];
    /** @var bool */
    private $usageTracking = true;
    /** @var bool if true AM Notifications are enabled */
    private $amNotices = true;
    //PACKAGES::Visual
    /** @var bool */
    public $package_mysqldump = false;
    /** @var string */
    public $package_mysqldump_path = '';
    /** @var int<0, 1> ENUM */
    public $package_phpdump_mode = GAMB_DUPLICA_WP_DB::PHPDUMP_MODE_MULTI;
    /** @var int<0, max> */
    public $package_mysqldump_qrylimit = GAMB_DUPLICA_WP_Constants::DEFAULT_MYSQL_DUMP_CHUNK_SIZE;
    /** @var GroupOptions[] */
    private $packageMysqldumpOptions = [];
    /** @var int<-1, 3> ENUM */
    public $archive_build_mode = GAMB_DUPLICA_WP_Archive_Build_Mode::Unconfigured;
    /** @var bool */
    public $archive_compression = true;
    /** @var bool */
    public $ziparchive_validation = false;
    /** @var int<0, 1> ENUM */
    public $ziparchive_mode = GAMB_DUPLICA_WP_ZipArchive_Mode::Multithreaded;
    /** @var int<0, max> */
    public $ziparchive_chunk_size_in_mb = GAMB_DUPLICA_WP_Constants::DEFAULT_ZIP_ARCHIVE_CHUNK;
    /** @var bool */
    public $homepath_as_abspath = false;
    //PACKAGES::Basic::Processing
    /** @var int<0, 3> ENUM */
    public $server_load_reduction = GAMB_DUPLICA_WP_Server_Load_Reduction::None;
    /** @var int<0, max> */
    public $max_package_runtime_in_min = GAMB_DUPLICA_WP_Constants::DEFAULT_MAX_PACKAGE_RUNTIME_IN_MIN;
    /** @var int <0, max> */
    public $max_package_transfer_time_in_min = GAMB_DUPLICA_WP_Constants::DEFAULT_MAX_PACKAGE_TRANSFER_TIME_IN_MIN;
    /** @var int<0, max> */
    public $php_max_worker_time_in_sec = GAMB_DUPLICA_WP_Constants::DEFAULT_MAX_WORKER_TIME;
    //PACKAGES::Basic::Cleanup
    /** @var int<0,2> ENUM */
    public $cleanup_mode = self::CLEANUP_MODE_OFF;
    /** @var string */
    public $cleanup_email = '';
    /** @var int<0,max> */
    public $auto_cleanup_hours = 24;
    //PACKAGES::Advanced
    /** @var int<0,1> ENUM LockUtil::LOCK_MODE_* */
    public $lock_mode = LockUtil::LOCK_MODE_SQL;
    /** @var string */
    public $ajax_protocol = '';
    /** @var string */
    public $custom_ajax_url = '';
    /** @var bool */
    public $clientside_kickoff = false;
    /** @var bool */
    public $basic_auth_enabled = false;
    /** @var string */
    public $basic_auth_user = '';  // Not actively used but required for upgrade
    /** @var string */
    public $basic_auth_password = '';
    /** @var string ENUM */
    public $installer_name_mode = self::INSTALLER_NAME_MODE_SIMPLE;
    /** @var string */
    public $installer_base_name = GAMB_DUPLICA_WP_Installer::DEFAULT_INSTALLER_FILE_NAME_WITHOUT_HASH;
    /** @var bool */
    public $skip_archive_scan = false;
    //SCHEDULES
    /** @var int<0, 2> ENUM */
    public $send_email_on_build_mode = GAMB_DUPLICA_WP_Email_Build_Mode::Email_On_Failure;
    /** @var string */
    public $notification_email_address = '';
    //STORAGE
    /** @var bool */
    public $storage_htaccess_off = false;
    /** @var int<0, max> */
    public $max_storage_retries = 10;
    /**
     * Used on old versions before 4.5.13, mantained for upgrade. Remove on 4.5.14 or later
     *
     * @deprecated since 4.5.13
     *
     * @var int<0, max>
     **/
    public $max_default_store_files = 20;
    /**
     * Used on old versions before 4.5.13, mantained for upgrade. Remove on 4.5.14 or later
     *
     * @deprecated since 4.5.13
     *
     * @var bool
     **/
    public $purge_default_package_record = false;

    /**
     * @var int<0, 2> ENUM AbstractStorageEntity::BACKUP_RECORDS_*
     */
    protected $purgeBackupRecords = AbstractStorageEntity::BACKUP_RECORDS_REMOVE_ALL;

    /**
     * @deprecated 4.5.15 use {@see DynamicEntity::get()} instead
     * @var        null|int<0, max>
     */
    public $dropbox_upload_chunksize_in_kb = 2000;

    /**
     * @deprecated 4.5.15 use {@see DynamicEntity::get()} instead
     * @var        null|int<-1, 2> ENUM
     */
    public $dropbox_transfer_mode = GAMB_DUPLICA_WP_Dropbox_Transfer_Mode::Unconfigured;

    /**
     * @deprecated 4.5.15 use {@see DynamicEntity::get()} instead
     * @var        null|int<0, max>
     */
    public $gdrive_upload_chunksize_in_kb = 1024;  // Not exposed through the UI (yet)

    /**
     * @deprecated 4.5.15 use {@see DynamicEntity::get()} instead
     * @var        null|int<-1, 1> ENUM
     */
    public $gdrive_transfer_mode = GAMB_DUPLICA_WP_Google_Drive_Transfer_Mode::Auto;

    /**
     * @deprecated 4.5.15 use {@see DynamicEntity::get()} instead
     * @var        null|int<0, max>
     */
    public $s3_upload_part_size_in_kb = 6000;

    /**
     * @deprecated 4.5.15 use {@see DynamicEntity::get()} instead
     * @var        null|int<0, max>
     */
    public $onedrive_upload_chunksize_in_kb = GambDuplicaWP_PRO_ONEDRIVE_UPLOAD_CHUNK_DEFAULT_SIZE_IN_KB;

    /**
     * @deprecated 4.5.15 use {@see DynamicEntity::get()} instead
     * @var        null|int<0, max>
     */
    public $local_upload_chunksize_in_MB = LocalStorage::LOCAL_STORAGE_CHUNK_SIZE_IN_MB;

    /** @var int[] */
    protected $manual_mode_storage_ids = [];
    //LICENSING
    /** @var int<0, 2> License Visibility ENUM */
    public $license_key_visible = License::VISIBILITY_ALL;
    /** @var string */
    public $lkp = ''; // Not actively used but required for upgrade
    //UPDATE CACHING
    /** @var int<0, max> */
    public $last_system_check_timestamp = 0;
    /** @var int<0, max> */
    public $initial_activation_timestamp = 0;
    /** @var bool */
    public $ssl_useservercerts = true;
    /** @var bool */
    public $ssl_disableverify = true;
    /** @var int<0, max> */
    public $import_chunk_size = GambDuplicaWP_PRO_DEFAULT_CHUNK_UPLOAD_SIZE; // in KB, 0 no chunk
    /** @var string */
    public $import_custom_path = '';
    /** @var bool */
    public $ipv4_only = false;
    /** @var bool */
    public $unhook_third_party_js = false;
    /** @var bool */
    public $unhook_third_party_css = false;
    /** @var string if empty custom path is disabled */
    private $recoveryCustomPath = '';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->packageMysqldumpOptions = $this->getDefaultMysqlDumpOptions();
    }

    /**
     * Return entity type identifier
     *
     * @return string
     */
    public static function getType(): string
    {
        return 'GAMB_DUPLICA_WP_Global_Entity';
    }

    /**
     * Will be called, automatically, when Serialize
     *
     * @return array<string, mixed>
     */
    public function __serialize() // phpcs:ignore PHPCompatibility.FunctionNameRestrictions.NewMagicMethods.__serializeFound
    {
        update_option(self::UNINSTALL_PACKAGE_OPTION_KEY, $this->uninstall_packages);
        update_option(self::UNINSTALL_SETTINGS_OPTION_KEY, $this->uninstall_settings);

        $data = JsonSerialize::serializeToData($this, JsonSerialize::JSON_SKIP_MAGIC_METHODS |  JsonSerialize::JSON_SKIP_CLASS_NAME);
        if ($this->isEncryptionEnabled() && strlen($this->basic_auth_password)) {
            $data['basic_auth_password'] = CryptBlowfish::encryptIfAvaiable($this->basic_auth_password);
        }
        if ($this->isEncryptionEnabled() && strlen($this->lkp)) {
            $data['lkp'] = CryptBlowfish::encryptIfAvaiable($this->lkp);
        }
        return $data;
    }

    /**
     * Serialize
     *
     * Wakeup method.
     *
     * @return void
     */
    public function __wakeup()
    {
        // fix boolean value from old version
        $this->license_key_visible = (int) $this->license_key_visible;

        $loadedOptionNames = [];
        foreach ($this->packageMysqldumpOptions as $index => $data) {
            $this->packageMysqldumpOptions[$index] = GroupOptions::getObjectFromArray($data); // @phpstan-ignore-line
            $loadedOptionNames[]                   = $this->packageMysqldumpOptions[$index]->getOptionName();
        }
        foreach ($this->getDefaultMysqlDumpOptions() as $defOpt) {
            if (in_array($defOpt->getOptionName(), $loadedOptionNames)) {
                continue;
            }
            $this->packageMysqldumpOptions[] = $defOpt;
        }

        if ($this->isEncryptionEnabled() && strlen($this->basic_auth_password)) {
            $this->basic_auth_password = CryptBlowfish::decryptIfAvaiable($this->basic_auth_password);
        }

        if ($this->isEncryptionEnabled() && strlen($this->lkp)) {
            $this->lkp = CryptBlowfish::decryptIfAvaiable($this->lkp);
        }
    }

    /**
     * Return default options
     *
     * @return GroupOptions[]
     */
    private function getDefaultMysqlDumpOptions()
    {
        return [
            new GroupOptions('quick', self::INPUT_MYSQLDUMP_OPTION_PREFIX, false),
            new GroupOptions('extended-insert', self::INPUT_MYSQLDUMP_OPTION_PREFIX, false),
            new GroupOptions('routines', self::INPUT_MYSQLDUMP_OPTION_PREFIX, true),
            new GroupOptions('disable-keys', self::INPUT_MYSQLDUMP_OPTION_PREFIX, false),
            new GroupOptions('compact', self::INPUT_MYSQLDUMP_OPTION_PREFIX, false),
        ];
    }

    /**
     * This function is called on first istance of singletion object
     * Can be used to set dynamic properties values
     *
     * @return void
     */
    protected function firstIstanceInit()
    {
        $result = $this->reset(
            [],
            [
                self::class,
                'getDefaultPropInitVal',
            ],
            function (): void {
                $this->set_build_mode();
            }
        );
        if ($result === false) {
            throw new Exception('Can\'t reset the user settings');
        }
    }

    /**
     * Return default prop val by system config
     *
     * @param string $name prop nam
     * @param mixed  $val  prop val
     *
     * @return mixed
     */
    protected static function getDefaultPropInitVal($name, $val)
    {
        switch ($name) {
            case 'cleanup_email':
                return get_option('admin_email');
            case 'lock_mode':
                return LockUtil::getDefaultLockType();
            case 'ajax_protocol':
                return strtolower(parse_url(network_admin_url(), PHP_URL_SCHEME));
            case 'php_max_worker_time_in_sec':
                // Default is just a bit under the .7 max
                return min(
                    floor(0.7 * SnapUtil::phpIniGet("max_execution_time", 30, 'int')),
                    GAMB_DUPLICA_WP_Constants::DEFAULT_MAX_WORKER_TIME
                );
            case 'crypt':
                return CryptBlowfish::isEncryptAvailable();
            case 'custom_ajax_url':
                return admin_url('admin-ajax.php');
            case 'email_summary_recipients':
                return EmailSummary::getDefaultRecipients();
        }
        return $val;
    }

    /**
     * Reset default values
     *
     * @return bool
     */
    public function resetUserSettings(): bool
    {
        try {
            $result = $this->reset(
                [
                    'manual_mode_storage_ids',
                    'license_key_visible',
                    'lkp',
                    'last_system_check_timestamp',
                    'initial_activation_timestamp',
                ],
                [
                    self::class,
                    'getDefaultPropInitVal',
                ],
                function (): void {
                    $this->set_build_mode();
                }
            );

            if ($result == false) {
                throw new Exception('Can\'t reset global entity values');
            }

            $sglobal = GAMB_DUPLICA_WP_Secure_Global_Entity::getInstance();
            if ($sglobal->save() == false) {
                throw new Exception('Can\'t save secure global');
            }
        } catch (Exception $e) {
            GAMB_DUPLICA_WP_Log::traceError('Reset user settings error mrg: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Get usage tracking
     *
     * @return bool
     */
    public function getUsageTracking()
    {
        return $this->usageTracking;
    }

    /**
     * Set usage tracking
     *
     * @param bool $value value
     *
     * @return void
     */
    public function setUsageTracking($value)
    {
        if (GambDuplicaWP_USTATS_DISALLOW) { // @phpstan-ignore-line
            // If usagfe tracking is hardcoded disabled, don't change the setting value
            return;
        }

        $value               = (bool) $value;
        $oldValue            = $this->usageTracking;
        $this->usageTracking = $value;

        if ($value == false && $oldValue != $value) {
            CommStats::disableUsageTracking();
        }
    }

    /**
     * Return recovery custom path
     *
     * @return string
     */
    public function getRecoveryCustomPath()
    {
        return $this->recoveryCustomPath;
    }

    /**
     * Return recovery custom URL
     *
     * @return string return empty URL if custom path isn't set
     */
    public function getRecoveryCustomURL()
    {
        if (strlen($this->recoveryCustomPath) == 0) {
            return '';
        }

        if (SnapIO::isChildPath($this->recoveryCustomPath, GAMB_DUPLICA_WP_Archive::getArchiveListPaths('wpcontent'), false, true, true)) {
            $mainPath = GAMB_DUPLICA_WP_Archive::getArchiveListPaths('wpcontent');
            $mainURL  = GAMB_DUPLICA_WP_Archive::getOriginalUrls('wpcontent');
        } else {
            $mainPath = GAMB_DUPLICA_WP_Archive::getArchiveListPaths('home');
            $mainURL  = GAMB_DUPLICA_WP_Archive::getOriginalUrls('home');
        }

        return $mainURL . '/' . SnapIo::getRelativePath($this->recoveryCustomPath, $mainPath, true);
    }

    /**
     * Set recovery custom path
     *
     * @param string $path        path
     * @param string $failMessage return fail message
     *
     * @return bool
     */
    public function setRecoveryCustomPath($path, &$failMessage = ''): bool
    {
        $remove = false;

        try {
            $this->recoveryCustomPath = '';

            if (strlen($path) == 0) {
                return true;
            }

            if (file_exists($path)) {
                if (
                    !is_dir($path) ||
                    !is_writable($path)
                ) {
                    throw new Exception(__('The Recovery custom path must be a folder with write permissions.', 'gamb-duplica-wp'));
                }
            } else {
                if (wp_mkdir_p($path) == false) {
                    throw new Exception(sprintf(__('It is not possible to create the folder %s', 'gamb-duplica-wp'), $path));
                }
            }

            if (
                !SnapIO::isChildPath($path, GAMB_DUPLICA_WP_Archive::getArchiveListPaths('home'), false, false, true) &&
                !SnapIO::isChildPath($path, GAMB_DUPLICA_WP_Archive::getArchiveListPaths('wpcontent'), false, false, true)
            ) {
                throw new Exception(__('The custom Recovery path must be a child folder of the home path or wp-content', 'gamb-duplica-wp'));
            }

            if (PathUtil::isPathInCoreDirs($path)) {
                throw new Exception(__('The Recovery custom path cannot be a wordpress core folder.', 'gamb-duplica-wp'));
            }
        } catch (Exception $e) {
            $remove      = true;
            $failMessage = $e->getMessage();
            return false;
        } finally {
            if ($remove) {
                rmdir($path);
            }
        }

        $this->recoveryCustomPath = $path;
        return true;
    }

    /**
     * Update global settings after install
     *
     * @return bool true on success false on failure
     */
    public function updateAftreInstall()
    {
        $this->lock_mode     = LockUtil::getDefaultLockType();
        $this->ajax_protocol = GambDuplicaWP_PRO_DEFAULT_AJAX_PROTOCOL;
        if ($this->getBuildMode() !== GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive) {
            $this->set_build_mode();
        }
        return $this->save();
    }

    /**
     * To export data
     *
     * @return array<string, mixed>
     */
    public function settingsExport()
    {
        $skipProps = [
            'id',
            'last_system_check_timestamp',
            'initial_activation_timestamp',
            'manual_mode_storage_ids',
            'license_key_visible',
            'lkp',
        ];

        $data = JsonSerialize::serializeToData($this, JsonSerialize::JSON_SKIP_MAGIC_METHODS |  JsonSerialize::JSON_SKIP_CLASS_NAME);
        foreach ($skipProps as $prop) {
            unset($data[$prop]);
        }
        return $data;
    }

    /**
     * Update object properties from import data
     *
     * @param array<string, mixed> $data        data to import
     * @param string               $dataVersion version of data
     * @param array<string, mixed> $extraData   extra data, useful form id mapping etc.
     *
     * @return bool True if success, otherwise false
     */
    public function settingsImport($data, $dataVersion, array $extraData = []): bool
    {
        $skipProps = [
            'id',
            'last_system_check_timestamp',
            'initial_activation_timestamp',
            'manual_mode_storage_ids',
            'license_key_visible',
            'lkp',
        ];

        $reflect = new ReflectionClass(self::class);
        $props   = $reflect->getProperties();

        foreach ($props as $prop) {
            if (in_array($prop->getName(), $skipProps)) {
                continue;
            }
            if (!isset($data[$prop->getName()])) {
                continue;
            }
            $prop->setAccessible(true);
            $prop->setValue($this, $data[$prop->getName()]);
        }
        return true;
    }

    /**
     * Set from object
     *
     * @param self $global_data global data
     *
     * @return void
     */
    public function setFromImportData(self $global_data)
    {
        $reflect = new ReflectionClass(self::class);
        $props   = $reflect->getProperties();

        $skipProps = [
            'id',
            'last_system_check_timestamp',
            'initial_activation_timestamp',
            'manual_mode_storage_ids',
            'license_key_visible',
            'lkp',
        ];

        foreach ($props as $prop) {
            if (in_array($prop->getName(), $skipProps)) {
                continue;
            }
            $prop->setAccessible(true);
            $prop->setValue($this, $prop->getValue($global_data));
        }
    }

    /**
     * Check if build mode is available
     *
     * @param int $buildMode build mode constant
     *
     * @return bool
     */
    public static function isBuildModeAvailable($buildMode)
    {
        switch ($buildMode) {
            case GAMB_DUPLICA_WP_Archive_Build_Mode::Unconfigured:
                return false;
            case GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec:
                return (GAMB_DUPLICA_WP_Zip_U::getShellExecZipPath() != null);
            case GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive:
                return ZipArchiveExtended::isPhpZipAvailable();
            case GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive:
                return true;
            default:
                throw new Exception('Invalid engine');
        }
    }

    /**
     * Return Backup build mode
     *
     * @return int Return enum GAMB_DUPLICA_WP_Archive_Build_Mode
     */
    public function getBuildMode()
    {
        $archive_build_mode = $this->archive_build_mode;

        switch ($archive_build_mode) {
            case GAMB_DUPLICA_WP_Archive_Build_Mode::Unconfigured:
                if (self::isBuildModeAvailable(GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec)) {
                    $archive_build_mode = GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec;
                } elseif (self::isBuildModeAvailable(GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive)) {
                    $archive_build_mode = GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive;
                } else {
                    $archive_build_mode = GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive;
                }
                break;
            case GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec:
                if (!self::isBuildModeAvailable(GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec)) {
                    if (self::isBuildModeAvailable(GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive)) {
                        $archive_build_mode = GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive;
                    } else {
                        $archive_build_mode = GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive;
                    }
                }
                break;
            case GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive:
                if (!self::isBuildModeAvailable(GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive)) {
                    if (self::isBuildModeAvailable(GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec)) {
                        $archive_build_mode = GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec;
                    } else {
                        $archive_build_mode = GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive;
                    }
                }
                break;
            case GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive:
                break;
            default:
                throw new Exception('Invalid engine');
        }
        return $archive_build_mode;
    }

    /**
     * Selt build mode and return it
     *
     * @param bool $save if true save update global entity only if build mode is changed
     *
     * @return int Return enum GAMB_DUPLICA_WP_Archive_Build_Mode
     */
    public function set_build_mode($save = false)
    {
        $newBuildMode = apply_filters('gamb-duplica-wp_pro_default_archive_build_mode', $this->getBuildMode());
        if ($newBuildMode != $this->archive_build_mode) {
            $this->archive_build_mode = $newBuildMode;
            if ($save) {
                $this->save();
            }
        }
        return $this->archive_build_mode;
    }

    /**
     *
     * @return int<0,max> microsenconds
     */
    public function getMicrosecLoadReduction()
    {
        return GAMB_DUPLICA_WP_Server_Load_Reduction::microseconds_from_reduction($this->server_load_reduction);
    }

    /**
     * set db mode ant all related params.
     * check mysqldump
     *
     * @param null|string $dbMode                if null get INPUT_POST
     * @param null|int    $phpDumpMode           if null get INPUT_POST
     * @param null|int    $dbPhpQueryLimit       if null get INPUT_POST
     * @param null|string $packageMysqldumpPath  if null get INPUT_POST
     * @param null|int    $dbMysqlDumpQueryLimit if null get INPUT_POST
     *
     * @return void
     */
    public function setDbMode(
        $dbMode = null,
        $phpDumpMode = null,
        $dbPhpQueryLimit = null,
        $packageMysqldumpPath = null,
        $dbMysqlDumpQueryLimit = null
    ) {
        //DATABASE
        $dbMode                = is_null($dbMode) ? SnapUtil::sanitizeDefaultInput(INPUT_POST, '_package_dbmode') : $dbMode;
        $phpDumpMode           = is_null($phpDumpMode) ? filter_input(
            INPUT_POST,
            '_phpdump_mode',
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'default'   => 0,
                    'min_range' => 0,
                    'max_range' => 1,
                ],
            ]
        ) : $phpDumpMode;
        $dbMysqlDumpQueryLimit = is_null($dbMysqlDumpQueryLimit) ? filter_input(
            INPUT_POST,
            '_package_mysqldump_qrylimit',
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'default'   => GAMB_DUPLICA_WP_Constants::DEFAULT_MYSQL_DUMP_CHUNK_SIZE,
                    'min_range' => GAMB_DUPLICA_WP_Constants::MYSQL_DUMP_CHUNK_SIZE_MIN_LIMIT,
                    'max_range' => GAMB_DUPLICA_WP_Constants::MYSQL_DUMP_CHUNK_SIZE_MAX_LIMIT,
                ],
            ]
        ) : $dbMysqlDumpQueryLimit;

        $packageMysqldumpPath = is_null($packageMysqldumpPath) ?
            SnapUtil::sanitizeDefaultInput(INPUT_POST, '_package_mysqldump_path') :
            $packageMysqldumpPath;
        $packageMysqldumpPath = SnapUtil::sanitizeNSCharsNewlineTabs($packageMysqldumpPath);
        $packageMysqldumpPath = preg_match('/^([A-Za-z]\:)?[\/\\\\]/', $packageMysqldumpPath) ? $packageMysqldumpPath : '';
        $packageMysqldumpPath = preg_replace('/[\'"]/m', '', $packageMysqldumpPath);
        $packageMysqldumpPath = SnapIO::safePathUntrailingslashit($packageMysqldumpPath);

        $mysqlDumpPath = empty($packageMysqldumpPath) ? GAMB_DUPLICA_WP_DB::getMySqlDumpPath() : $packageMysqldumpPath;
        if ($dbMode == 'mysql' && empty($mysqlDumpPath)) {
            $dbMode = 'php';
        }

        $this->package_mysqldump          = ($dbMode == 'mysql');
        $this->package_phpdump_mode       = $phpDumpMode;
        $this->package_mysqldump_path     = $packageMysqldumpPath;
        $this->package_mysqldump_qrylimit = $dbMysqlDumpQueryLimit;

        foreach ($this->getMysqldumpOptions() as $option) {
            $option->update();
        }
    }

    /**
     * Sets cleanup fields and configures WP Cron accordingly
     *
     * @param int    $cleanup_mode       Cleanup mode to set
     * @param string $cleanup_email      Email address to send cleanup notification to
     * @param int    $auto_cleanup_hours Number of hours after which cleanup should be performed
     *
     * @return void
     */
    public function setCleanupFields($cleanup_mode = null, $cleanup_email = null, $auto_cleanup_hours = null)
    {
        $this->cleanup_mode = is_null($cleanup_mode) ? filter_input(
            INPUT_POST,
            'cleanup_mode',
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'default'   => self::CLEANUP_MODE_OFF,
                    'min_range' => 0,
                    'max_range' => 2,
                ],
            ]
        ) : $cleanup_mode;

        $email               = filter_input(INPUT_POST, 'cleanup_email', FILTER_VALIDATE_EMAIL, ['options' => ['default' => '']]);
        $email               = $email === '' ? get_option('admin_email') : $email;
        $this->cleanup_email = is_null($cleanup_email) ? $email : $cleanup_email;

        $this->auto_cleanup_hours = is_null($auto_cleanup_hours) ? filter_input(
            INPUT_POST,
            'auto_cleanup_hours',
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'default'   => 24,
                    'min_range' => 1,
                ],
            ]
        ) : $auto_cleanup_hours;

        self::cleanupScheduleSetup();
    }

    /**
     * Schedules cron event for installer files cleanup purposes,
     * and unschedules it if it's not needed anymore.
     *
     * @return void
     */
    public static function cleanupScheduleSetup()
    {
        $global = self::getInstance();
        SnapWP::unscheduleEvent(self::CLEANUP_HOOK);
        if ($global->cleanup_mode == self::CLEANUP_MODE_MAIL) {
            $nextRunTime = time() + self::CLEANUP_EMAIL_NOTICE_INTERVAL * 3600;
            SnapWP::scheduleEvent($nextRunTime, self::CLEANUP_INTERVAL_NAME, self::CLEANUP_HOOK);
        } elseif ($global->cleanup_mode == self::CLEANUP_MODE_AUTO) {
            $nextRunTime = time() + $global->auto_cleanup_hours * 3600;
            SnapWP::scheduleEvent($nextRunTime, self::CLEANUP_INTERVAL_NAME, self::CLEANUP_HOOK);
        }
    }

    /**
     * Customizes schedules according to current cleanup_mode. If necessary, it
     * adds a custom cron schedule that will run every N hours.
     *
     * @param array<string,array{interval:int,display:string}> $schedules An array of non-default cron schedules.
     *
     * @return array<string,array{interval:int,display:string}> Filtered array of non-default cron schedules.
     */
    public static function customCleanupCronInterval($schedules)
    {
        $global = self::getInstance();

        switch ($global->cleanup_mode) {
            case self::CLEANUP_MODE_OFF:
                // No need to modify anything
                break;
            case self::CLEANUP_MODE_MAIL:
                $schedules[self::CLEANUP_INTERVAL_NAME] = [
                    'interval' => self::CLEANUP_EMAIL_NOTICE_INTERVAL * 3600, // In seconds, every N hours
                    'display'  => sprintf(esc_html__('Every %1$d hours', 'gamb-duplica-wp'), self::CLEANUP_EMAIL_NOTICE_INTERVAL),
                ];
                break;
            case self::CLEANUP_MODE_AUTO:
                $schedules[self::CLEANUP_INTERVAL_NAME] = [
                    'interval' => $global->auto_cleanup_hours * 3600, // In seconds, every N hours
                    'display'  => sprintf(esc_html__('Every %1$d hours', 'gamb-duplica-wp'), $global->auto_cleanup_hours),
                ];
                break;
            default:
                throw new Exception('Invalid cleanup mode:' . SnapLog::v2str($global->cleanup_mode));
        }
        return $schedules;
    }

    /**
     * The function that gets executed by WP Cron for cleanup of installer files.
     * It does different tasks based on current cleanup_mode setting.
     *
     * @return void
     */
    public static function cleanupCronJob()
    {
        $global = self::getInstance();

        $websiteUrl = SnapURL::getCurrentUrl(false, false, 1);
        $to         = $global->cleanup_email;
        if (empty($to)) {
            $to = get_option('admin_email');
        }

        switch ($global->cleanup_mode) {
            case self::CLEANUP_MODE_MAIL:
                // Email Notice cron job routine for cleanup of installer files
                $listOfInstallerFiles = MigrationMng::checkInstallerFilesList();
                $filesToRemove        = [];

                foreach ($listOfInstallerFiles as $path) {
                    if (time() - filectime($path) > self::CLEANUP_FILE_TIME_DELAY) {
                        $filesToRemove[] = $path;
                    }
                }

                if (count($filesToRemove) > 0 && !empty($to)) {
                    // Send an Email Notice
                    $subject  = __("Action required", 'gamb-duplica-wp');
                    $message  = sprintf(__('This email is sent by your Wordpress plugin "gamb-duplica-wp Pro" from website: %1$s. ', 'gamb-duplica-wp'), $websiteUrl);
                    $message .= __('You received this email because Cleanup mode is set to "Email Notice". ', 'gamb-duplica-wp');
                    $message .= __('Cleanup routine discovered that some installer files (leftovers from migration) were not removed. ', 'gamb-duplica-wp');
                    $message .= __('We strongly advise you to remove these files. ', 'gamb-duplica-wp');
                    $message .= __('Here is the list of files found on your website that you should remove:', 'gamb-duplica-wp') . "<br/>";
                    foreach ($filesToRemove as $path) {
                        $message .= "-> $path<br/>";
                    }
                    $message .= "<br/>";
                    $message .= __('Note: You could enable "Auto Cleanup" mode if you go to:', 'gamb-duplica-wp') . "<br/>";
                    $message .= __('WordPress Admin > gamb-duplica-wp Pro > Settings > Backups Tab > Cleanup.', 'gamb-duplica-wp') . "<br/>";
                    $message .= __('That mode will do cleanup of those files automatically for you.', 'gamb-duplica-wp') . "<br/>";
                    $message .= "<br/>";
                    $message .= __('Best regards,', 'gamb-duplica-wp') . "<br/>";
                    $message .= __('gamb-duplica-wp Pro', 'gamb-duplica-wp');

                    if (wp_mail($to, $subject, $message, ['Content-Type: text/html; charset=UTF-8'])) {
                        // OK
                        \GAMB_DUPLICA_WP_Log::trace('wp_mail sent email notice regarding cleanup of installer files');
                    } else {
                        \GAMB_DUPLICA_WP_Log::trace("Problem sending email notice regarding cleanup of installer files to {$to}");
                    }
                }
                break;
            case self::CLEANUP_MODE_AUTO:
                // Auto Cleanup cron job routine for cleanup of installer files
                $installerFiles = MigrationMng::cleanMigrationFiles(false, self::CLEANUP_FILE_TIME_DELAY);
                if (count($installerFiles) == 0) {
                    // No installer files were found, so we do nothing else
                    return;
                }

                $filesFailedRemoval = [];
                foreach ($installerFiles as $path => $success) {
                    if (!$success) {
                        $filesFailedRemoval[] = $path;
                    }
                }
                if (count($filesFailedRemoval) == 0) {
                    // All found installer files were removed successfully,
                    // or they did not even need to be removed yet because of CLEANUP_FILE_TIME_DELAY
                    return;
                }

                // If this is executed that means that some of installer files
                // could not be removed for some reason (permission issues?)
                if (!empty($to)) {
                    // Send an Email Notice about files that could not be removed during auto cleanup
                    $subject  = __("Action required", 'gamb-duplica-wp');
                    $message  = sprintf(__('This email is sent by your Wordpress plugin "gamb-duplica-wp Pro" from website: %1$s. ', 'gamb-duplica-wp'), $websiteUrl);
                    $message .= __('"Auto Cleanup" mode is ON, ', 'gamb-duplica-wp');
                    $message .= __('however cleanup routine discovered that some installer files (leftovers from migration) could not be removed. ', 'gamb-duplica-wp');
                    $message .= __('We strongly advise you to remove those files manually. ', 'gamb-duplica-wp');
                    $message .= __('Here is the list of files found on your website that you should remove:', 'gamb-duplica-wp') . "<br/>";
                    foreach ($filesFailedRemoval as $path) {
                        $message .= "-> $path<br/>";
                    }
                    $message .= "<br/>";
                    $message .= __('Those files probably could not be removed due to permission issues. ', 'gamb-duplica-wp');
                    $message .= sprintf(
                        __('You can find more info in FAQ %1$son this link%2$s.', 'gamb-duplica-wp'),
                        "<a href='" . GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL . "how-to-fix-file-permissions-issues' target='_blank'>",
                        "</a>"
                    ) . "<br/>";
                    $message .= "<br/>";
                    $message .= __('Note: To edit "Cleanup" settings go to:', 'gamb-duplica-wp') . "<br/>";
                    $message .= __('WordPress Admin > gamb-duplica-wp Pro > Settings > Backups Tab > Cleanup.', 'gamb-duplica-wp') . "<br/>";
                    $message .= "<br/>";
                    $message .= __('Best regards,', 'gamb-duplica-wp') . "<br/>";
                    $message .= __('gamb-duplica-wp Pro', 'gamb-duplica-wp');

                    if (wp_mail($to, $subject, $message, ['Content-Type: text/html; charset=UTF-8'])) {
                        // OK
                        \GAMB_DUPLICA_WP_Log::trace('wp_mail sent email notice regarding failed auto cleanup of installer files');
                    } else {
                        \GAMB_DUPLICA_WP_Log::trace("Problem sending email notice regarding failed auto cleanup of installer files to {$to}");
                    }
                }
                break;
            case self::CLEANUP_MODE_OFF:
            default:
                break;
        }
    }

    /**
     * Set archive mode
     *
     * @param ?int  $archiveBuildMode        Archive build mode, if null get INPUT_POST
     * @param ?int  $zipArchiveMode          Zip archive mode, if null get INPUT_POST
     * @param ?bool $archiveCompression      Archive compression, if null get INPUT_POST
     * @param ?bool $ziparchiveValidation    Zip archive validation, if null get INPUT_POST
     * @param ?int  $ziparchiveChunkSizeInMb Zip archive chunk size in MB, if null get INPUT_POST
     *
     * @return void
     */
    public function setArchiveMode(
        $archiveBuildMode = null,
        $zipArchiveMode = null,
        $archiveCompression = null,
        $ziparchiveValidation = null,
        $ziparchiveChunkSizeInMb = null
    ) {
        $isZipAvailable = (GAMB_DUPLICA_WP_Zip_U::getShellExecZipPath() != null);

        $prelimBuildMode = is_null($archiveBuildMode) ? filter_input(
            INPUT_POST,
            'archive_build_mode',
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                    'max_range' => 3,
                ],
            ]
        ) : $archiveBuildMode;

        // Something has changed which invalidates Shell exec so move it to ZA
        $this->archive_build_mode = (!$isZipAvailable && ($prelimBuildMode == GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec)) ? GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive : $prelimBuildMode;
        $this->ziparchive_mode    = is_null($zipArchiveMode) ? filter_input(
            INPUT_POST,
            'ziparchive_mode',
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'default'   => 0,
                    'min_range' => 0,
                    'max_range' => 1,
                ],
            ]
        ) : $zipArchiveMode;

        $this->archive_compression         = is_null($archiveCompression) ? filter_input(INPUT_POST, 'archive_compression', FILTER_VALIDATE_BOOLEAN) : $archiveCompression;
        $this->ziparchive_validation       = is_null($ziparchiveValidation) ? filter_input(INPUT_POST, 'ziparchive_validation', FILTER_VALIDATE_BOOLEAN) : $ziparchiveValidation;
        $this->ziparchive_chunk_size_in_mb = is_null($ziparchiveChunkSizeInMb) ? filter_input(
            INPUT_POST,
            'ziparchive_chunk_size_in_mb',
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'default'   => GAMB_DUPLICA_WP_Constants::DEFAULT_ZIP_ARCHIVE_CHUNK,
                    'min_range' => 1,
                ],
            ]
        ) : $ziparchiveChunkSizeInMb;
    }

    /**
     * Set clientside kickoff
     *
     * @param bool $enable enable or disable
     *
     * @return void
     */
    public function setClientsideKickoff($enable)
    {
        if ($this->clientside_kickoff != $enable) {
            $this->clientside_kickoff = $enable;

            if ($this->clientside_kickoff) {
                // Auto setting the max Backup runtime in case of client kickoff is turned on and
                // the max Backup runtime is less than 480 minutes - 8 hours
                $this->max_package_runtime_in_min = max(480, $this->max_package_runtime_in_min);
                $this->setDbMode('mysql');

                // RSR 4/29/19 not setting archive mode for now - too risky
                // $mode = (GAMB_DUPLICA_WP_Zip_U::getShellExecZipPath() != null) ? GAMB_DUPLICA_WP_Archive_Build_Mode::Shell_Exec : GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive;
                // $this->setArchiveMode($mode);
            }
        }
    }

    /**
     * Set encryption settings
     *
     * @param bool $enable enable or disable
     *
     * @return void
     */
    public function setEncryption($enable)
    {
        $this->crypt = (CryptBlowfish::isEncryptAvailable() && $enable);
    }

    /**
     * Is encryption settings enabled
     *
     * @return bool
     */
    public function isEncryptionEnabled()
    {
        // If crypt is disabled don't check for encryption availability
        return $this->crypt && CryptBlowfish::isEncryptAvailable();
    }

    /**
     * Configure dropbox transfer mode
     *
     * @return void
     */
    public function configure_dropbox_transfer_mode()
    {
        $dGlobal = DynamicGlobalEntity::getInstance();
        if ($dGlobal->getVal('dropbox_transfer_mode') == GAMB_DUPLICA_WP_Dropbox_Transfer_Mode::Unconfigured) {
            $mode = GAMB_DUPLICA_WP_Dropbox_Transfer_Mode::Disabled;
            if (SnapUtil::isCurlEnabled()) {
                $mode = GAMB_DUPLICA_WP_Dropbox_Transfer_Mode::cURL;
            } elseif (GAMB_DUPLICA_WP_Server::isURLFopenEnabled()) {
                $mode = GAMB_DUPLICA_WP_Dropbox_Transfer_Mode::FOpen_URL;
            }
            $dGlobal->setVal('dropbox_transfer_mode', $mode, true);
        }
    }

    /**
     * Get installer backup filename
     *
     * @return string
     */
    public function get_installer_backup_filename(): string
    {
        $installer_extension = $this->get_installer_extension();

        if (trim($installer_extension) == '') {
            return 'installer-backup';
        } else {
            return "installer-backup.$installer_extension";
        }
    }

    /**
     * Get installer extension
     *
     * @return string
     */
    public function get_installer_extension()
    {
        return pathinfo($this->installer_base_name, PATHINFO_EXTENSION);
    }

    /**
     * Get archive engine label
     *
     * @return string
     */
    public function get_archive_engine()
    {
        $mode = '';
        switch ($this->archive_build_mode) {
            case GAMB_DUPLICA_WP_Archive_Build_Mode::ZipArchive:
                $mode = ($this->ziparchive_mode == GAMB_DUPLICA_WP_ZipArchive_Mode::Multithreaded) ?
                    __("ZipArchive: multi-thread", 'gamb-duplica-wp') :
                    __("ZipArchive: single-thread", 'gamb-duplica-wp');
                break;

            case GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive:
                $mode = __('DupArchive', 'gamb-duplica-wp');
                break;

            default:
                $mode = __("Shell Zip", 'gamb-duplica-wp');
                break;
        }

        return $mode;
    }

    /**
     * Return archive extension type
     *
     * @return string
     */
    public function get_archive_extension_type()
    {
        $mode = 'zip';
        if ($this->archive_build_mode == GAMB_DUPLICA_WP_Archive_Build_Mode::DupArchive) {
            $mode = 'daf';
        }
        return $mode;
    }

    /**
     * Return Mysqldump options
     *
     * @return GroupOptions[]
     */
    public function getMysqldumpOptions()
    {
        return $this->packageMysqldumpOptions;
    }

    /**
     * Get manual mode storage ids
     *
     * @return int[]
     */
    public function getManualModeStorageIds()
    {
        if (count($this->manual_mode_storage_ids) == 0) {
            $this->manual_mode_storage_ids = [StoragesUtil::getDefaultStorageId()];
        }
        return $this->manual_mode_storage_ids;
    }

    /**
     * Set manual mode storage ids
     *
     * @param int[] $storageIds Storage ids
     *
     * @return void
     */
    public function setManualModeStorageIds(array $storageIds)
    {
        $this->manual_mode_storage_ids = [];
        foreach ($storageIds as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                continue;
            }
            $this->manual_mode_storage_ids[] = $id;
        }
        if (count($this->manual_mode_storage_ids) == 0) {
            $this->manual_mode_storage_ids = [StoragesUtil::getDefaultStorageId()];
        }
    }

    /**
     * Get Email Summary Recipients
     *
     * @return string[]
     */
    public function getEmailSummaryRecipients()
    {
        return $this->email_summary_recipients;
    }

    /**
     * Set Email Summary Recipients
     *
     * @param string[] $recipients List of recipient email addreses
     *
     * @return void
     */
    public function setEmailSummaryRecipients($recipients)
    {
        $recipients = filter_var($recipients, FILTER_VALIDATE_EMAIL, FILTER_REQUIRE_ARRAY);
        if ($recipients === false) {
            $recipients = [];
        }

        foreach ($recipients as $key => $recipient) {
            if ($recipient === false) {
                continue;
            }

            $recipients[$key] = sanitize_email($recipient);
        }

        $this->email_summary_recipients = array_values(array_unique($recipients));
    }

    /**
     * Get email summary frequency
     *
     * @return string
     */
    public function getEmailSummaryFrequency()
    {
        return $this->email_summary_frequency;
    }

    /**
     * Set email summary frequency
     *
     * @param string $frequency The frequency
     *
     * @return void
     */
    public function setEmailSummaryFrequency($frequency)
    {
        if (EmailSummaryBootstrap::updateFrequency($this->email_summary_frequency, $frequency) === false) {
            GAMB_DUPLICA_WP_Log::trace("Invalid email summary frequency: {$frequency}");
            return;
        }
        $this->email_summary_frequency = $frequency;
    }

    /**
     * True if AM notifications are enabled
     *
     * @return bool
     */
    public function isAmNoticesEnabled()
    {
        return $this->amNotices;
    }

    /**
     * Set notifications enabled
     *
     * @param bool $enable true if enabled
     *
     * @return void
     */
    public function setAmNotices($enable)
    {
        $this->amNotices = $enable;
    }

    /**
     * Set purge backup records
     *
     * @param int<0,2> $value value
     *
     * @return void
     */
    public function setPurgeBackupRecords($value)
    {
        switch ($value) {
            case AbstractStorageEntity::BACKUP_RECORDS_REMOVE_ALL:
            case AbstractStorageEntity::BACKUP_RECORDS_REMOVE_NEVER:
            case AbstractStorageEntity::BACKUP_RECORDS_REMOVE_DEFAULT:
                $this->purgeBackupRecords = $value;
                break;
            default:
                throw new Exception('Invalid value');
        }
    }

    /**
     * Get option on how to handle the old backup records
     *
     * @return int<0,2>
     */
    public function getPurgeBackupRecords()
    {
        return $this->purgeBackupRecords;
    }
}
