<?php

namespace Daniser\EntityResolver\Concerns;

use Daniser\EntityResolver\Exceptions\EntityException;
use Daniser\EntityResolver\Exceptions\ResolverException;
use Daniser\EntityResolver\Facades\EntityResolver;

trait Resolvable
{
    /**
     * @param mixed $address
     *
     * @throws ResolverException
     * @throws EntityException
     *
     * @return static
     */
    public static function resolve($address)
    {
        return EntityResolver::resolve(__CLASS__, $address);
    }
}
