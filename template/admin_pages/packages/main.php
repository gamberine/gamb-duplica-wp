<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined('ABSPATH') || exit;

use GambDuplicaWP\Controllers\PackagesPageController;
use GambDuplicaWP\Core\Views\TplMng;
use GambDuplicaWP\Core\Views\Notifications;
use GambDuplicaWP\Models\SystemGlobalEntity;

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var bool $blur
 */

$tplMng        = TplMng::getInstance();
$system_global = SystemGlobalEntity::getInstance();

$totalElements = GAMB_DUPLICA_WP_Package::getNumPackages();
$statusActive  = GAMB_DUPLICA_WP_Package::isPackageRunning();
$activePackage = GAMB_DUPLICA_WP_Package::get_next_active_package();
$isTransfer    = $activePackage === null ? false : $activePackage->Status === GAMB_DUPLICA_WP_PackageStatus::STORAGE_PROCESSING;

$pager       = new GAMB_DUPLICA_WP_Package_Pagination();
$perPage     = $pager->get_per_page();
$currentPage = $statusActive && !$isTransfer ? 1 : $pager->get_pagenum();
$offset      = ($currentPage - 1) * $perPage;

$global = GAMB_DUPLICA_WP_Global_Entity::getInstance();

do_action(Notifications::GambDuplicaWP_PRO_BEFORE_PACKAGES_HOOK);
?>

<form
    id="form-gamb-duplica-wp"
    method="post"
    class="<?php echo esc_attr($tplData['blur'] ? 'dup-mock-blur' : ''); ?>"
    >
    <?php PackagesPageController::getInstance()
            ->getActionByKey(PackagesPageController::ACTION_STOP_BUILD)->getActionNonceFileds(); ?>
    <input type="hidden" id="stop-backup-id" name="stop-backup-id" />
    <?php $tplMng->render('admin_pages/packages/toolbar'); ?>

    <table class="widefat dup-table-list dup-packtbl striped" aria-label="Backup List">
        <?php
        $tplMng->render(
            'admin_pages/packages/packages_table_head',
            ['totalElements' => $totalElements]
        );

        if ($totalElements == 0) {
            $tplMng->render('admin_pages/packages/no_elements_row');
        } else {
            GAMB_DUPLICA_WP_Package::by_status_callback(
                function (\GAMB_DUPLICA_WP_Package $package): void {
                    TplMng::getInstance()->render(
                        'admin_pages/packages/package_row',
                        ['package' => $package]
                    );
                },
                [],
                $perPage,
                $offset,
                '`id` DESC'
            );
        }
        $tplMng->render(
            'admin_pages/packages/packages_table_foot',
            ['totalElements' => $totalElements]
        ); ?>
    </table>
</form>

<?php if ($totalElements > $perPage) { ?>
    <form id="form-gamb-duplica-wp-nav" method="post">
        <?php wp_nonce_field('dpro_package_form_nonce'); ?>
        <div class="dup-paged-nav tablenav">
            <?php if ($statusActive > 0) : ?>
                <div id="dpro-paged-progress" style="padding-right: 10px">
                    <i class="fas fa-circle-notch fa-spin fa-lg fa-fw"></i>
                    <i><?php esc_html_e('Paging disabled during build...', 'gamb-duplica-wp'); ?></i>
                </div>
            <?php else : ?>
                <div id="dpro-paged-buttons">
                    <?php $pager->display_pagination($totalElements, $perPage); ?>
                </div>
            <?php endif; ?>
        </div>
    </form>
<?php } else { ?>
    <div style="float:right; padding:10px 5px">
        <?php echo esc_html(sprintf(_n('%s item', '%s items', $totalElements, 'gamb-duplica-wp'), $totalElements)); ?>
    </div>
    <?php
}

$tplMng->render(
    'admin_pages/packages/packages_scripts',
    [
        'perPage'     => $perPage,
        'offset'      => $offset,
        'currentPage' => $currentPage,
    ]
);
