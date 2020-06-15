<?php

namespace TTBooking\EntityLocator\Concerns;

use TTBooking\EntityLocator\Exceptions\EntityException;
use TTBooking\EntityLocator\Exceptions\LocatorException;
use TTBooking\EntityLocator\Facades\EntityLocator;

trait Locatable
{
    /**
     * @param mixed $address
     *
     * @throws LocatorException
     * @throws EntityException
     *
     * @return static
     */
    public static function locate($address)
    {
        return EntityLocator::locate(__CLASS__, $address);
    }
}
