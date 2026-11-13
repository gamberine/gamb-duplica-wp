<?php

/**
 * Schedule page controller
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Controllers;

use GAMB_DUPLICA_WP_Schedule_Entity;
use gamb-duplica-wp\Addons\ProBase\License\License;
use gamb-duplica-wp\Addons\ProBase\License\LicenseNotices;
use gamb-duplica-wp\Core\CapMng;
use gamb-duplica-wp\Core\Controllers\ControllersManager;
use gamb-duplica-wp\Core\Controllers\AbstractMenuPageController;
use gamb-duplica-wp\Core\Controllers\PageAction;
use gamb-duplica-wp\Core\Upgrade\UpgradePlugin;
use gamb-duplica-wp\Core\Views\TplMng;
use gamb-duplica-wp\Libs\Snap\SnapUtil;

class SchedulePageController extends AbstractMenuPageController
{
    const INNER_PAGE_LIST = 'schedules';
    const INNER_PAGE_EDIT = 'edit';

    /*
     * action types
     */
    const ACTION_EDIT_SAVE = 'save';
    const ACTION_EDIT_COPY = 'copy-schedule';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->parentSlug   = ControllersManager::MAIN_MENU_SLUG;
        $this->pageSlug     = ControllersManager::SCHEDULES_SUBMENU_SLUG;
        $this->pageTitle    = __('Schedule Backups', 'gamb-duplica-wp');
        $this->menuLabel    = __('Schedule Backups', 'gamb-duplica-wp');
        $this->capatibility = CapMng::CAP_SCHEDULE;
        $this->menuPos      = 30;

        add_filter('gamb-duplica-wp_page_actions_' . $this->pageSlug, [$this, 'pageActions']);
        add_filter('gamb-duplica-wp_page_template_data_' . $this->pageSlug, [$this, 'updatePackagePageTitle']);
        add_action('gamb-duplica-wp_render_page_content_' . $this->pageSlug, [$this, 'renderContent'], 10, 2);
        add_action('gamb-duplica-wp_before_render_page_' . $this->pageSlug, [$this, 'beforeRenderPage'], 10, 2);
    }

    /**
     * Return actions for current page
     *
     * @param PageAction[] $actions actions lists
     *
     * @return PageAction[]
     */
    public function pageActions($actions)
    {
        $actions[] = new PageAction(
            self::ACTION_EDIT_SAVE,
            [
                $this,
                'saveSchedule',
            ],
            [$this->pageSlug],
            self::INNER_PAGE_EDIT
        );
        $actions[] = new PageAction(
            self::ACTION_EDIT_COPY,
            [
                $this,
                'copySchedule',
            ],
            [$this->pageSlug],
            self::INNER_PAGE_EDIT
        );
        return $actions;
    }

    /**
     * Set Backup object before render pages
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return void
     */
    public function beforeRenderPage($currentLevelSlugs, $innerPage)
    {
        TplMng::getInstance()->setGlobalValue('blur', !License::can(License::CAPABILITY_SCHEDULE));
    }

    /**
     * Set Backup page title
     *
     * @param array<string, mixed> $tplData template global data
     *
     * @return array<string, mixed>
     */
    public function updatePackagePageTitle($tplData)
    {
        switch ($this->getCurrentInnerPage()) {
            case self::INNER_PAGE_EDIT:
                break;
            case self::INNER_PAGE_LIST:
            default:
                $tplData['pageTitle']             =  __('Schedule Backup', 'gamb-duplica-wp');
                $tplData['templateSecondaryPart'] = 'admin_pages/schedules/schedule_create_button';
                break;
        }
        return $tplData;
    }

    /**
     * Save schedule
     *
     * @return array<string, mixed>
     */
    public function saveSchedule(): array
    {
        $result = [
            'saveSuccess'      => false,
            'actionScheduleId' => SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'schedule_id', -1),
        ];

        $schedule_id = $result['actionScheduleId'];
        if ($schedule_id == -1) {
            $schedule = new GAMB_DUPLICA_WP_Schedule_Entity();
        } else {
            $schedule = GAMB_DUPLICA_WP_Schedule_Entity::getById($schedule_id);
        }

        if ($schedule == false) {
            $result['errorMessage'] = __('Schedule not found', 'gamb-duplica-wp');
            return $result;
        }

        $schedule->setFromInput(SnapUtil::INPUT_REQUEST);
        if ($schedule->save() == false) {
            $result['errorMessage'] = __('Is not possible to update the schedule settings', 'gamb-duplica-wp');
        } else {
            $result['saveSuccess']    = true;
            $result['successMessage'] = __('Schedule saved', 'gamb-duplica-wp');
        }

        $result['actionScheduleId'] = $schedule->getId();

        return $result;
    }

    /**
     * Save schedule
     *
     * @return array<string, mixed>
     */
    public function copySchedule(): array
    {
        $result = [
            'saveSuccess'      => false,
            'actionScheduleId' => SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'schedule_id', -1),
        ];

        $schedule_id = $result['actionScheduleId'];
        $source_id   = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'duppro-source-schedule-id', -1);

        if ($source_id == -1 || !GAMB_DUPLICA_WP_Schedule_Entity::exists($source_id)) {
            $result['errorMessage'] = __('Schedule to copy not found', 'gamb-duplica-wp');
            return $result;
        }

        if ($schedule_id == -1) {
            $schedule = new GAMB_DUPLICA_WP_Schedule_Entity();
        } else {
            $schedule = GAMB_DUPLICA_WP_Schedule_Entity::getById($schedule_id);
        }

        if ($schedule == false) {
            $result['errorMessage'] = __('Schedule not found', 'gamb-duplica-wp');
            return $result;
        }

        $schedule->copy_from_source_id($source_id);
        if ($schedule->save() == false) {
            $result['errorMessage'] = __('Is not possible to update the schedule settings', 'gamb-duplica-wp');
        } else {
            $result['saveSuccess']    = true;
            $result['successMessage'] = __('Schedule copied.', 'gamb-duplica-wp');
        }

        $result['actionScheduleId'] = $schedule->getId();

        return $result;
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
        switch ($this->getCurrentInnerPage()) {
            case self::INNER_PAGE_EDIT:
                $scheduleId = TplMng::getInstance()->getGlobalValue(
                    'actionScheduleId',
                    SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'schedule_id', -1)
                );
                if ($scheduleId == -1) {
                    $schedule = new GAMB_DUPLICA_WP_Schedule_Entity();
                } else {
                    $schedule = GAMB_DUPLICA_WP_Schedule_Entity::getById($scheduleId);
                }
                TplMng::getInstance()->render(
                    'admin_pages/schedules/schedule_edit',
                    ['schedule' => $schedule]
                );
                break;
            case self::INNER_PAGE_LIST:
            default:
                TplMng::getInstance()->render('admin_pages/schedules/schedule_list');
                break;
        }
    }

    /**
     * Get schedule edit URL
     *
     * @return string
     */
    public function getEditBaseUrl()
    {
        return ControllersManager::getMenuLink(
            ControllersManager::SCHEDULES_SUBMENU_SLUG,
            null,
            null,
            [ControllersManager::QUERY_STRING_INNER_PAGE => 'edit']
        );
    }

    /**
     * Get schedule edit URL
     *
     * @param int $scheduleId schedule id, if -1 then new schedule will be created
     *
     * @return string
     */
    public function getEditUrl($scheduleId = -1)
    {
        return ControllersManager::getMenuLink(
            ControllersManager::SCHEDULES_SUBMENU_SLUG,
            null,
            null,
            [
                ControllersManager::QUERY_STRING_INNER_PAGE => 'edit',
                'schedule_id'                               => $scheduleId,
            ]
        );
    }

    /**
     * Get schedule copy action URL
     *
     * @param int $scheduleId schedule id, if -1 then new schedule will be created
     * @param int $sourceId   source schedule id, if -1 then source if ins't add on URL
     *
     * @return string
     */
    public function getCopyActionUrl($scheduleId = -1, $sourceId = -1)
    {

        $action    = $this->getActionByKey(self::ACTION_EDIT_COPY);
        $extraArgs = [
            ControllersManager::QUERY_STRING_INNER_PAGE => 'edit',
            'action'                                    => $action->getKey(),
            '_wpnonce'                                  => $action->getNonce(),
            'schedule_id'                               => $scheduleId,
        ];
        if ($sourceId > -1) {
            $extraArgs['duppro-source-schedule-id'] = $sourceId;
        }
        return ControllersManager::getMenuLink(
            ControllersManager::SCHEDULES_SUBMENU_SLUG,
            null,
            null,
            $extraArgs
        );
    }
}
