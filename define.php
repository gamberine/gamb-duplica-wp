<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined('ABSPATH') || exit;

// For compatibility to an older WP
if (!defined('KB_IN_BYTES')) {
    define('KB_IN_BYTES', 1024);
}
if (!defined('MB_IN_BYTES')) {
    define('MB_IN_BYTES', 1024 * KB_IN_BYTES);
}
if (!defined('GB_IN_BYTES')) {
    define('GB_IN_BYTES', 1024 * MB_IN_BYTES);
}

if (!defined('MONTH_IN_SECONDS')) {
    define('MONTH_IN_SECONDS', 30 * DAY_IN_SECONDS);
}

if (!defined('SECONDS_IN_MICROSECONDS')) {
    define('SECONDS_IN_MICROSECONDS', 1000000);
}

define('GambDuplicaWP_PRO_VERSION', '5.0.0');
define('GambDuplicaWP_PRO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GambDuplicaWP_PRO_SITE_URL', get_site_url());
define('GambDuplicaWP_PRO_IMG_URL', GambDuplicaWP_PRO_PLUGIN_URL . 'assets/img');

if (!defined("GambDuplicaWP_PRO_SSDIR_NAME")) {
    define("GambDuplicaWP_PRO_SSDIR_NAME", 'gamb-duplica-wp-backups');
}

if (!defined("GambDuplicaWP_DEBUG")) {
    define("GambDuplicaWP_DEBUG", false);
}

if (!defined("GambDuplicaWP_PRO_DEBUG_TPL_OUTPUT_INVALID")) {
    define("GambDuplicaWP_PRO_DEBUG_TPL_OUTPUT_INVALID", false);
}

if (!defined("GambDuplicaWP_PRO_DEBUG_TPL_DATA")) {
    define("GambDuplicaWP_PRO_DEBUG_TPL_DATA", false);
}

if (!defined("GambDuplicaWP_FORCE_IMPORT_BRIDGE_MODE")) {
    define("GambDuplicaWP_FORCE_IMPORT_BRIDGE_MODE", false);
}

// PATHS
$contentPath = untrailingslashit(wp_normalize_path(realpath(WP_CONTENT_DIR)));

define("GambDuplicaWP_PRO_SSDIR_PATH", $contentPath . '/' . GambDuplicaWP_PRO_SSDIR_NAME);
define("GambDuplicaWP_PRO_SSDIR_URL", content_url() . '/' . GambDuplicaWP_PRO_SSDIR_NAME);
define("GambDuplicaWP_PRO_IMPORTS_DIR_NAME", 'imports');
define("GambDuplicaWP_PRO_RECOVER_DIR_NAME", 'recover');

define("GambDuplicaWP_PRO_SSDIR_PATH_TMP", GambDuplicaWP_PRO_SSDIR_PATH . '/tmp');
define("GambDuplicaWP_PRO_PATH_IMPORTS", GambDuplicaWP_PRO_SSDIR_PATH . '/' . GambDuplicaWP_PRO_IMPORTS_DIR_NAME);
define("GambDuplicaWP_PRO_URL_IMPORTS", GambDuplicaWP_PRO_SSDIR_URL . '/' . GambDuplicaWP_PRO_IMPORTS_DIR_NAME);
define("GambDuplicaWP_PRO_SSDIR_PATH_TMP_IMPORT", GambDuplicaWP_PRO_SSDIR_PATH_TMP . '/import');

define("GambDuplicaWP_PRO_PATH_RECOVER", GambDuplicaWP_PRO_SSDIR_PATH . '/' . GambDuplicaWP_PRO_RECOVER_DIR_NAME);
define("GambDuplicaWP_PRO_URL_RECOVER", GambDuplicaWP_PRO_SSDIR_URL . '/' . GambDuplicaWP_PRO_RECOVER_DIR_NAME);

define("GambDuplicaWP_PRO_SSDIR_PATH_INSTALLER", GambDuplicaWP_PRO_SSDIR_PATH . '/installer');
define('GambDuplicaWP_PRO_LOCAL_OVERWRITE_PARAMS', 'gamb-duplica-wp_pro_params_overwrite');

