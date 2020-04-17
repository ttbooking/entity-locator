<?php

namespace Daniser\EntityResolver;

class ValidatingResolver implements Contracts\EntityResolver
{
    public function resolve(string $type, $id): object
    {
        if (! $id instanceof $type) {
            throw new Exceptions\EntityNotFoundException("Given object is not an instance of $type.");
        }

        return $id;
    }
}
