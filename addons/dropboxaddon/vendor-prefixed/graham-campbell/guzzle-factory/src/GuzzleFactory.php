<?php

declare (strict_types=1);
/*
 * This file is part of Guzzle Factory.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace VendorGambDuplicaWP\GrahamCampbell\GuzzleFactory;

use VendorGambDuplicaWP\GuzzleHttp\BodySummarizer;
use VendorGambDuplicaWP\GuzzleHttp\Client;
use VendorGambDuplicaWP\GuzzleHttp\Exception\ConnectException;
use VendorGambDuplicaWP\GuzzleHttp\Exception\TransferException;
use VendorGambDuplicaWP\GuzzleHttp\HandlerStack;
use VendorGambDuplicaWP\GuzzleHttp\Middleware;
use VendorGambDuplicaWP\GuzzleHttp\Utils;
use VendorGambDuplicaWP\Psr\Http\Message\RequestInterface;
use VendorGambDuplicaWP\Psr\Http\Message\ResponseInterface;
/**
 * This is the guzzle factory class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class GuzzleFactory
{
    /**
     * The default connect timeout.
     *
     * @var int
     */
    const CONNECT_TIMEOUT = 10;
    /**
     * The default transport timeout.
     *
     * @var int
     */
    const TIMEOUT = 15;
    /**
     * The default backoff multiplier.
     *
     * @var int
     */
    const BACKOFF = 1000;
    /**
     * The default 4xx retry codes.
     *
     * @var int[]
     */
    const CODES = [429];
    /**
     * The default amount of retries.
     */
    const RETRIES = 3;
    /**
     * Create a new guzzle client.
     *
     * @param array      $options
     * @param int|null   $backoff
     * @param int[]|null $codes
     * @param int|null   $retries
     *
     * @return \VendorGambDuplicaWP\GuzzleHttp\Client
     */
    public static function make(array $options = [], ?int $backoff = null, ?array $codes = null, ?int $retries = null)
    {
        $config = array_merge(['connect_timeout' => self::CONNECT_TIMEOUT, 'timeout' => self::TIMEOUT], $options);
        $config['handler'] = self::handler($backoff, $codes, $retries, $options['handler'] ?? null);
        return new Client($config);
    }
    /**
     * Create a new retrying handler stack.
     *
     * @param int|null                      $backoff
     * @param int[]|null                    $codes
     * @param int|null                      $retries
     * @param \VendorGambDuplicaWP\GuzzleHttp\HandlerStack|null $stack
     *
     * @return \VendorGambDuplicaWP\GuzzleHttp\HandlerStack
     */
    public static function handler(?int $backoff = null, ?array $codes = null, ?int $retries = null, ?HandlerStack $stack = null)
    {
        $stack = $stack ?? self::innerHandler();
        if ($retries === 0) {
            return $stack;
        }
        $stack->push(self::createRetryMiddleware($backoff ?? self::BACKOFF, $codes ?? self::CODES, $retries ?? self::RETRIES), 'retry');
        return $stack;
    }
    /**
     * Create a new handler stack.
     *
     * @param callable|null $handler
     *
     * @return \VendorGambDuplicaWP\GuzzleHttp\HandlerStack
     */
    public static function innerHandler(?callable $handler = null): HandlerStack
    {
        $stack = new HandlerStack($handler ?? Utils::chooseHandler());
        $stack->push(Middleware::httpErrors(new BodySummarizer(250)), 'http_errors');
        $stack->push(Middleware::redirect(), 'allow_redirects');
        $stack->push(Middleware::cookies(), 'cookies');
        $stack->push(Middleware::prepareBody(), 'prepare_body');
        return $stack;
    }
    /**
     * Create a new retry middleware.
     *
     * @param int   $backoff
     * @param int[] $codes
     * @param int   $maxRetries
     *
     * @return callable
     */
    private static function createRetryMiddleware(int $backoff, array $codes, int $maxRetries): callable
    {
        return Middleware::retry(function ($retries, RequestInterface $request, ?ResponseInterface $response = null, ?TransferException $exception = null) use ($codes, $maxRetries) {
            return $retries < $maxRetries && ($exception instanceof ConnectException || $response && ($response->getStatusCode() >= 500 || in_array($response->getStatusCode(), $codes, \true)));
        }, function ($retries) use ($backoff) {
            return (int) pow(2, $retries) * $backoff;
        });
    }
}
