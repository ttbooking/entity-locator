<?php

declare(strict_types=1);

use TTBooking\EntityLocator\AggregateLocator;
use TTBooking\EntityLocator\Exceptions\EntityException;
use TTBooking\EntityLocator\Exceptions\LocatorException;
use TTBooking\EntityLocator\Facades\EntityLocator;

if (! function_exists('locator_array')) {
    /**
     * @param  mixed  ...$locators
     * @return array
     */
    function locator_array(...$locators): array
    {
        return [AggregateLocator::class, compact('locators')];
    }
}

if (! function_exists('locate_entity')) {
    /**
     * @param  string  $type
     * @param  mixed  $id
     * @return object
     *
     * @throws LocatorException
     * @throws EntityException
     */
    function locate_entity(string $type, $id): object
    {
        return EntityLocator::locate($type, $id);
    }
}
