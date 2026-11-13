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
    <input type="text" name="name" id="brand-name" value="<?php echo esc_attr($brand->name); ?>" data-parsley-required>
    <p class="description"><?php esc_html_e("Displayed as the page title of the installer.", 'gamb-duplica-wp'); ?></p>
</div>

<label class="lbl-larger" >
    <?php esc_html_e("Notes", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <textarea name="notes" id="brand-notes"><?php echo esc_html($brand->notes); ?></textarea>
</div>

<label class="lbl-larger" >
    <?php esc_html_e("Logo", 'gamb-duplica-wp'); ?>
</label>
<div class="margin-bottom-1" >
    <div class="width-xlarge" >
        <div class="style-guide-link">
            <a href="javascript:void(0)" class="button secondary hollow small" onclick="DupPro.Brand.ShowStyleGuide();">
                <?php esc_html_e("Style Guide", 'gamb-duplica-wp'); ?>
            </a>
        </div>
        <div class="brand-logo-editor">
            <?php
            wp_editor(
                $brand->logo,
                'brand-logo',
                [
                    'wpautop'           => true,
                    'media_buttons'     => true,
                    'textarea_name'     => 'logo',
                    'textarea_rows'     => 50,
                    'tabindex'          => '',
                    'tabfocus_elements' => ':prev,:next',
                    'editor_css'        => '',
                    'editor_class'      => 'required',
                    'teeny'             => false,
                    'dfw'               => false,
                    'tinymce'           => false,
                    'quicktags'         => ['buttons' => 'strong,em,i,ins,close,img,link'],
                ]
            );
            ?>
        </div>
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
