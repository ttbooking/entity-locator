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

    /** @var bool */
    protected bool $ancestralOrdering;

    /**
     * AggregateResolver constructor.
     *
     * @param Container $container
     * @param array $resolvers
     * @param bool $fallback
     * @param bool $ancestralOrdering
     */
    public function __construct(
        Container $container,
        array $resolvers = [],
        bool $fallback = true,
        bool $ancestralOrdering = false
    ) {
        $this->container = $container;
        $this->resolvers = $resolvers;
        $this->fallback = $fallback;
        $this->ancestralOrdering = $ancestralOrdering;
    }

    public function resolve(string $type, $id): object
    {
        $possible = static::possibleResolvables(array_keys($this->resolvers), $type, $this->ancestralOrdering);

        if (! $possible) {
            throw new Exceptions\EntityTypeMismatchException("Unresolvable type: $type cannot be resolved.");
        }

        beginning:
        if (! $entityClass = array_shift($possible)) {
            throw new Exceptions\EntityNotFoundException("$type not found.");
        }

        [$resolver, $parameters] = ((array) $this->resolvers[$entityClass]) + [1 => []];
        $parameters += compact('entityClass');

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

    /**
     * @param array $resolvables
     * @param string $type
     * @param bool $ordered
     *
     * @return array
     */
    protected static function possibleResolvables(array $resolvables, string $type, bool $ordered = false): array
    {
        return $ordered
            ? static::orderedPossibleResolvables($resolvables, $type)
            : array_filter($resolvables, fn ($resolvable) => is_a($type, $resolvable, true) || is_int($resolvable));
    }

    /**
     * @param array $resolvables
     * @param string $type
     *
     * @return array
     */
    protected static function orderedPossibleResolvables(array $resolvables, string $type): array
    {
        [$generic, $specific] = self::partition($resolvables, fn ($resolvable) => is_int($resolvable));
        $possible = array_intersect(array_merge([$type], class_parents($type), class_implements($type)), $specific);

        return array_merge($possible, $generic);
    }

    /**
     * @param array $array
     * @param callable $callback
     *
     * @return array[]
     */
    private static function partition(array $array, callable $callback): array
    {
        $passed = [];
        $failed = [];

        foreach ($array as $key => $item) {
            if ($callback($item, $key)) {
                $passed[$key] = $item;
            } else {
                $failed[$key] = $item;
            }
        }

        return [$passed, $failed];
    }
}
