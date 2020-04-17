<?php

return [

    'resolvers' => [
        /*App\User::class => [Daniser\EntityResolver\AggregateResolver::class, ['resolvers' => [
            [Daniser\EntityResolver\ModelResolver::class, ['columns' => 'email']],
            Daniser\EntityResolver\ModelResolver::class,
        ]]],*/

        // TODO: chain of responsibility is not needed here, just rely on aggregate resolver's fallback mechanism
        /*App\User::class => resolver_array(
            [Daniser\EntityResolver\ModelResolver::class, ['columns' => 'email']],
            Daniser\EntityResolver\ModelResolver::class,
        ),*/

        App\User::class => [Daniser\EntityResolver\ModelResolver::class, ['columns' => 'email']],

        Illuminate\Database\Eloquent\Model::class => Daniser\EntityResolver\ModelResolver::class,

        Daniser\EntityResolver\ValidatingResolver::class,
    ],

    'enable_fallback' => true,

    'ancestral_ordering' => true,

    'attribute_coherence' => true,

    'composite_delimiter' => ':',

    'aliases' => [
        'user' => App\User::class,
    ],

    'merge_with_morph_map' => true,

    'override_morph_map' => true,

];
