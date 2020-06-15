<?php

namespace TTBooking\EntityLocator;

class ValidatingLocator implements Contracts\EntityLocator
{
    public function locate(string $type, $id): object
    {
        if (! $id instanceof $type) {
            throw new Exceptions\EntityNotFoundException("Given object is not an instance of $type.");
        }

        return $id;
    }
}
