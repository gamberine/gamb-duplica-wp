<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Utils\Help\Help;
use GambDuplicaWP\Utils\Support\SupportToolkit;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<div id="gamb-duplica-wp-help-wrapper">
    <div id="gamb-duplica-wp-help-header">
        <img src="<?php echo esc_url(GambDuplicaWP_PRO_PLUGIN_URL . 'assets/img/gamb-duplica-wp-header-logo.svg'); ?>" />
    </div>
    <div id="gamb-duplica-wp-help-content">
        <div id="gamb-duplica-wp-help-search">
            <input type="text" placeholder="<?php esc_attr_e("Search", "gamb-duplica-wp"); ?>" />
            <ul id="gamb-duplica-wp-help-search-results"></ul>
            <div id="gamb-duplica-wp-help-search-results-empty"><?php esc_html_e("No results found", "gamb-duplica-wp"); ?></div>
        </div>
        <div id="gamb-duplica-wp-context-articles">
            <?php if (count(Help::getInstance()->getArticlesByTag($tplData['tag'])) > 0) : ?>
                <h2><?php esc_html_e("Related Articles", "gamb-duplica-wp"); ?></h2>
                <?php $tplMng->render('parts/help/article-list', ['articles' => Help::getInstance()->getArticlesByTag($tplData['tag'])]); ?>
            <?php endif; ?>
        </div>
        <div id="gamb-duplica-wp-help-categories">
            <?php $tplMng->render('parts/help/category-list', ['categories' => Help::getInstance()->getTopLevelCategories()]); ?>
        </div>
        <div id="gamb-duplica-wp-help-footer">
            <div class="gamb-duplica-wp-help-footer-block">
                <i aria-hidden="true" class="fa fa-file-alt"></i>
                <h3><?php esc_html_e("View Documentation", "gamb-duplica-wp"); ?></h3>
                <p>
                    <?php esc_html_e("Browse documentation, reference material, and tutorials for gamb-duplica-wp.", "gamb-duplica-wp"); ?>
                </p>
                <a 
                    href="<?php echo esc_url(GambDuplicaWP_PRO_BLOG_URL . 'docs'); ?>"
                    rel="noopener noreferrer" 
                    target="_blank" 
                    class="button">
                  <?php esc_html_e("View All Documentation", "gamb-duplica-wp"); ?>
                </a>
            </div>
            <div class="gamb-duplica-wp-help-footer-block">
                <i aria-hidden="true" class="fa fa-life-ring"></i>
                <h3><?php esc_html_e("Get Support", "gamb-duplica-wp"); ?></h3>
                <p>
                    <?php esc_html_e("You can access our world-class support below.", "gamb-duplica-wp"); ?>
                    <?php echo wp_kses(
                        sprintf(
                            _x(
                                'If reporting a bug, remember to include the %1$s to speed up the debugging process.',
                                '1: diagnostic data link with label or link to instructions to download logs manually',
                                'gamb-duplica-wp'
                            ),
                            SupportToolkit::getDiagnosticInfoLinks()
                        ),
                        [
                            'a' => [
                                'href'   => [],
                                'target' => [],
                            ],
                        ]
                    ); ?>
                </p>
                <a 
                    href="<?php echo esc_url(GambDuplicaWP_PRO_BLOG_URL . 'my-account/support/'); ?>"
                    rel="noopener noreferrer" 
                    target="_blank" 
                    class="button">
                    <?php esc_html_e("Get Support", "gamb-duplica-wp"); ?>
                </a>
            </div>
        </div>
    </div>
</div>
