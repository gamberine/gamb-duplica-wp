<?php

/**
 * Exception for 416 Requested Range Not Satisfiable responses
 *
 * @package Requests\Exceptions
 */
namespace VendorGambDuplicaWP\WpOrg\Requests\Exception\Http;

use VendorGambDuplicaWP\WpOrg\Requests\Exception\Http;
/**
 * Exception for 416 Requested Range Not Satisfiable responses
 *
 * @package Requests\Exceptions
 */
final class Status416 extends Http
{
    /**
     * HTTP status code
     *
     * @var integer
     */
    protected $code = 416;
    /**
     * Reason phrase
     *
     * @var string
     */
    protected $reason = 'Requested Range Not Satisfiable';
}
