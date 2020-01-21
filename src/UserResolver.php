<?php

namespace Daniser\EntityResolver;

use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserResolver extends ModelResolver
{
    public function resolve(string $type, $id): User
    {
        if (! is_a($type, User::class, true)) {
            throw new Exceptions\EntityTypeMismatchException("Invalid type: $type cannot be resolved.");
        }

        if (is_string($id) && ! ctype_digit($id)) {
            try {
                return $type::where('email', $id)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                throw new Exceptions\EntityNotFoundException("User with email $id not found.", 0, $e);
            }
        }

        return parent::resolve($type, $id);
    }
}
