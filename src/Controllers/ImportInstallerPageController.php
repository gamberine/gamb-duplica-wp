<?php

/**
 * Impost installer page controller
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Controllers;

use GambDuplicaWP\Core\Bootstrap;
use GambDuplicaWP\Core\CapMng;
use GambDuplicaWP\Core\Controllers\ControllersManager;
use GambDuplicaWP\Core\Controllers\AbstractSinglePageController;
use GambDuplicaWP\Core\Views\TplMng;
use GambDuplicaWP\Libs\Snap\SnapUtil;
use GambDuplicaWP\Package\Import\PackageImporter;

class ImportInstallerPageController extends AbstractSinglePageController
{
    /** @var PackageImporter */
    protected static $importObj;
    /** @var string */
    protected static $iframeSrc;

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->pageSlug     = ControllersManager::IMPORT_INSTALLER_PAGE;
        $this->pageTitle    = __('Install Backup', 'gamb-duplica-wp');
        $this->capatibility = CapMng::CAP_IMPORT;

        add_action('gamb-duplica-wp_before_run_actions_' . $this->pageSlug, [$this, 'packageCheck']);
        add_action('gamb-duplica-wp_render_page_content_' . $this->pageSlug, [$this, 'renderContent'], 10, 2);
    }

    /**
     * Return true if current page is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return !((bool) GambDuplicaWP_PRO_DISALLOW_IMPORT); // @phpstan-ignore-line
    }

    /**
     * called on admin_print_styles-[page] hook
     *
     * @return void
     */
    public function pageStyles()
    {
        Bootstrap::enqueueStyles();
        wp_enqueue_style('dup-pro-import');
    }

    /**
     * called on admin_print_scripts-[page] hook
     *
     * @return void
     */
    public function pageScripts()
    {
        self::dequeueAllScripts();
        Bootstrap::enqueueScripts();
        wp_enqueue_script('dup-pro-import-installer');
    }

    /**
     * dequeue all scripts except jquery and dup-pro script
     *
     * @return boolean // false if scripts can't be dequeued
     */
    public static function dequeueAllScripts(): bool
    {

        if (!function_exists('wp_scripts')) {
            return false;
        }

        $scripts = wp_scripts();
        foreach ($scripts->registered as $handle => $script) {
            if (
                strpos($handle, 'jquery') === 0 ||
                strpos($handle, 'dup-pro') === 0
            ) {
                continue;
            }
            wp_dequeue_script($handle);
        }

        return true;
    }

    /**
     * Load import object and make a redirect if is a lite Backup
     *
     * @param array<string, string> $currentLevelSlugs current menu page
     *
     * @return void
     */
    public function packageCheck($currentLevelSlugs)
    {
        $archivePath     = SnapUtil::sanitizeDefaultInput(INPUT_GET, 'package');
        self::$importObj = new PackageImporter($archivePath);
        self::$iframeSrc = self::$importObj->prepareToInstall();

        /* uncomment this to enable installer on new page
        if (self::$importObj->isLite()) {
            wp_redirect(self::$iframeSrc);
            die;
        }*/
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
        $tplMng = TplMng::getInstance();
        $data   = $tplMng->getGlobalData();

        if ($data['actionsError']) {
            $tplMng->render('admin_pages/import/import-installer-error');
        } else {
            $tplMng->render(
                'admin_pages/import/import-installer',
                [
                    'importObj' => self::$importObj,
                    'iframeSrc' => self::$iframeSrc,
                ]
            );
        }
    }
}
