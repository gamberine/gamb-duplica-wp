<?php

/**
 * License class
 *
 * @category  gamb-duplica-wp
 * @package   Installer
 * @author    Snapcreek <admin@snapcreek.com>
 * @copyright 2011-2021  Snapcreek LLC
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

namespace gamb-duplica-wp\Installer\Addons\ProBase;

use gamb-duplica-wp\Installer\Core\Params\Descriptors\DescriptorInterface;
use gamb-duplica-wp\Installer\Core\Params\Items\ParamForm;
use gamb-duplica-wp\Installer\Core\Params\PrmMng;
use DUPX_U;

class AdvancedParams implements DescriptorInterface
{
    /**
     * Init advanced params
     *
     * @param \gamb-duplica-wp\Installer\Core\Params\Items\ParamItem[] $params params list
     *
     * @return void
     */
    public static function init(&$params)
    {
        $params[PrmMng::PARAM_GEN_WP_AUTH_KEY] = new ParamForm(
            PrmMng::PARAM_GEN_WP_AUTH_KEY,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => false],
            [
                'label'         => 'Auth Keys:',
                'checkboxLabel' => 'Generate New Unique Authentication Keys and Salts',
                'status'        => (License::can(License::CAPABILITY_UPDATE_AUTH) ? ParamForm::STATUS_ENABLED : ParamForm::STATUS_DISABLED),
                'subNote'       => (License::can(License::CAPABILITY_UPDATE_AUTH) ? '' : License::getLicenseUpdateText()),
            ]
        );
    }

    /**
     * Update params after overwrite
     *
     * @param \gamb-duplica-wp\Installer\Core\Params\Items\ParamItem[] $params params list
     *
     * @return void
     */
    public static function updateParamsAfterOverwrite($params)
    {
    }
}
