<?php

namespace VendorGambDuplicaWP\Psr\Http\Client;

use VendorGambDuplicaWP\Psr\Http\Message\RequestInterface;
use VendorGambDuplicaWP\Psr\Http\Message\ResponseInterface;
interface ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \VendorGambDuplicaWP\Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface;
}
