<?php

declare(strict_types=1);

namespace TTBooking\EntityLocator\Contracts;

use TTBooking\EntityLocator\Exceptions\EntityException;
use TTBooking\EntityLocator\Exceptions\LocatorException;

interface EntityLocator
{
    /**
     * @param string $type
     * @param mixed $id
     *
     * @throws LocatorException
     * @throws EntityException
     *
     * @return object
     */
    public function locate(string $type, $id): object;
}
