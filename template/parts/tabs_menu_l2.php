<?php

/**
 * gamb-duplica-wp page header
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Core\Controllers\SubMenuItem;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

if (empty($tplData['menuItemsL2'])) {
    return;
}

/** @var SubMenuItem[] */ // @phpstan-ignore varTag.nativeType
$items = $tplData['menuItemsL2'];

foreach ($items as $item) {
    $id      = 'dup-submenu-l2-' . $tplData['currentLevelSlugs'][0] . '-' . $item->slug;
    $classes = [
        'dup-submenu-l2',
        'dup-nav-item',
    ];
    if ($item->active) {
        $classes[] = 'active';
    }

    $attrString = [];
    foreach ($item->attributes as $key => $value) {
        $attrString[] = $key . '="' . esc_attr($value) . '"';
    }
    if (count($attrString) > 0) {
        $attrString = implode(' ', $attrString);
    } else {
        $attrString = '';
    }

    if (strlen($item->link) > 0) {
        ?>
        <a 
            href="<?php echo esc_url($item->link); ?>" 
            id="<?php echo esc_attr($id); ?>" 
            class="<?php echo esc_attr(implode(' ', $classes)); ?>" 
            <?php
            echo $attrString; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
        >
            <?php echo esc_html($item->label); ?>
        </a>
        <?php
    } else {
        ?>
        <span 
            id="<?php echo esc_attr($id); ?>" 
            class="<?php echo esc_attr(implode(' ', $classes)); ?>"
            <?php
            echo $attrString; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
        >
            <?php echo esc_html($item->label); ?>
        </span>
        <?php
    }
}

