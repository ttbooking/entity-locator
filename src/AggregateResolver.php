<?php

namespace Daniser\EntityResolver;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;

class AggregateResolver implements Contracts\EntityResolver
{
    /** @var Container */
    protected Container $container;

    /** @var array */
    protected array $resolvers;

    /** @var bool */
    protected bool $fallback;

    /**
     * AggregateResolver constructor.
     *
     * @param Container $container
     * @param array $resolvers
     * @param bool $fallback
     */
    public function __construct(Container $container, array $resolvers = [], bool $fallback = true)
    {
        $this->container = $container;
        $this->resolvers = $resolvers;
        $this->fallback = $fallback;
    }

    public function resolve(string $type, $id): object
    {
        $possible = array_intersect(
            array_merge([$type], class_parents($type), class_implements($type)),
            array_keys($this->resolvers)
        );
        if (! $possible) {
            throw new Exceptions\EntityTypeMismatchException("Unresolvable type: $type cannot be resolved.");
        }

        beginning:
        if (! $actual = array_shift($possible)) {
            throw new Exceptions\EntityNotFoundException("$type not found.");
        }

        [$resolver, $parameters] = ((array) $this->resolvers[$actual]) + [1 => []];

        if (! is_subclass_of($resolver, Contracts\EntityResolver::class)) {
            throw new Exceptions\ResolverException("Invalid resolver: $resolver is not a resolver.");
        }

        try {
            return $this->container->make($resolver, $parameters)->resolve($type, $id);
        } catch (BindingResolutionException $e) {
            throw new Exceptions\ResolverException("Unreachable resolver: $resolver is uninstantiable.", $e->getCode(), $e);
        } catch (Exceptions\EntityNotFoundException $e) {
            if ($this->fallback) {
                goto beginning;
            }
            throw $e;
        }
    }
}
