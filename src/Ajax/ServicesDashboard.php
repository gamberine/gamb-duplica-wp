<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Ajax;

use GAMB_DUPLICA_WP_Package;
use GambDuplicaWP\Ajax\AjaxWrapper;
use GambDuplicaWP\Core\CapMng;
use GambDuplicaWP\Libs\Snap\SnapUtil;
use GambDuplicaWP\Views\DashboardWidget;

class ServicesDashboard extends AbstractAjaxService
{
    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init()
    {
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_dashboad_widget_info', 'dashboardWidgetInfo');
        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_dismiss_recommended_plugin', 'dismissRecommendedPlugin');
    }

    /**
     * Set recovery callback
     *
     * @return array<string, mixed>
     */
    public static function dashboardWidgetInfoCallback()
    {
        return [
            'isRunning'      => GAMB_DUPLICA_WP_Package::isPackageRunning() || GAMB_DUPLICA_WP_Package::isPackageCancelling(),
            'lastBackupInfo' => DashboardWidget::getLastBackupString(),
        ];
    }

    /**
     * Set recovery action
     *
     * @return void
     */
    public function dashboardWidgetInfo()
    {
        AjaxWrapper::json(
            [
                self::class,
                'dashboardWidgetInfoCallback',
            ],
            'gamb-duplica-wp_pro_dashboad_widget_info',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }

    /**
     * Set dismiss recommended callback
     *
     * @return bool
     */
    public static function dismissRecommendedPluginCallback()
    {
        return (update_user_meta(get_current_user_id(), DashboardWidget::RECOMMENDED_PLUGIN_DISMISSED_OPT_KEY, true) !== false);
    }

    /**
     * Set recovery action
     *
     * @return void
     */
    public function dismissRecommendedPlugin()
    {
        AjaxWrapper::json(
            [
                self::class,
                'dismissRecommendedPluginCallback',
            ],
            'gamb-duplica-wp_pro_dashboad_widget_dismiss_recommended',
            SnapUtil::sanitizeTextInput(INPUT_POST, 'nonce'),
            CapMng::CAP_BASIC
        );
    }
}
