<?php

declare(strict_types=1);

namespace TTBooking\EntityLocator\Concerns;

use TTBooking\EntityLocator\Exceptions\EntityException;
use TTBooking\EntityLocator\Exceptions\LocatorException;
use TTBooking\EntityLocator\Facades\EntityLocator;

trait Locatable
{
    /**
     * @param  mixed  $address
     * @return static
     *
     * @throws LocatorException
     * @throws EntityException
     */
    public static function locate($address)
    {
        return EntityLocator::locate(__CLASS__, $address);
    }
}
