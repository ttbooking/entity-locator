<?php

use Daniser\EntityResolver\Facades\EntityResolver;
use Daniser\EntityResolver\Exceptions\ResolverException;
use Daniser\EntityResolver\Exceptions\EntityException;

if (! function_exists('resolve_entity')) {
    /**
     * @param string $type
     * @param mixed $id
     *
     * @throws ResolverException
     * @throws EntityException
     *
     * @return object
     */
    function resolve_entity(string $type, $id): object
    {
        return EntityResolver::resolve($type, $id);
    }
}
