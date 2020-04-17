<?php

use Daniser\EntityResolver\AggregateResolver;
use Daniser\EntityResolver\ChainedResolver;
use Daniser\EntityResolver\Exceptions\EntityException;
use Daniser\EntityResolver\Exceptions\ResolverException;
use Daniser\EntityResolver\Facades\EntityResolver;

if (! function_exists('resolver_array')) {
    /**
     * @param mixed ...$resolvers
     *
     * @return array
     */
    function resolver_array(...$resolvers): array
    {
        return [AggregateResolver::class, compact('resolvers')];
    }
}

if (! function_exists('chain_resolvers')) {
    /**
     * @param mixed ...$resolvers
     *
     * @return array
     */
    function chain_resolvers(...$resolvers): array
    {
        return [ChainedResolver::class, compact('resolvers')];
    }
}

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
