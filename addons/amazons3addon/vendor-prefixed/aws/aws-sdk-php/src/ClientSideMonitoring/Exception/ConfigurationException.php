<?php

namespace VendorGambDuplicaWP\Aws\ClientSideMonitoring\Exception;

use VendorGambDuplicaWP\Aws\HasMonitoringEventsTrait;
use VendorGambDuplicaWP\Aws\MonitoringEventsInterface;
/**
 * Represents an error interacting with configuration for client-side monitoring.
 */
class ConfigurationException extends \RuntimeException implements MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
