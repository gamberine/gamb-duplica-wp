<?php

namespace VendorGambDuplicaWP\Aws\Exception;

use VendorGambDuplicaWP\Aws\HasMonitoringEventsTrait;
use VendorGambDuplicaWP\Aws\MonitoringEventsInterface;
class InvalidRegionException extends \RuntimeException implements MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
