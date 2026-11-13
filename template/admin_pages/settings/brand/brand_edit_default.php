<?php

/**
 * @package gamb-duplica-wp
 */

defined("ABSPATH") or die("");
use GambDuplicaWP\Models\BrandEntity;

/**
 * Variables
 *
 * @var gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var gamb-duplica-wp\Models\BrandEntity $brand
 */
$brand = $tplData['brand'];

?>
<label class="lbl-larger" >
    <?php esc_html_e("Name", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <?php echo esc_html($brand->name); ?>
</div>

<label class="lbl-larger" >
    <?php esc_html_e("Notes", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <?php echo esc_html($brand->notes); ?>
</div>

<label class="lbl-larger" >
    <?php esc_html_e("Logo", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <div class="width-xlarge" >
        <div class="style-guide-link float-right">
            <a href="javascript:void(0)" class="button secondary hollow small" onclick="DupPro.Brand.ShowStyleGuide();">
                <?php esc_html_e("Style Guide", 'gamb-duplica-wp'); ?>
            </a>
        </div>
        <textarea id="brand-default-logo" readonly="true"><?php echo esc_html($brand->logo); ?></textarea>
    </div>
</div>

<label class="lbl-larger" >
    <?php esc_html_e("Activation", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1 width-xlarge" >
    <?php esc_html_e(
        "This brand can be activated by using the installer brand drop-down during the Backup creation process. 
        It can also be set via a template.",
        'gamb-duplica-wp'
    ); ?>
</div>

<i><?php esc_html_e("The default brand cannot be changed", 'gamb-duplica-wp'); ?></i>
