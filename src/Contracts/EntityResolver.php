<?php

namespace Daniser\EntityResolver\Contracts;

use Daniser\EntityResolver\Exceptions\ResolverException;
use Daniser\EntityResolver\Exceptions\EntityException;

interface EntityResolver
{
    /**
     * @param string $type
     * @param mixed $id
     *
     * @throws ResolverException
     * @throws EntityException
     *
     * @return object
     */
    public function resolve(string $type, $id): object;
}
