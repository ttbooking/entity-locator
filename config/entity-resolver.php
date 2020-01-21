<?php

return [

    'resolvers' => [
        App\User::class => Daniser\EntityResolver\UserResolver::class,
    ],

    'aliases' => [
        'user' => App\User::class,
    ],

];
