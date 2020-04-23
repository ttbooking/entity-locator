<?php

namespace Daniser\EntityResolver;

use ErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

class ModelResolver implements Contracts\EntityResolver
{
    /** @var string */
    protected string $modelClass;

    /** @var string[] */
    protected array $columns;

    /**
     * ModelResolver constructor.
     *
     * @param string $entityClass
     * @param string|string[] $columns
     */
    public function __construct(string $entityClass = Model::class, $columns = [])
    {
        if (! is_a($entityClass, Model::class, true)) {
            throw new Exceptions\ResolverException("Cannot instantiate resolver: $entityClass must be an instance of Model.");
        }

        $this->modelClass = $entityClass;
        $this->columns = (array) $columns;
    }

    public function resolve(string $type, $id): Model
    {
        if (! is_a($type, $this->modelClass, true)) {
            throw new Exceptions\EntityTypeMismatchException("Invalid type: $type cannot be resolved.");
        }

        $attributes = (array) $id;

        try {
            $attributes = Arr::isAssoc($attributes) ? $attributes
                : array_combine($this->columns ?: [(new $type)->getKeyName()], $attributes);

            return $type::where($attributes)->firstOrFail();
        } catch (ErrorException | ModelNotFoundException $e) {
            throw new Exceptions\EntityNotFoundException("Model $type with given attributes not found.", $e->getCode(), $e);
        }
    }
}
