<?php

namespace VendorGambDuplicaWP\Aws\DefaultsMode\Exception;

use VendorGambDuplicaWP\Aws\HasMonitoringEventsTrait;
use VendorGambDuplicaWP\Aws\MonitoringEventsInterface;
/**
 * Represents an error interacting with configuration mode
 */
class ConfigurationException extends \RuntimeException implements MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
