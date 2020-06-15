<?php

namespace TTBooking\EntityLocator;

use ErrorException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;

class AggregateLocator implements Contracts\EntityLocator
{
    /** @var Container */
    protected Container $container;

    /** @var array */
    protected array $locators;

    /** @var bool */
    protected bool $fallback;

    /** @var bool */
    protected bool $ancestralOrdering;

    /**
     * AggregateLocator constructor.
     *
     * @param Container $container
     * @param array $locators
     * @param bool $fallback
     * @param bool $ancestralOrdering
     */
    public function __construct(
        Container $container,
        array $locators = [],
        bool $fallback = true,
        bool $ancestralOrdering = false
    ) {
        $this->container = $container;
        $this->locators = $locators;
        $this->fallback = $fallback;
        $this->ancestralOrdering = $ancestralOrdering;
    }

    public function locate(string $type, $id): object
    {
        $possible = static::possibleLocatables(array_keys($this->locators), $type, $this->ancestralOrdering);

        if (! $possible) {
            throw new Exceptions\EntityTypeMismatchException("Unresolvable type: $type cannot be located.");
        }

        beginning:
        if (! $actual = array_shift($possible)) {
            throw new Exceptions\EntityNotFoundException("$type not found.");
        }

        [$locator, $parameters] = ((array) $this->locators[$actual]) + [1 => []];

        if (! is_subclass_of($locator, Contracts\EntityLocator::class)) {
            throw new Exceptions\LocatorException("Invalid locator: $locator is not a locator.");
        }

        if (! is_int($actual)) {
            $parameters += ['entityClass' => $actual];
        }

        try {
            return $this->container->make($locator, $parameters)->locate($type, $id);
        } catch (BindingResolutionException $e) {
            throw new Exceptions\LocatorException("Unreachable locator: $locator is uninstantiable.", $e->getCode(), $e);
        } catch (Exceptions\EntityNotFoundException $e) {
            if ($this->fallback) {
                goto beginning;
            }
            throw $e;
        }
    }

    /**
     * @param array $locatables
     * @param string $type
     * @param bool $ordered
     *
     * @return array
     */
    protected static function possibleLocatables(array $locatables, string $type, bool $ordered = false): array
    {
        return $ordered
            ? static::orderedPossibleLocatables($locatables, $type)
            : array_filter($locatables, fn ($locatable) => is_int($locatable) || is_a($type, $locatable, true));
    }

    /**
     * @param array $locatables
     * @param string $type
     *
     * @return array
     */
    protected static function orderedPossibleLocatables(array $locatables, string $type): array
    {
        [$generic, $specific] = self::partition($locatables, fn ($locatable) => is_int($locatable));

        try {
            $possible = array_intersect(array_merge([$type], class_parents($type), class_implements($type)), $specific);
        } catch (ErrorException $e) {
            $possible = [];
        }

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