// MATCH generic archive pattern, matches[1] is archive name and hash
define('GambDuplicaWP_PRO_GEN_FILE_REGEX_PATTERN', '/^(.+_[a-z0-9]{7,}_[0-9]{14})_.+\\.(?:zip|daf|php|bak)$/');
// MATCH archive pattern, matches[1] is archive name and hash
define('GambDuplicaWP_PRO_ARCHIVE_REGEX_PATTERN', '/^(.+_[a-z0-9]{7,}_[0-9]{14})_archive\\.(?:zip|daf)$/');
// MATCH installer.php installer-backup.php and full installer with hash
define('GambDuplicaWP_PRO_INSTALLER_REGEX_PATTERN', '/^(?:.+_[a-z0-9]{7,}_[0-9]{14}_)?installer(?:-backup)?\\.php$/');
// MATCH dup-installer and dup-installer-[HASH]
define('GambDuplicaWP_PRO_DUP_INSTALLER_FOLDER_REGEX_PATTERN', '/^dup-installer(?:-[a-z0-9]{7,}-[0-9]{8})?$/');
define('GambDuplicaWP_PRO_DUP_INSTALLER_BOOTLOG_REGEX_PATTERN', '/^dup-installer-bootlog__[a-z0-9]{7,}-[0-9]{8}.txt$/');
define('GambDuplicaWP_PRO_DUP_INSTALLER_OWRPARAM_REGEX_PATTERN', '/^' . GambDuplicaWP_PRO_LOCAL_OVERWRITE_PARAMS . '_[a-z0-9]{7,}-[0-9]{8}.json$/');
define("GambDuplicaWP_PRO_DUMP_PATH", GambDuplicaWP_PRO_SSDIR_PATH . '/dump');
define("GambDuplicaWP_PRO_ORIG_FOLDER_PREFIX", 'original_files_');
define('GambDuplicaWP_PRO_LIB_PATH', GambDuplicaWP____PATH . '/lib');
define('GambDuplicaWP_PRO_CERT_PATH', apply_filters('gamb-duplica-wp_pro_certificate_path', GambDuplicaWP____PATH . '/src/Libs/Certificates/cacert.pem'));

//RESTRAINT CONSTANTS
define('GambDuplicaWP_PRO_PHP_MAX_MEMORY', 4 * GB_IN_BYTES);
define("GambDuplicaWP_PRO_MIN_MEMORY_LIMIT", '256M');
define("GambDuplicaWP_PRO_DB_MAX_TIME", 5000);
define("GambDuplicaWP_PRO_DB_EOF_MARKER", 'gamb-duplica-wp_PRO_MYSQLDUMP_EOF');
define("GambDuplicaWP_PRO_DB_MYSQLDUMP_ERROR_CONTAINING_LINE_COUNT", 10);
define("GambDuplicaWP_PRO_DB_MYSQLDUMP_ERROR_CHARS_IN_LINE_COUNT", 1000);
define("GambDuplicaWP_PRO_SCAN_SITE_ZIP_ARCHIVE_WARNING_SIZE", 350 * MB_IN_BYTES);
define("GambDuplicaWP_PRO_SCAN_SITE_WARNING_SIZE", 1.5 * GB_IN_BYTES);

define("GambDuplicaWP_PRO_SCAN_WARN_FILE_SIZE", 4 * MB_IN_BYTES);
define("GambDuplicaWP_PRO_SCAN_WARN_DIR_SIZE", 100 * MB_IN_BYTES);
define("GambDuplicaWP_PRO_SCAN_CACHESIZE", 1 * MB_IN_BYTES);
define("GambDuplicaWP_PRO_SCAN_DB_ALL_SIZE", 100 * MB_IN_BYTES);
define("GambDuplicaWP_PRO_SCAN_DB_ALL_ROWS", 1000000); //1 million rows
define('GambDuplicaWP_PRO_SCAN_DB_TBL_ROWS', 100000); //100K rows per table
define('GambDuplicaWP_PRO_SCAN_DB_TBL_SIZE', 10 * MB_IN_BYTES);
define("GambDuplicaWP_PRO_SCAN_TIMEOUT", 25); //Seconds
define("GambDuplicaWP_PRO_SCAN_MAX_UNREADABLE_COUNT", 1000);
define("GambDuplicaWP_PRO_MAX_FAILURE_COUNT", 1000);
define("GambDuplicaWP_PRO_BUFFER_DOWNLOAD_SIZE", 4377); // BYTES
define("GambDuplicaWP_PRO_DEFAULT_CHUNK_UPLOAD_SIZE", 1024); // KB
define('GambDuplicaWP_PRO_SQL_SCRIPT_PHP_CODE_MULTI_THREADED_MAX_RETRIES', 10);
define("GambDuplicaWP_PRO_FTP_CURL_CHUNK_SIZE", 2 * MB_IN_BYTES);

