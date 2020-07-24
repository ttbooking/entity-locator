<?php

declare(strict_types=1);

namespace TTBooking\EntityLocator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static object locate(string $type, mixed $id)
 *
 * @see \TTBooking\EntityLocator\AggregateLocator::class
 */
class EntityLocator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'entityLocator';
    }
}
