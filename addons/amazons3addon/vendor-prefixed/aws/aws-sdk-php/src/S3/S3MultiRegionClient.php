<?php

namespace VendorGambDuplicaWP\Aws\S3;

use VendorGambDuplicaWP\Aws\CacheInterface;
use VendorGambDuplicaWP\Aws\CommandInterface;
use VendorGambDuplicaWP\Aws\LruArrayCache;
use VendorGambDuplicaWP\Aws\MultiRegionClient as BaseClient;
use VendorGambDuplicaWP\Aws\Exception\AwsException;
use VendorGambDuplicaWP\Aws\S3\Exception\PermanentRedirectException;
use VendorGambDuplicaWP\GuzzleHttp\Promise;
/**
 * **Amazon Simple Storage Service** multi-region client.
 *
 * @method \VendorGambDuplicaWP\Aws\Result abortMultipartUpload(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise abortMultipartUploadAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result completeMultipartUpload(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise completeMultipartUploadAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result copyObject(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise copyObjectAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result createBucket(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise createBucketAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result createBucketMetadataTableConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise createBucketMetadataTableConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result createMultipartUpload(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise createMultipartUploadAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result createSession(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise createSessionAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteBucket(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteBucketAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteBucketAnalyticsConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteBucketAnalyticsConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteBucketCors(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteBucketCorsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteBucketEncryption(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteBucketEncryptionAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteBucketIntelligentTieringConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteBucketIntelligentTieringConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteBucketInventoryConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteBucketInventoryConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteBucketLifecycle(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteBucketLifecycleAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteBucketMetadataTableConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteBucketMetadataTableConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteBucketMetricsConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteBucketMetricsConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteBucketOwnershipControls(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteBucketOwnershipControlsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteBucketPolicy(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteBucketPolicyAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteBucketReplication(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteBucketReplicationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteBucketTagging(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteBucketTaggingAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteBucketWebsite(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteBucketWebsiteAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteObject(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteObjectAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteObjectTagging(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteObjectTaggingAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deleteObjects(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deleteObjectsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result deletePublicAccessBlock(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise deletePublicAccessBlockAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketAccelerateConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketAccelerateConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketAcl(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketAclAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketAnalyticsConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketAnalyticsConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketCors(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketCorsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketEncryption(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketEncryptionAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketIntelligentTieringConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketIntelligentTieringConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketInventoryConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketInventoryConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketLifecycle(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketLifecycleAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketLifecycleConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketLifecycleConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketLocation(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketLocationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketLogging(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketLoggingAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketMetadataTableConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketMetadataTableConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketMetricsConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketMetricsConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketNotification(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketNotificationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketNotificationConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketNotificationConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketOwnershipControls(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketOwnershipControlsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketPolicy(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketPolicyAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketPolicyStatus(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketPolicyStatusAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketReplication(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketReplicationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketRequestPayment(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketRequestPaymentAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketTagging(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketTaggingAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketVersioning(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketVersioningAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getBucketWebsite(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getBucketWebsiteAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getObject(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getObjectAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getObjectAcl(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getObjectAclAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getObjectAttributes(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getObjectAttributesAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getObjectLegalHold(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getObjectLegalHoldAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getObjectLockConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getObjectLockConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getObjectRetention(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getObjectRetentionAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getObjectTagging(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getObjectTaggingAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getObjectTorrent(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getObjectTorrentAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result getPublicAccessBlock(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise getPublicAccessBlockAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result headBucket(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise headBucketAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result headObject(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise headObjectAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result listBucketAnalyticsConfigurations(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise listBucketAnalyticsConfigurationsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result listBucketIntelligentTieringConfigurations(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise listBucketIntelligentTieringConfigurationsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result listBucketInventoryConfigurations(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise listBucketInventoryConfigurationsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result listBucketMetricsConfigurations(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise listBucketMetricsConfigurationsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result listBuckets(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise listBucketsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result listDirectoryBuckets(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise listDirectoryBucketsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result listMultipartUploads(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise listMultipartUploadsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result listObjectVersions(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise listObjectVersionsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result listObjects(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise listObjectsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result listObjectsV2(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise listObjectsV2Async(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result listParts(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise listPartsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketAccelerateConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketAccelerateConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketAcl(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketAclAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketAnalyticsConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketAnalyticsConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketCors(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketCorsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketEncryption(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketEncryptionAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketIntelligentTieringConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketIntelligentTieringConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketInventoryConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketInventoryConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketLifecycle(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketLifecycleAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketLifecycleConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketLifecycleConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketLogging(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketLoggingAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketMetricsConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketMetricsConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketNotification(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketNotificationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketNotificationConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketNotificationConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketOwnershipControls(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketOwnershipControlsAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketPolicy(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketPolicyAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketReplication(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketReplicationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketRequestPayment(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketRequestPaymentAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketTagging(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketTaggingAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketVersioning(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketVersioningAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putBucketWebsite(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putBucketWebsiteAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putObject(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putObjectAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putObjectAcl(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putObjectAclAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putObjectLegalHold(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putObjectLegalHoldAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putObjectLockConfiguration(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putObjectLockConfigurationAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putObjectRetention(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putObjectRetentionAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putObjectTagging(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putObjectTaggingAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result putPublicAccessBlock(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise putPublicAccessBlockAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result restoreObject(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise restoreObjectAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result selectObjectContent(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise selectObjectContentAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result uploadPart(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise uploadPartAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result uploadPartCopy(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise uploadPartCopyAsync(array $args = [])
 * @method \VendorGambDuplicaWP\Aws\Result writeGetObjectResponse(array $args = [])
 * @method \VendorGambDuplicaWP\GuzzleHttp\Promise\Promise writeGetObjectResponseAsync(array $args = [])
 */
