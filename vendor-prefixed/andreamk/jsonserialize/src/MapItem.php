<?php

/**
 * Map item class
 *
 * @package VendorGambDuplicaWP\Amk\JsonSerialize
 */
namespace VendorGambDuplicaWP\Amk\JsonSerialize;

/**
 * Map item element
 */
class MapItem
{
    /** @var ?string */
    public $type = null;
    /** @var self[] */
    public $childs = [];
}
