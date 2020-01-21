<?php

namespace Daniser\EntityResolver;

use Illuminate\Contracts\Container\Container;

class AggregateResolver implements Contracts\EntityResolver
{
    /** @var Container $container */
    protected Container $container;

    /** @var string[] $resolvers */
    protected array $resolvers;

    /**
     * AggregateResolver constructor.
     *
     * @param Container $container
     * @param string[] $resolvers
     */
    public function __construct(Container $container, array $resolvers = [])
    {
        $this->container = $container;
        $this->resolvers = $resolvers;
    }

    public function resolve(string $type, $id): object
    {
        if (! array_key_exists($type, $this->resolvers)) {
            throw new Exceptions\EntityResolutionException("Unresolvable type: $type cannot be resolved.");
        }

        /** @var Contracts\EntityResolver $resolver */
        if (! is_subclass_of($resolver = $this->resolvers[$type], Contracts\EntityResolver::class)) {
            throw new Exceptions\ResolverException("Invalid resolver: $resolver is not a resolver.");
        }

        return $resolver->resolve($type, $id);
    }
}