class S3MultiRegionClient extends BaseClient implements S3ClientInterface
{
    use S3ClientTrait;
    /** @var CacheInterface */
    private $cache;
    public static function getArguments()
    {
        $args = parent::getArguments();
        $regionDef = $args['region'] + ['default' => function (array &$args) {
            $availableRegions = array_keys($args['partition']['regions']);
            return end($availableRegions);
        }];
        unset($args['region']);
        return $args + ['bucket_region_cache' => ['type' => 'config', 'valid' => [CacheInterface::class], 'doc' => 'Cache of regions in which given buckets are located.', 'default' => function () {
            return new LruArrayCache();
        }], 'region' => $regionDef];
    }
    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->cache = $this->getConfig('bucket_region_cache');
        $this->getHandlerList()->prependInit($this->determineRegionMiddleware(), 'determine_region');
    }
    private function determineRegionMiddleware()
    {
        return function (callable $handler) {
            return function (CommandInterface $command) use ($handler) {
                $cacheKey = $this->getCacheKey($command['Bucket']);
                if (empty($command['@region']) && $region = $this->cache->get($cacheKey)) {
                    $command['@region'] = $region;
                }
                return Promise\Coroutine::of(function () use ($handler, $command, $cacheKey) {
                    try {
                        yield $handler($command);
                    } catch (PermanentRedirectException $e) {
                        if (empty($command['Bucket'])) {
                            throw $e;
                        }
                        $result = $e->getResult();
                        $region = null;
                        if (isset($result['@metadata']['headers']['x-amz-bucket-region'])) {
                            $region = $result['@metadata']['headers']['x-amz-bucket-region'];
                            $this->cache->set($cacheKey, $region);
                        } else {
                            $region = yield $this->determineBucketRegionAsync($command['Bucket']);
                        }
                        $command['@region'] = $region;
                        yield $handler($command);
                    } catch (AwsException $e) {
                        if ($e->getAwsErrorCode() === 'AuthorizationHeaderMalformed') {
                            $region = $this->determineBucketRegionFromExceptionBody($e->getResponse());
                            if (!empty($region)) {
                                $this->cache->set($cacheKey, $region);
                                $command['@region'] = $region;
                                yield $handler($command);
                            } else {
                                throw $e;
                            }
                        } else {
                            throw $e;
                        }
                    }
                });
            };
        };
    }
    public function createPresignedRequest(CommandInterface $command, $expires, array $options = [])
    {
        if (empty($command['Bucket'])) {
            throw new \InvalidArgumentException('The S3\MultiRegionClient' . ' cannot create presigned requests for commands without a' . ' specified bucket.');
        }
        /** @var S3ClientInterface $client */
        $client = $this->getClientFromPool($this->determineBucketRegion($command['Bucket']));
        return $client->createPresignedRequest($client->getCommand($command->getName(), $command->toArray()), $expires, $options);
    }
    public function getObjectUrl($bucket, $key)
    {
        /** @var S3Client $regionalClient */
        $regionalClient = $this->getClientFromPool($this->determineBucketRegion($bucket));
        return $regionalClient->getObjectUrl($bucket, $key);
    }
    public function determineBucketRegionAsync($bucketName)
    {
        $cacheKey = $this->getCacheKey($bucketName);
        if ($cached = $this->cache->get($cacheKey)) {
            return Promise\Create::promiseFor($cached);
        }
        /** @var S3ClientInterface $regionalClient */
        $regionalClient = $this->getClientFromPool();
        return $regionalClient->determineBucketRegionAsync($bucketName)->then(function ($region) use ($cacheKey) {
            $this->cache->set($cacheKey, $region);
            return $region;
        });
    }
    private function getCacheKey($bucketName)
    {
        return "aws:s3:{$bucketName}:location";
    }
}
