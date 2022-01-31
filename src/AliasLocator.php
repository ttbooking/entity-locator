<?php

declare(strict_types=1);

namespace TTBooking\EntityLocator;

class AliasLocator implements Contracts\EntityLocator
{
    /** @var Contracts\EntityLocator */
    protected Contracts\EntityLocator $locator;

    /** @var string[] */
    protected array $aliases;

    /**
     * AliasLocator constructor.
     *
     * @param  Contracts\EntityLocator  $locator
     * @param  string[]  $aliases
     */
    public function __construct(Contracts\EntityLocator $locator, array $aliases = [])
    {
        $this->locator = $locator;
        $this->aliases = $aliases;
    }

    public function locate(string $type, $id): object
    {
        try {
            return $this->locator->locate($type, $id);
        } catch (Exceptions\EntityException $e) {
            if (! array_key_exists($type, $this->aliases)) {
                throw $e;
            }

            return $this->locator->locate($this->aliases[$type], $id);
        }
    }
}
