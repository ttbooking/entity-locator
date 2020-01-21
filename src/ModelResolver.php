<?php

namespace Daniser\EntityResolver;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ModelResolver implements Contracts\EntityResolver
{
    public function resolve(string $type, $id): Model
    {
        if (! is_subclass_of($type, Model::class)) {
            throw new Exceptions\EntityTypeMismatchException("Invalid type: $type cannot be resolved.");
        }

        try {
            return $type::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new Exceptions\EntityNotFoundException("Model $type with id $id not found.", 0, $e);
        }
    }
}
