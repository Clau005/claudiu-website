<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Visual Editor Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the visual editor package settings.
    |
    */

    'route_prefix' => 'admin',

    'middleware' => ['web', 'auth:admin'],

    'auth' => [
        'guard' => 'admin',
        'passwords' => 'admins',
    ],

    'navigation' => [
        'default_items' => [
            'dashboard' => [
                'label' => 'Dashboard',
                'url' => '/admin/dashboard',
                'icon' => 'home',
                'order' => 1,
            ],
        ],
    ],

];
