<?php

namespace Daniser\EntityResolver;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class AggregateResolver implements Contracts\EntityResolver
{
    /** @var ContainerInterface $container */
    protected ContainerInterface $container;

    /** @var string[] $resolvers */
    protected array $resolvers;

    /**
     * AggregateResolver constructor.
     *
     * @param ContainerInterface $container
     * @param string[] $resolvers
     */
    public function __construct(ContainerInterface $container, array $resolvers = [])
    {
        $this->container = $container;
        $this->resolvers = $resolvers;
    }

    public function resolve(string $type, $id): object
    {
        if (! array_key_exists($type, $this->resolvers)) {
            throw new Exceptions\EntityResolutionException("Unresolvable type: $type cannot be resolved.");
        }

        if (! is_subclass_of($resolver = $this->resolvers[$type], Contracts\EntityResolver::class)) {
            throw new Exceptions\ResolverException("Invalid resolver: $resolver is not a resolver.");
        }

        try {
            return $this->container->get($resolver)->resolve($type, $id);
        } catch (NotFoundExceptionInterface $e) {
            throw new Exceptions\ResolverException("Unreachable resolver: $resolver is uninstantiable.", $e->getCode(), $e);
        }
    }
}
