<?php

return [

    'locators' => [
        App\User::class => [TTBooking\EntityLocator\ModelLocator::class, ['columns' => 'email']],
        Illuminate\Database\Eloquent\Model::class => TTBooking\EntityLocator\ModelLocator::class,
        TTBooking\EntityLocator\ValidatingLocator::class,
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
