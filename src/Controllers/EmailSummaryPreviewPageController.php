<?php

/**
 * Impost installer page controller
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Controllers;

use gamb-duplica-wp\Core\CapMng;
use gamb-duplica-wp\Core\Controllers\AbstractBlankPageController;
use gamb-duplica-wp\Core\Views\TplMng;
use gamb-duplica-wp\Utils\Email\EmailSummary;

class EmailSummaryPreviewPageController extends AbstractBlankPageController
{
    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->pageSlug     = EmailSummary::PREVIEW_SLUG;
        $this->capatibility = CapMng::CAP_SETTINGS;

        add_action('gamb-duplica-wp_render_page_content_' . $this->pageSlug, [$this, 'renderContent'], 10, 2);
    }

    /**
     * Render page content
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return void
     */
    public function renderContent($currentLevelSlugs, $innerPage)
    {
        TplMng::getInstance()->render('mail/email_summary', EmailSummary::getInstance()->getData());
    }
}
