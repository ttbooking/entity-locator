<?php

declare(strict_types=1);

namespace TTBooking\EntityLocator;

use ErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

class ModelLocator implements Contracts\EntityLocator
{
    /** @var string */
    protected string $modelClass;

    /** @var string[] */
    protected array $columns;

    /**
     * ModelLocator constructor.
     *
     * @param string $entityClass
     * @param string|string[] $columns
     */
    public function __construct(string $entityClass = Model::class, $columns = [])
    {
        if (! is_a($entityClass, Model::class, true)) {
            throw new Exceptions\LocatorException("Cannot instantiate locator: $entityClass must be an instance of Model.");
        }

        $this->modelClass = $entityClass;
        $this->columns = (array) $columns;
    }

    public function locate(string $type, $id): Model
    {
        if (! is_a($type, $this->modelClass, true)) {
            throw new Exceptions\EntityTypeMismatchException("Invalid type: $type cannot be located.");
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
