<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Controllers\PackagesPageController;
use GambDuplicaWP\Core\CapMng;
use GambDuplicaWP\Core\Controllers\SubMenuItem;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string,mixed> $tplData
 * @var string $pageTitle
 */
$pageTitle = $tplData['pageTitle'];
/** @var GAMB_DUPLICA_WP_Package $package */
$package = $tplData['package'];
/** @var string */
$templateSecondaryPart = ($tplData['templateSecondaryPart'] ?? '');
/** @var array<string,mixed> */
$templateSecondaryArgs = ($tplData['templateSecondaryArgs'] ?? []);
/** @var string */
$innerPage = $tplData['currentInnerPage'];


$items        = [];
$item         = new SubMenuItem(
    PackagesPageController::LIST_INNER_PAGE_LIST,
    __('Backups', 'gamb-duplica-wp'),
    '',
    true
);
$item->link   = PackagesPageController::getInstance()->getMenuLink();
$item->active = ($innerPage == PackagesPageController::LIST_INNER_PAGE_LIST);
$items[]      = $item;

$item         = new SubMenuItem(
    PackagesPageController::LIST_INNER_PAGE_DETAILS,
    __('Details', 'gamb-duplica-wp'),
    '',
    true
);
$item->link   = PackagesPageController::getInstance()->getPackageDetailsUrl($package->ID);
$item->active = ($innerPage == PackagesPageController::LIST_INNER_PAGE_DETAILS);
$items[]      = $item;

$item         = new SubMenuItem(
    PackagesPageController::LIST_INNER_PAGE_TRANSFER,
    __('Transfer', 'gamb-duplica-wp'),
    '',
    CapMng::can(CapMng::CAP_CREATE, false)
);
$item->link   = PackagesPageController::getInstance()->getPackageTransferUrl($package->ID);
$item->active = ($innerPage == PackagesPageController::LIST_INNER_PAGE_TRANSFER);
$items[]      = $item;
?>

<div class="dup-body-header">
    <?php
    $tplMng->render('parts/tabs_menu_l2', ['menuItemsL2' => $items]);

    if (strlen($templateSecondaryPart) > 0) {
        $tplMng->render($templateSecondaryPart, $templateSecondaryArgs);
    }
    ?>
</div>
<hr class="wp-header-end margin-top-0">

<h1>
    <?php echo esc_html($pageTitle); ?>
</h1>
<?php if ($package->Status == GAMB_DUPLICA_WP_PackageStatus::ERROR) { ?>
<div id='dpro-error' class="error">
    <p>
        <b>
            <?php
                printf(
                    esc_html_x(
                        'Error encountered building Backup, please review %1$sBackup log%2$s for details.',
                        '1 and 2 are opening and closing anchor tags',
                        'gamb-duplica-wp'
                    ),
                    '<a target="_blank" href="' . esc_url($package->get_log_url()) . '">',
                    '</a>'
                );
            ?> 
        </b>
        <br/>
        <?php
            printf(
                esc_html_x(
                    'For more help read the %1$sFAQ pages%2$s or submit a %3$shelp ticket%4$s.',
                    '1 and 3 are opening, 2 and 4 are closing anchor/link tags',
                    'gamb-duplica-wp'
                ),
                '<a target="_blank" href="' . esc_url(GambDuplicaWP_PRO_TECH_FAQ_URL) . '">',
                '</a>',
                '<a target="_blank" href="' . esc_url(GambDuplicaWP_PRO_BLOG_URL . 'my-account/support/') . '">',
                '</a>'
            );
        ?>
    </p>
</div>
    <?php
}

$alertTransferDisabled          = new GAMB_DUPLICA_WP_UI_Dialog();
$alertTransferDisabled->title   = __('Transfer Error', 'gamb-duplica-wp');
$alertTransferDisabled->message = __('No Backup in default location so transfer is disabled.', 'gamb-duplica-wp');
$alertTransferDisabled->initAlert();
?>
<script>
    DupPro.Pack.TransferDisabled = function() {
        <?php $alertTransferDisabled->showAlert(); ?>
    }
</script>