define("GambDuplicaWP_PRO_MIN_SIZE_DBFILE_WITHOUT_FILTERS", 5120); // SQL CHECK:  File should be at minimum 5K.
                                                                // A base WP install with only Create tables is about 9K
define("GambDuplicaWP_PRO_MIN_SIZE_DBFILE_WITH_FILTERS", 800);

define("GambDuplicaWP_PRO_ONEDRIVE_UPLOAD_CHUNK_MIN_SIZE_IN_KB", 320);
define("GambDuplicaWP_PRO_ONEDRIVE_UPLOAD_CHUNK_DEFAULT_SIZE_IN_KB", 3200);

$GLOBALS['gamb-duplica-wp_PRO_SERVER_LIST'] = array(
    'Apache',
    'LiteSpeed',
    'Nginx',
    'Lighttpd',
    'IIS',
    'WebServerX',
    'uWSGI',
    'Flywheel',
);
$GLOBALS['gamb-duplica-wp_PRO_OPTS_DELETE'] = array(
    'gamb-duplica-wp_pro_ui_view_state',
    'gamb-duplica-wp_pro_package_active',
    'gamb-duplica-wp_pro_settings',
);

$GLOBALS['gamb-duplica-wp_PRO_GLOBAL_FILE_FILTERS_ON'] = true;
$GLOBALS['gamb-duplica-wp_PRO_GLOBAL_DIR_FILTERS_ON']  = true;

/* TRANSIENT OPTIONS */
define('GambDuplicaWP_PRO_FRONTEND_TRANSIENT', 'gamb-duplica-wp_pro_frotend_delay');
define('GambDuplicaWP_PRO_FRONTEND_ACTION_DELAY', 1 * MINUTE_IN_SECONDS);

define('GambDuplicaWP_PRO_INSTALLER_RENAME_KEY', 'rename_delay');
define('GambDuplicaWP_PRO_INSTALLER_RENAME_DELAY', 12 * HOUR_IN_SECONDS);

define('GambDuplicaWP_PRO_PENDING_CANCELLATION_TRANSIENT', 'gamb-duplica-wp_pro_pending_cancellations');
define('GambDuplicaWP_PRO_PENDING_CANCELLATION_TIMEOUT', 1 * DAY_IN_SECONDS);

define('GambDuplicaWP_TMP_CLEANUP_CHECK_KEY', 'tmp_cleanup_check');
define('GambDuplicaWP_TMP_CLEANUP_CHECK_DELAY', 1 * DAY_IN_SECONDS);

define('GambDuplicaWP_PRO_DEFAULT_AJAX_PROTOCOL', 'admin');

/* TODO: Replace all target opening up in different target with the common help target */
define('GambDuplicaWP_PRO_HELP_TARGET', '_sc-help');

/* Help URLs */
/* TODO: search for these URLs throughout the code and replace with the corresponding define */
define('GambDuplicaWP_PRO_BLOG_URL', 'https://github.com/gamberine/gamb-duplica-wp');
define('GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL', GambDuplicaWP_PRO_BLOG_URL . '/blob/main/README.md');
define('GambDuplicaWP_PRO_USER_GUIDE_URL', GambDuplicaWP_PRO_GambDuplicaWP_DOCS_URL);
define('GambDuplicaWP_PRO_TECH_FAQ_URL', GambDuplicaWP_PRO_BLOG_URL . '/blob/main/docs/AUDIT.md');
define('GambDuplicaWP_PRO_RECOVERY_GUIDE_URL', GambDuplicaWP_PRO_BLOG_URL . '/blob/main/docs/OPERATIONS.md');
define('GambDuplicaWP_PRO_DRAG_DROP_GUIDE_URL', GambDuplicaWP_PRO_RECOVERY_GUIDE_URL);

