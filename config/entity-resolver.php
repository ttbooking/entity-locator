<?php

return [

    'resolvers' => [
        App\User::class => [Daniser\EntityResolver\ModelResolver::class, ['columns' => 'email']],
        Illuminate\Database\Eloquent\Model::class => Daniser\EntityResolver\ModelResolver::class,
        Daniser\EntityResolver\ValidatingResolver::class,
    ],

    'enable_fallback' => true,

    'ancestral_ordering' => true,

    'composite_delimiter' => ':',

    'aliases' => [
        'user' => App\User::class,
    ],

    'merge_with_morph_map' => true,

    'override_morph_map' => true,

];
