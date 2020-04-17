<?php

namespace Daniser\EntityResolver;

class CompositeKeyResolver implements Contracts\EntityResolver
{
    /** @var Contracts\EntityResolver */
    protected Contracts\EntityResolver $resolver;

    /** @var string */
    protected string $delimiter;

    /**
     * CompositeKeyResolver constructor.
     *
     * @param Contracts\EntityResolver $resolver
     * @param string $delimiter
     */
    public function __construct(Contracts\EntityResolver $resolver, string $delimiter = ':')
    {
        $this->resolver = $resolver;
        $this->delimiter = $delimiter;
    }

    public function resolve(string $type, $id): object
    {
        if (is_string($id)) {
            $id = explode($this->delimiter, $id);
        }

        return $this->resolver->resolve($type, $id);
    }
}
