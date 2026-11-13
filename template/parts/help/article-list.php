<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Utils\Help\Article;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

/** @var Article[] $articles p*/
$articles = $tplData['articles'];
?>
<?php if (count($articles) > 0) : ?>
    <ul <?php echo isset($tplData['list_class']) ? 'class="' . esc_attr($tplData['list_class']) . '"' : ''; ?>>
        <?php foreach ($articles as $article) : ?>
            <li class="gamb-duplica-wp-help-article" data-id="<?php echo (int) $article->getId(); ?>">
                <i aria-hidden="true" class="fa fa-file-alt"></i>
                <a href="<?php echo esc_url($article->getLink()); ?>" target="_blank"><?php echo esc_html($article->getTitle()); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
