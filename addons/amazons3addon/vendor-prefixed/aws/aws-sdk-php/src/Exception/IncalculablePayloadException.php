<?php

namespace VendorGambDuplicaWP\Aws\Exception;

use VendorGambDuplicaWP\Aws\HasMonitoringEventsTrait;
use VendorGambDuplicaWP\Aws\MonitoringEventsInterface;
class IncalculablePayloadException extends \RuntimeException implements MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
