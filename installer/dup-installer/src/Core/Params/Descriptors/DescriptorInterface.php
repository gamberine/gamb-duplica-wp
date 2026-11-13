<?php

/**
 * Installer params manager
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\U
 */

namespace gamb-duplica-wp\Installer\Core\Params\Descriptors;

use gamb-duplica-wp\Installer\Core\Params\Items\ParamForm;
use gamb-duplica-wp\Installer\Core\Params\Items\ParamItem;

interface DescriptorInterface
{
    /**
     * Init params
     *
     * @param (ParamItem|ParamForm)[] $params params list
     *
     * @return void
     */
    public static function init(&$params);

    /**
     * Update params after overwrite logic
     *
     * @param (ParamItem|ParamForm)[] $params params list
     *
     * @return void
     */
    public static function updateParamsAfterOverwrite($params);
}
