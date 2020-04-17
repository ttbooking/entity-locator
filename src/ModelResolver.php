<?php

namespace Daniser\EntityResolver;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

class ModelResolver implements Contracts\EntityResolver
{
    /** @var string */
    protected string $model;

    /** @var string[] */
    protected array $columns;

    /**
     * ConfigurableModelResolver constructor.
     *
     * @param string $model
     * @param string|string[] $columns
     */
    public function __construct(string $model, $columns = [])
    {
        if (! is_subclass_of($model, Model::class)) {
            throw new Exceptions\ResolverException("Cannot instantiate resolver: $model must be an instance of Model.");
        }

        $this->model = $model;
        $this->columns = $columns ? (array) $columns : [(new $model)->getKeyName()];
    }

    public function resolve(string $type, $id): Model
    {
        if (! is_a($type, $this->model, true)) {
            throw new Exceptions\EntityTypeMismatchException("Invalid type: $type cannot be resolved.");
        }

        $attributes = (array) $id;
        $attributes = Arr::isAssoc($attributes) ? $attributes : array_combine($this->columns, $attributes);

        try {
            return $type::where($attributes)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new Exceptions\EntityNotFoundException("Model $type with given attributes not found.", $e->getCode(), $e);
        }
    }
}
