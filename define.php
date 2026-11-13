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

define('gamb-duplica-wp_PRO_VERSION', '4.5.21');
define('gamb-duplica-wp_PRO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('gamb-duplica-wp_PRO_SITE_URL', get_site_url());
define('gamb-duplica-wp_PRO_IMG_URL', gamb-duplica-wp_PRO_PLUGIN_URL . 'assets/img');

if (!defined("gamb-duplica-wp_PRO_SSDIR_NAME")) {
    define("gamb-duplica-wp_PRO_SSDIR_NAME", 'backups-dup-pro');
}

if (!defined("gamb-duplica-wp_DEBUG")) {
    define("gamb-duplica-wp_DEBUG", false);
}

if (!defined("gamb-duplica-wp_PRO_DEBUG_TPL_OUTPUT_INVALID")) {
    define("gamb-duplica-wp_PRO_DEBUG_TPL_OUTPUT_INVALID", false);
}

if (!defined("gamb-duplica-wp_PRO_DEBUG_TPL_DATA")) {
    define("gamb-duplica-wp_PRO_DEBUG_TPL_DATA", false);
}

if (!defined("gamb-duplica-wp_FORCE_IMPORT_BRIDGE_MODE")) {
    define("gamb-duplica-wp_FORCE_IMPORT_BRIDGE_MODE", false);
}

// PATHS
$contentPath = untrailingslashit(wp_normalize_path(realpath(WP_CONTENT_DIR)));

define("gamb-duplica-wp_PRO_SSDIR_PATH", $contentPath . '/' . gamb-duplica-wp_PRO_SSDIR_NAME);
define("gamb-duplica-wp_PRO_SSDIR_URL", content_url() . '/' . gamb-duplica-wp_PRO_SSDIR_NAME);
define("gamb-duplica-wp_PRO_IMPORTS_DIR_NAME", 'imports');
define("gamb-duplica-wp_PRO_RECOVER_DIR_NAME", 'recover');

define("gamb-duplica-wp_PRO_SSDIR_PATH_TMP", gamb-duplica-wp_PRO_SSDIR_PATH . '/tmp');
define("gamb-duplica-wp_PRO_PATH_IMPORTS", gamb-duplica-wp_PRO_SSDIR_PATH . '/' . gamb-duplica-wp_PRO_IMPORTS_DIR_NAME);
define("gamb-duplica-wp_PRO_URL_IMPORTS", gamb-duplica-wp_PRO_SSDIR_URL . '/' . gamb-duplica-wp_PRO_IMPORTS_DIR_NAME);
define("gamb-duplica-wp_PRO_SSDIR_PATH_TMP_IMPORT", gamb-duplica-wp_PRO_SSDIR_PATH_TMP . '/import');

define("gamb-duplica-wp_PRO_PATH_RECOVER", gamb-duplica-wp_PRO_SSDIR_PATH . '/' . gamb-duplica-wp_PRO_RECOVER_DIR_NAME);
define("gamb-duplica-wp_PRO_URL_RECOVER", gamb-duplica-wp_PRO_SSDIR_URL . '/' . gamb-duplica-wp_PRO_RECOVER_DIR_NAME);

define("gamb-duplica-wp_PRO_SSDIR_PATH_INSTALLER", gamb-duplica-wp_PRO_SSDIR_PATH . '/installer');
define('gamb-duplica-wp_PRO_LOCAL_OVERWRITE_PARAMS', 'gamb-duplica-wp_pro_params_overwrite');

// MATCH generic archive pattern, matches[1] is archive name and hash
define('gamb-duplica-wp_PRO_GEN_FILE_REGEX_PATTERN', '/^(.+_[a-z0-9]{7,}_[0-9]{14})_.+\\.(?:zip|daf|php|bak)$/');
// MATCH archive pattern, matches[1] is archive name and hash
define('gamb-duplica-wp_PRO_ARCHIVE_REGEX_PATTERN', '/^(.+_[a-z0-9]{7,}_[0-9]{14})_archive\\.(?:zip|daf)$/');
// MATCH installer.php installer-backup.php and full installer with hash
define('gamb-duplica-wp_PRO_INSTALLER_REGEX_PATTERN', '/^(?:.+_[a-z0-9]{7,}_[0-9]{14}_)?installer(?:-backup)?\\.php$/');
// MATCH dup-installer and dup-installer-[HASH]
define('gamb-duplica-wp_PRO_DUP_INSTALLER_FOLDER_REGEX_PATTERN', '/^dup-installer(?:-[a-z0-9]{7,}-[0-9]{8})?$/');
define('gamb-duplica-wp_PRO_DUP_INSTALLER_BOOTLOG_REGEX_PATTERN', '/^dup-installer-bootlog__[a-z0-9]{7,}-[0-9]{8}.txt$/');
define('gamb-duplica-wp_PRO_DUP_INSTALLER_OWRPARAM_REGEX_PATTERN', '/^' . gamb-duplica-wp_PRO_LOCAL_OVERWRITE_PARAMS . '_[a-z0-9]{7,}-[0-9]{8}.json$/');
define("gamb-duplica-wp_PRO_DUMP_PATH", gamb-duplica-wp_PRO_SSDIR_PATH . '/dump');
define("gamb-duplica-wp_PRO_ORIG_FOLDER_PREFIX", 'original_files_');
define('gamb-duplica-wp_PRO_LIB_PATH', gamb-duplica-wp____PATH . '/lib');
define('gamb-duplica-wp_PRO_CERT_PATH', apply_filters('gamb-duplica-wp_pro_certificate_path', gamb-duplica-wp____PATH . '/src/Libs/Certificates/cacert.pem'));

//RESTRAINT CONSTANTS
define('gamb-duplica-wp_PRO_PHP_MAX_MEMORY', 4 * GB_IN_BYTES);
define("gamb-duplica-wp_PRO_MIN_MEMORY_LIMIT", '256M');
define("gamb-duplica-wp_PRO_DB_MAX_TIME", 5000);
define("gamb-duplica-wp_PRO_DB_EOF_MARKER", 'gamb-duplica-wp_PRO_MYSQLDUMP_EOF');
define("gamb-duplica-wp_PRO_DB_MYSQLDUMP_ERROR_CONTAINING_LINE_COUNT", 10);
define("gamb-duplica-wp_PRO_DB_MYSQLDUMP_ERROR_CHARS_IN_LINE_COUNT", 1000);
define("gamb-duplica-wp_PRO_SCAN_SITE_ZIP_ARCHIVE_WARNING_SIZE", 350 * MB_IN_BYTES);
define("gamb-duplica-wp_PRO_SCAN_SITE_WARNING_SIZE", 1.5 * GB_IN_BYTES);

define("gamb-duplica-wp_PRO_SCAN_WARN_FILE_SIZE", 4 * MB_IN_BYTES);
define("gamb-duplica-wp_PRO_SCAN_WARN_DIR_SIZE", 100 * MB_IN_BYTES);
define("gamb-duplica-wp_PRO_SCAN_CACHESIZE", 1 * MB_IN_BYTES);
define("gamb-duplica-wp_PRO_SCAN_DB_ALL_SIZE", 100 * MB_IN_BYTES);
define("gamb-duplica-wp_PRO_SCAN_DB_ALL_ROWS", 1000000); //1 million rows
define('gamb-duplica-wp_PRO_SCAN_DB_TBL_ROWS', 100000); //100K rows per table
define('gamb-duplica-wp_PRO_SCAN_DB_TBL_SIZE', 10 * MB_IN_BYTES);
define("gamb-duplica-wp_PRO_SCAN_TIMEOUT", 25); //Seconds
define("gamb-duplica-wp_PRO_SCAN_MAX_UNREADABLE_COUNT", 1000);
define("gamb-duplica-wp_PRO_MAX_FAILURE_COUNT", 1000);
define("gamb-duplica-wp_PRO_BUFFER_DOWNLOAD_SIZE", 4377); // BYTES
define("gamb-duplica-wp_PRO_DEFAULT_CHUNK_UPLOAD_SIZE", 1024); // KB
define('gamb-duplica-wp_PRO_SQL_SCRIPT_PHP_CODE_MULTI_THREADED_MAX_RETRIES', 10);
define("gamb-duplica-wp_PRO_FTP_CURL_CHUNK_SIZE", 2 * MB_IN_BYTES);

define("gamb-duplica-wp_PRO_MIN_SIZE_DBFILE_WITHOUT_FILTERS", 5120); // SQL CHECK:  File should be at minimum 5K.
                                                                // A base WP install with only Create tables is about 9K
define("gamb-duplica-wp_PRO_MIN_SIZE_DBFILE_WITH_FILTERS", 800);

define("gamb-duplica-wp_PRO_ONEDRIVE_UPLOAD_CHUNK_MIN_SIZE_IN_KB", 320);
define("gamb-duplica-wp_PRO_ONEDRIVE_UPLOAD_CHUNK_DEFAULT_SIZE_IN_KB", 3200);

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
define('gamb-duplica-wp_PRO_FRONTEND_TRANSIENT', 'gamb-duplica-wp_pro_frotend_delay');
define('gamb-duplica-wp_PRO_FRONTEND_ACTION_DELAY', 1 * MINUTE_IN_SECONDS);

define('gamb-duplica-wp_PRO_INSTALLER_RENAME_KEY', 'rename_delay');
define('gamb-duplica-wp_PRO_INSTALLER_RENAME_DELAY', 12 * HOUR_IN_SECONDS);

define('gamb-duplica-wp_PRO_PENDING_CANCELLATION_TRANSIENT', 'gamb-duplica-wp_pro_pending_cancellations');
define('gamb-duplica-wp_PRO_PENDING_CANCELLATION_TIMEOUT', 1 * DAY_IN_SECONDS);

define('gamb-duplica-wp_TMP_CLEANUP_CHECK_KEY', 'tmp_cleanup_check');
define('gamb-duplica-wp_TMP_CLEANUP_CHECK_DELAY', 1 * DAY_IN_SECONDS);

define('gamb-duplica-wp_PRO_DEFAULT_AJAX_PROTOCOL', 'admin');

/* TODO: Replace all target opening up in different target with the common help target */
define('gamb-duplica-wp_PRO_HELP_TARGET', '_sc-help');

/* Help URLs */
/* TODO: search for these URLs throughout the code and replace with the corresponding define */
define('gamb-duplica-wp_PRO_BLOG_URL', 'https://gamb-duplica-wp.com/');
define('gamb-duplica-wp_PRO_gamb-duplica-wp_DOCS_URL', gamb-duplica-wp_PRO_BLOG_URL . 'knowledge-base/');
define('gamb-duplica-wp_PRO_USER_GUIDE_URL', gamb-duplica-wp_PRO_gamb-duplica-wp_DOCS_URL);
define('gamb-duplica-wp_PRO_TECH_FAQ_URL', gamb-duplica-wp_PRO_BLOG_URL . 'knowledge-base-article-categories/troubleshooting/');
define('gamb-duplica-wp_PRO_RECOVERY_GUIDE_URL', gamb-duplica-wp_PRO_BLOG_URL . 'restore-wordpress-from-backup/');
define('gamb-duplica-wp_PRO_DRAG_DROP_GUIDE_URL', gamb-duplica-wp_PRO_BLOG_URL . 'how-to-migrate-wordpress-site/');
define('gamb-duplica-wp_PRO_LOCKING_FILE_FILENAME', gamb-duplica-wp____PATH . '/dup_pro_lock.bin');

if (!defined('gamb-duplica-wp_PRO_INDEX_INCLUDE_HASH')) {
    define('gamb-duplica-wp_PRO_INDEX_INCLUDE_HASH', false);
}

if (!defined('gamb-duplica-wp_PRO_INDEX_INCLUDE_INSTALLER_FILES')) {
    define('gamb-duplica-wp_PRO_INDEX_INCLUDE_INSTALLER_FILES', false);
}

if (!defined('gamb-duplica-wp_PRO_DISALLOW_IMPORT')) {
    define('gamb-duplica-wp_PRO_DISALLOW_IMPORT', false);
}

if (!defined('gamb-duplica-wp_PRO_DISALLOW_RECOVERY')) {
    define('gamb-duplica-wp_PRO_DISALLOW_RECOVERY', false);
}

if (!defined('gamb-duplica-wp_AUTH_KEY')) {
    define('gamb-duplica-wp_AUTH_KEY', '');
}

if (!defined('gamb-duplica-wp_CAPABILITIES_RESET')) {
    define('gamb-duplica-wp_CAPABILITIES_RESET', false);
}

if (!defined('gamb-duplica-wp_CUSTOM_STATS_REMOTE_HOST')) {
    define('gamb-duplica-wp_CUSTOM_STATS_REMOTE_HOST', '');
}

if (!defined('gamb-duplica-wp_USTATS_DISALLOW')) {
    define('gamb-duplica-wp_USTATS_DISALLOW', false);
}

if (!defined('gamb-duplica-wp_PRO_PRIMARY_OAUTH_SERVER')) {
    define('gamb-duplica-wp_PRO_PRIMARY_OAUTH_SERVER', 'https://connect.gamb-duplica-wp.com');
}
if (!defined('gamb-duplica-wp_PRO_SECONDARY_OAUTH_SERVER')) {
    define('gamb-duplica-wp_PRO_SECONDARY_OAUTH_SERVER', 'https://connect2.gamb-duplica-wp.com');
}
if (!defined('gamb-duplica-wp_DISABLE_CAP_BASIC')) {
    define('gamb-duplica-wp_DISABLE_CAP_BASIC', false);
}
if (!defined('gamb-duplica-wp_DISABLE_CAP_CREATE')) {
    define('gamb-duplica-wp_DISABLE_CAP_CREATE', false);
}
if (!defined('gamb-duplica-wp_DISABLE_CAP_SCHEDULE')) {
    define('gamb-duplica-wp_DISABLE_CAP_SCHEDULE', false);
}
if (!defined('gamb-duplica-wp_DISABLE_CAP_STORAGE')) {
    define('gamb-duplica-wp_DISABLE_CAP_STORAGE', false);
}
if (!defined('gamb-duplica-wp_DISABLE_CAP_BACKUP_RESTORE')) {
    define('gamb-duplica-wp_DISABLE_CAP_BACKUP_RESTORE', false);
}
if (!defined('gamb-duplica-wp_DISABLE_CAP_IMPORT')) {
    define('gamb-duplica-wp_DISABLE_CAP_IMPORT', false);
}
if (!defined('gamb-duplica-wp_DISABLE_CAP_EXPORT')) {
    define('gamb-duplica-wp_DISABLE_CAP_EXPORT', false);
}
if (!defined('gamb-duplica-wp_DISABLE_CAP_SETTINGS')) {
    define('gamb-duplica-wp_DISABLE_CAP_SETTINGS', false);
}
if (!defined('gamb-duplica-wp_DISABLE_CAP_LICENSE')) {
    define('gamb-duplica-wp_DISABLE_CAP_LICENSE', false);
}
if (!defined('gamb-duplica-wp_TABLE_VALIDATION_FILTER_LIST')) {
    // It could be a string for single table or an array for multiple tables
    define('gamb-duplica-wp_TABLE_VALIDATION_FILTER_LIST', []);
}
