<?php

namespace VendorGambDuplicaWP\GuzzleHttp;

use VendorGambDuplicaWP\Psr\Http\Message\RequestInterface;
use VendorGambDuplicaWP\Psr\Http\Message\ResponseInterface;
interface MessageFormatterInterface
{
    /**
     * Returns a formatted message string.
     *
     * @param RequestInterface       $request  Request that was sent
     * @param ResponseInterface|null $response Response that was received
     * @param \Throwable|null        $error    Exception that was received
     */
    public function format(RequestInterface $request, ?ResponseInterface $response = null, ?\Throwable $error = null): string;
}
