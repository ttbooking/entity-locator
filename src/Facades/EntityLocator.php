<?php

namespace TTBooking\EntityLocator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TTBooking\EntityLocator\AggregateLocator::class
 */
class EntityLocator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'entityLocator';
    }
}
