<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Utils\Help\Category;
use GambDuplicaWP\Utils\Help\Help;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

/** @var Category[] $categories */
$categories = $tplData['categories'];
if (empty($categories)) : ?>
    <p>
        <?php
            printf(
                esc_html_x(
                    'Could not fetch help information. Please try again later or visit the %1$sonline docs%2$s.',
                    '%1$s and %2$s are the opening and closing tags for the link to the online docs.',
                    'gamb-duplica-wp'
                ),
                '<a href="' . esc_url(GambDuplicaWP_PRO_BLOG_URL . 'docs') . '" target="_blank">',
                '</a>'
            );
        ?>
    </p>
<?php else : ?>
    <ul class="gamb-duplica-wp-help-category-list">
    <?php foreach ($categories as $category) : ?>
        <li class="gamb-duplica-wp-help-category">
            <header>
                <i class="fa fa-folder-open"></i>
                <span><?php echo esc_html($category->getName()); ?></span>
                <i class="fa fa-angle-right"></i>
            </header>
            <?php if (count($category->getChildren()) > 0) { ?>
                <?php $tplMng->render('parts/help/category-list', ['categories' => $category->getChildren()]);
            } ?>
            <?php if ($category->getArticleCount() > 0) : ?>
                <?php $tplMng->render(
                    'parts/help/article-list',
                    [
                        'articles'   => Help::getInstance()->getArticlesByCategory($category->getId()),
                        'list_class' => 'gamb-duplica-wp-help-article-list',
                    ]
                ); ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>
