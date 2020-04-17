<?php

return [

    'resolvers' => [
        //App\User::class => Daniser\EntityResolver\UserResolver::class,

        /*App\User::class => [Daniser\EntityResolver\ChainedResolver::class, ['resolvers' => [
            [Daniser\EntityResolver\ConfigurableModelResolver::class, ['columns' => 'email']],
            Daniser\EntityResolver\ConfigurableModelResolver::class,
        ]]],*/

        // TODO: chain of responsibility is not needed here, just rely on aggregate resolver's fallback mechanism
        /*App\User::class => chain_resolvers(
            [Daniser\EntityResolver\ConfigurableModelResolver::class, ['columns' => 'email']],
            Daniser\EntityResolver\ConfigurableModelResolver::class,
        ),*/

        App\User::class => [Daniser\EntityResolver\ConfigurableModelResolver::class, ['columns' => 'email']],

        Illuminate\Database\Eloquent\Model::class => Daniser\EntityResolver\ConfigurableModelResolver::class,
    ],

    //'ancestral_ordering' => false,

    'enable_fallback' => true,

    'models' => [
        App\User::class => 'email',
    ],

    'attribute_coherence' => true,

    'composite_delimiter' => ':',

    'aliases' => [
        'user' => App\User::class,
    ],

    'merge_with_morph_map' => true,

    'override_morph_map' => true,

];
