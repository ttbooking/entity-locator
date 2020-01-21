<?php

namespace Daniser\EntityResolver\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Daniser\EntityResolver\AggregateResolver::class
 */
class EntityResolver extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'entityResolver';
    }
}
