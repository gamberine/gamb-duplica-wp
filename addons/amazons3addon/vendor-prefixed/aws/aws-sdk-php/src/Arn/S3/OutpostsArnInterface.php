<?php

namespace VendorGambDuplicaWP\Aws\Arn\S3;

use VendorGambDuplicaWP\Aws\Arn\ArnInterface;
/**
 * @internal
 */
interface OutpostsArnInterface extends ArnInterface
{
    public function getOutpostId();
}
