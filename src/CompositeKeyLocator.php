<?php

declare(strict_types=1);

namespace TTBooking\EntityLocator;

class CompositeKeyLocator implements Contracts\EntityLocator
{
    /** @var Contracts\EntityLocator */
    protected Contracts\EntityLocator $locator;

    /** @var string */
    protected string $delimiter;

    /**
     * CompositeKeyLocator constructor.
     *
     * @param  Contracts\EntityLocator  $locator
     * @param  string  $delimiter
     */
    public function __construct(Contracts\EntityLocator $locator, string $delimiter = ':')
    {
        $this->locator = $locator;
        $this->delimiter = $delimiter;
    }

    public function locate(string $type, $id): object
    {
        if (is_string($id)) {
            $id = explode($this->delimiter, $id);
        }

        return $this->locator->locate($type, $id);
    }
}
