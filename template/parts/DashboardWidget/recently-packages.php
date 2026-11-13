<?php

/**
 * gamb-duplica-wp Backup row in table Backups list
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use gamb-duplica-wp\Controllers\PackagesPageController;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var GAMB_DUPLICA_WP_Package[] $packages
 */
$packages = $tplData['packages'];

?>
<hr class="separator" >
<div class="dup-section-last-packages">
    <p>
        <b><?php esc_html_e('Recently Backups', 'gamb-duplica-wp'); ?></b>
    </p>
    <?php if (count($packages) > 0) { ?>
    <ul>
        <?php foreach ($packages as $package) {
            $createdTime  = strtotime($package->getCreated());
            $createdDate  = date_i18n(get_option('date_format'), $createdTime);
            $createdHours = date_i18n(get_option('time_format'), $createdTime);

            ?>
            <li>
                <a href="<?php echo esc_url(PackagesPageController::getInstance()->getPackageDetailsURL($package->ID)); ?>">
                    <?php echo esc_html($package->getName()); ?>
                </a> - <i class="gary" ><?php echo esc_html($createdDate . ' ' .  $createdHours); ?></i>
            </li>
        <?php } ?>
    </ul>
    <?php } ?>
    <p class="dup-packages-counts">
        <?php printf(esc_html__('Backups: %1$d, Failures: %2$d', 'gamb-duplica-wp'), (int) $tplData['totalPackages'], (int) $tplData['totalFailures']); ?>
    </p>
</div>