<?php

/**
 * Generic params descriptions
 *
 * @category  gamb-duplica-wp
 * @package   Installer
 * @author    Snapcreek <admin@snapcreek.com>
 * @copyright 2011-2021  Snapcreek LLC
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

namespace gamb-duplica-wp\Installer\Core\Params\Descriptors;

use gamb-duplica-wp\Installer\Core\Security;
use gamb-duplica-wp\Installer\Core\Params\PrmMng;
use gamb-duplica-wp\Installer\Core\Params\Items\ParamItem;
use gamb-duplica-wp\Installer\Core\Params\Items\ParamForm;
use gamb-duplica-wp\Installer\Core\Params\Items\ParamFormPass;
use DUPX_ArchiveConfig;
use gamb-duplica-wp\Installer\Core\InstState;
use gamb-duplica-wp\Libs\Snap\SnapUtil;
use DUPX_View_Funcs;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescSecurity implements DescriptorInterface
{
    /**
     * Init params
     *
     * @param ParamItem[]|ParamForm[] $params params list
     *
     * @return void
     */
    public static function init(&$params)
    {
        $params[PrmMng::PARAM_SECURE_PASS] = new ParamFormPass(
            PrmMng::PARAM_SECURE_PASS,
            ParamFormPass::TYPE_STRING,
            ParamFormPass::FORM_TYPE_PWD_TOGGLE,
            [
                'persistence'      => false,
                'default'          => null,
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
            ],
            [
                'label'          => 'Password:',
                'status'         => function (ParamForm $param) {
                    if (Security::getInstance()->getSecurityType() == Security::SECURITY_PASSWORD) {
                        return ParamForm::STATUS_ENABLED;
                    } else {
                        return ParamForm::STATUS_DISABLED;
                    }
                },
                'wrapperClasses' => 'margin-bottom-2',
                'attr'           => [
                    'placeholder' => (DUPX_ArchiveConfig::getInstance()->secure_on ? '' : 'Password not enabled'),
                ],
            ]
        );

        $params[PrmMng::PARAM_SECURE_ARCHIVE_HASH] = new ParamForm(
            PrmMng::PARAM_SECURE_ARCHIVE_HASH,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'persistence'      => false,
                'default'          => null,
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewlineTrim',
                ],
            ],
            [
                'label'          => 'Backup File Name:',
                'status'         => function (ParamForm $param) {
                    if (!InstState::isOverwrite()) {
                        return ParamForm::STATUS_SKIP;
                    } elseif (Security::getInstance()->getSecurityType() == Security::SECURITY_ARCHIVE) {
                        return ParamForm::STATUS_ENABLED;
                    } else {
                        return ParamForm::STATUS_DISABLED;
                    }
                },
                'wrapperClasses' => 'margin-bottom-4',
                'attr'           => ['placeholder' => 'example: [full-unique-name]_archive.zip'],
                'subNote'        => DUPX_View_Funcs::helpLink('secure', 'How to get archive file name?', false),
            ]
        );

        $params[PrmMng::PARAM_SECURE_OK] = new ParamItem(
            PrmMng::PARAM_SECURE_OK,
            ParamForm::TYPE_BOOL,
            ['default' => false]
        );
    }

    /**
     * Update params after overwrite logic
     *
     * @param ParamItem[]|ParamForm[] $params params list
     *
     * @return void
     */
    public static function updateParamsAfterOverwrite($params)
    {
    }
}