define('GambDuplicaWP_PRO_LOCKING_FILE_FILENAME', GambDuplicaWP____PATH . '/dup_pro_lock.bin');

if (!defined('GambDuplicaWP_PRO_INDEX_INCLUDE_HASH')) {
    define('GambDuplicaWP_PRO_INDEX_INCLUDE_HASH', false);
}

if (!defined('GambDuplicaWP_PRO_INDEX_INCLUDE_INSTALLER_FILES')) {
    define('GambDuplicaWP_PRO_INDEX_INCLUDE_INSTALLER_FILES', false);
}

if (!defined('GambDuplicaWP_PRO_DISALLOW_IMPORT')) {
    define('GambDuplicaWP_PRO_DISALLOW_IMPORT', false);
}

if (!defined('GambDuplicaWP_PRO_DISALLOW_RECOVERY')) {
    define('GambDuplicaWP_PRO_DISALLOW_RECOVERY', false);
}

if (!defined('GambDuplicaWP_AUTH_KEY')) {
    define('GambDuplicaWP_AUTH_KEY', '');
}

if (!defined('GambDuplicaWP_CAPABILITIES_RESET')) {
    define('GambDuplicaWP_CAPABILITIES_RESET', false);
}

if (!defined('GambDuplicaWP_CUSTOM_STATS_REMOTE_HOST')) {
    define('GambDuplicaWP_CUSTOM_STATS_REMOTE_HOST', '');
}

if (!defined('GambDuplicaWP_USTATS_DISALLOW')) {
    define('GambDuplicaWP_USTATS_DISALLOW', false);
}

if (!defined('GambDuplicaWP_PRO_PRIMARY_OAUTH_SERVER')) {
    define('GambDuplicaWP_PRO_PRIMARY_OAUTH_SERVER', 'https://connect.gamb-duplica-wp.com');
}
if (!defined('GambDuplicaWP_PRO_SECONDARY_OAUTH_SERVER')) {
    define('GambDuplicaWP_PRO_SECONDARY_OAUTH_SERVER', 'https://connect2.gamb-duplica-wp.com');
}
if (!defined('GambDuplicaWP_DISABLE_CAP_BASIC')) {
    define('GambDuplicaWP_DISABLE_CAP_BASIC', false);
}
if (!defined('GambDuplicaWP_DISABLE_CAP_CREATE')) {
    define('GambDuplicaWP_DISABLE_CAP_CREATE', false);
}
if (!defined('GambDuplicaWP_DISABLE_CAP_SCHEDULE')) {
    define('GambDuplicaWP_DISABLE_CAP_SCHEDULE', false);
}
if (!defined('GambDuplicaWP_DISABLE_CAP_STORAGE')) {
    define('GambDuplicaWP_DISABLE_CAP_STORAGE', false);
}
if (!defined('GambDuplicaWP_DISABLE_CAP_BACKUP_RESTORE')) {
    define('GambDuplicaWP_DISABLE_CAP_BACKUP_RESTORE', false);
}
if (!defined('GambDuplicaWP_DISABLE_CAP_IMPORT')) {
    define('GambDuplicaWP_DISABLE_CAP_IMPORT', false);
}
if (!defined('GambDuplicaWP_DISABLE_CAP_EXPORT')) {
    define('GambDuplicaWP_DISABLE_CAP_EXPORT', false);
}
if (!defined('GambDuplicaWP_DISABLE_CAP_SETTINGS')) {
    define('GambDuplicaWP_DISABLE_CAP_SETTINGS', false);
}
if (!defined('GambDuplicaWP_DISABLE_CAP_LICENSE')) {
    define('GambDuplicaWP_DISABLE_CAP_LICENSE', false);
}
if (!defined('GambDuplicaWP_TABLE_VALIDATION_FILTER_LIST')) {
    // It could be a string for single table or an array for multiple tables
    define('GambDuplicaWP_TABLE_VALIDATION_FILTER_LIST', []);
}
