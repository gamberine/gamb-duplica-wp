<?php

/**
 * Exception for 412 Precondition Failed responses
 *
 * @package Requests\Exceptions
 */
namespace VendorGambDuplicaWP\WpOrg\Requests\Exception\Http;

use VendorGambDuplicaWP\WpOrg\Requests\Exception\Http;
/**
 * Exception for 412 Precondition Failed responses
 *
 * @package Requests\Exceptions
 */
final class Status412 extends Http
{
    /**
     * HTTP status code
     *
     * @var integer
     */
    protected $code = 412;
    /**
     * Reason phrase
     *
     * @var string
     */
    protected $reason = 'Precondition Failed';
}
