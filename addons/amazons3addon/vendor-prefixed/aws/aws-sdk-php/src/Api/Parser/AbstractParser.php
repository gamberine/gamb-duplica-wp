<?php

namespace VendorGambDuplicaWP\Aws\Api\Parser;

use VendorGambDuplicaWP\Aws\Api\Service;
use VendorGambDuplicaWP\Aws\Api\StructureShape;
use VendorGambDuplicaWP\Aws\CommandInterface;
use VendorGambDuplicaWP\Aws\ResultInterface;
use VendorGambDuplicaWP\Psr\Http\Message\ResponseInterface;
use VendorGambDuplicaWP\Psr\Http\Message\StreamInterface;
/**
 * @internal
 */
abstract class AbstractParser
{
    /** @var \VendorGambDuplicaWP\Aws\Api\Service Representation of the service API*/
    protected $api;
    /** @var callable */
    protected $parser;
    /**
     * @param Service $api Service description.
     */
    public function __construct(Service $api)
    {
        $this->api = $api;
    }
    /**
     * @param CommandInterface  $command  Command that was executed.
     * @param ResponseInterface $response Response that was received.
     *
     * @return ResultInterface
     */
    abstract public function __invoke(CommandInterface $command, ResponseInterface $response);
    abstract public function parseMemberFromStream(StreamInterface $stream, StructureShape $member, $response);
}
