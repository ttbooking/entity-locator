<?php

namespace Daniser\EntityResolver;

class ChainedResolver implements Contracts\EntityResolver
{
    /** @var string[] $resolvers */
    protected array $resolvers;

    /**
     * ChainedResolver constructor.
     *
     * @param string[] $resolvers
     */
    public function __construct(array $resolvers = [])
    {
        $this->resolvers = $resolvers;
    }

    public function resolve(string $type, $id): object
    {

    }
}
