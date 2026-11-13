<?php

namespace VendorGambDuplicaWP;

if (\class_exists('VendorGambDuplicaWP\Google_Client', \false)) {
    // Prevent error with preloading in PHP 7.4
    // @see https://github.com/googleapis/google-api-php-client/issues/1976
    return;
}
$classMap = ['VendorGambDuplicaWP\Google\Client' => 'VendorGambDuplicaWP\\Google_Client', 'VendorGambDuplicaWP\Google\Service' => 'VendorGambDuplicaWP\\Google_Service', 'VendorGambDuplicaWP\Google\AccessToken\Revoke' => 'VendorGambDuplicaWP\\Google_AccessToken_Revoke', 'VendorGambDuplicaWP\Google\AccessToken\Verify' => 'VendorGambDuplicaWP\\Google_AccessToken_Verify', 'VendorGambDuplicaWP\Google\Model' => 'VendorGambDuplicaWP\\Google_Model', 'VendorGambDuplicaWP\Google\Utils\UriTemplate' => 'VendorGambDuplicaWP\\Google_Utils_UriTemplate', 'VendorGambDuplicaWP\Google\AuthHandler\Guzzle6AuthHandler' => 'VendorGambDuplicaWP\\Google_AuthHandler_Guzzle6AuthHandler', 'VendorGambDuplicaWP\Google\AuthHandler\Guzzle7AuthHandler' => 'VendorGambDuplicaWP\\Google_AuthHandler_Guzzle7AuthHandler', 'VendorGambDuplicaWP\Google\AuthHandler\AuthHandlerFactory' => 'VendorGambDuplicaWP\\Google_AuthHandler_AuthHandlerFactory', 'VendorGambDuplicaWP\Google\Http\Batch' => 'VendorGambDuplicaWP\\Google_Http_Batch', 'VendorGambDuplicaWP\Google\Http\MediaFileUpload' => 'VendorGambDuplicaWP\\Google_Http_MediaFileUpload', 'VendorGambDuplicaWP\Google\Http\REST' => 'VendorGambDuplicaWP\\Google_Http_REST', 'VendorGambDuplicaWP\Google\Task\Retryable' => 'VendorGambDuplicaWP\\Google_Task_Retryable', 'VendorGambDuplicaWP\Google\Task\Exception' => 'VendorGambDuplicaWP\\Google_Task_Exception', 'VendorGambDuplicaWP\Google\Task\Runner' => 'VendorGambDuplicaWP\\Google_Task_Runner', 'VendorGambDuplicaWP\Google\Collection' => 'VendorGambDuplicaWP\\Google_Collection', 'VendorGambDuplicaWP\Google\Service\Exception' => 'VendorGambDuplicaWP\\Google_Service_Exception', 'VendorGambDuplicaWP\Google\Service\Resource' => 'VendorGambDuplicaWP\\Google_Service_Resource', 'VendorGambDuplicaWP\Google\Exception' => 'VendorGambDuplicaWP\\Google_Exception'];
foreach ($classMap as $class => $alias) {
    \class_alias($class, $alias);
}
/**
 * This class needs to be defined explicitly as scripts must be recognized by
 * the autoloader.
 */
class Google_Task_Composer extends \VendorGambDuplicaWP\Google\Task\Composer
{
}
/**
 * This class needs to be defined explicitly as scripts must be recognized by
 * the autoloader.
 */
\class_alias('VendorGambDuplicaWP\Google_Task_Composer', 'VendorGambDuplicaWP\\Google_Task_Composer', \false);
/** @phpstan-ignore-next-line */
if (\false) {
    class Google_AccessToken_Revoke extends \VendorGambDuplicaWP\Google\AccessToken\Revoke
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_AccessToken_Revoke', 'VendorGambDuplicaWP\\Google_AccessToken_Revoke', \false);
    class Google_AccessToken_Verify extends \VendorGambDuplicaWP\Google\AccessToken\Verify
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_AccessToken_Verify', 'VendorGambDuplicaWP\\Google_AccessToken_Verify', \false);
    class Google_AuthHandler_AuthHandlerFactory extends \VendorGambDuplicaWP\Google\AuthHandler\AuthHandlerFactory
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_AuthHandler_AuthHandlerFactory', 'VendorGambDuplicaWP\\Google_AuthHandler_AuthHandlerFactory', \false);
    class Google_AuthHandler_Guzzle6AuthHandler extends \VendorGambDuplicaWP\Google\AuthHandler\Guzzle6AuthHandler
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_AuthHandler_Guzzle6AuthHandler', 'VendorGambDuplicaWP\\Google_AuthHandler_Guzzle6AuthHandler', \false);
    class Google_AuthHandler_Guzzle7AuthHandler extends \VendorGambDuplicaWP\Google\AuthHandler\Guzzle7AuthHandler
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_AuthHandler_Guzzle7AuthHandler', 'VendorGambDuplicaWP\\Google_AuthHandler_Guzzle7AuthHandler', \false);
    class Google_Client extends \VendorGambDuplicaWP\Google\Client
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_Client', 'VendorGambDuplicaWP\\Google_Client', \false);
    class Google_Collection extends \VendorGambDuplicaWP\Google\Collection
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_Collection', 'VendorGambDuplicaWP\\Google_Collection', \false);
    class Google_Exception extends \VendorGambDuplicaWP\Google\Exception
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_Exception', 'VendorGambDuplicaWP\\Google_Exception', \false);
    class Google_Http_Batch extends \VendorGambDuplicaWP\Google\Http\Batch
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_Http_Batch', 'VendorGambDuplicaWP\\Google_Http_Batch', \false);
    class Google_Http_MediaFileUpload extends \VendorGambDuplicaWP\Google\Http\MediaFileUpload
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_Http_MediaFileUpload', 'VendorGambDuplicaWP\\Google_Http_MediaFileUpload', \false);
    class Google_Http_REST extends \VendorGambDuplicaWP\Google\Http\REST
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_Http_REST', 'VendorGambDuplicaWP\\Google_Http_REST', \false);
    class Google_Model extends \VendorGambDuplicaWP\Google\Model
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_Model', 'VendorGambDuplicaWP\\Google_Model', \false);
    class Google_Service extends \VendorGambDuplicaWP\Google\Service
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_Service', 'VendorGambDuplicaWP\\Google_Service', \false);
    class Google_Service_Exception extends \VendorGambDuplicaWP\Google\Service\Exception
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_Service_Exception', 'VendorGambDuplicaWP\\Google_Service_Exception', \false);
    class Google_Service_Resource extends \VendorGambDuplicaWP\Google\Service\Resource
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_Service_Resource', 'VendorGambDuplicaWP\\Google_Service_Resource', \false);
    class Google_Task_Exception extends \VendorGambDuplicaWP\Google\Task\Exception
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_Task_Exception', 'VendorGambDuplicaWP\\Google_Task_Exception', \false);
    interface Google_Task_Retryable extends \VendorGambDuplicaWP\Google\Task\Retryable
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_Task_Retryable', 'VendorGambDuplicaWP\\Google_Task_Retryable', \false);
    class Google_Task_Runner extends \VendorGambDuplicaWP\Google\Task\Runner
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_Task_Runner', 'VendorGambDuplicaWP\\Google_Task_Runner', \false);
    class Google_Utils_UriTemplate extends \VendorGambDuplicaWP\Google\Utils\UriTemplate
    {
    }
    \class_alias('VendorGambDuplicaWP\Google_Utils_UriTemplate', 'VendorGambDuplicaWP\\Google_Utils_UriTemplate', \false);
}
