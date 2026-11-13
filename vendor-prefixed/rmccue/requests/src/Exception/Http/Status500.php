<?php

/**
 * Exception for 500 Internal Server Error responses
 *
 * @package Requests\Exceptions
 */
namespace VendorGambDuplicaWP\WpOrg\Requests\Exception\Http;

use VendorGambDuplicaWP\WpOrg\Requests\Exception\Http;
/**
 * Exception for 500 Internal Server Error responses
 *
 * @package Requests\Exceptions
 */
final class Status500 extends Http
{
    /**
     * HTTP status code
     *
     * @var integer
     */
    protected $code = 500;
    /**
     * Reason phrase
     *
     * @var string
     */
    protected $reason = 'Internal Server Error';
}
