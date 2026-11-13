<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Ajax;

abstract class AbstractAjaxService
{
    /**
     * Init ajax calls
     *
     * @return void
     */
    abstract public function init();

    /**
     * Add ajax action
     *
     * @param string $tag        ajax tag name
     * @param string $methodName method name
     *
     * @return bool Always returns true
     */
    protected function addAjaxCall($tag, $methodName)
    {
        return add_action($tag, [$this, $methodName]);
    }
}
