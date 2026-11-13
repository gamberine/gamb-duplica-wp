<?php

namespace VendorGambDuplicaWP\Aws\Arn\S3;

use VendorGambDuplicaWP\Aws\Arn\ArnInterface;
/**
 * @internal
 */
interface BucketArnInterface extends ArnInterface
{
    public function getBucketName();
}
