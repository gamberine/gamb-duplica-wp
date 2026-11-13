<?php

namespace VendorGambDuplicaWP\Cron;

interface FieldFactoryInterface
{
    public function getField(int $position): FieldInterface;
}
