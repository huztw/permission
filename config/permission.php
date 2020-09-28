<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Default User's Group
    |--------------------------------------------------------------------------
    |
    | This value is the default for application.
    |
     */

    'group'          => 'admin',

    /*
    |--------------------------------------------------------------------------
    | Administrator
    |--------------------------------------------------------------------------
    |
    | Which role has all permission
    |
     */
    'administrator'  => 'administrator',

    /*
    |--------------------------------------------------------------------------
    | Route default value
    |--------------------------------------------------------------------------
    |
    | Set a default value for route in database.
    |
     */
    'default_routes' => [

        // Set routes visibility by use "public", "protected", "private"
        'visibility' => 'protected',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission setting
    |--------------------------------------------------------------------------
    |
    | Set user authenticatable to use permission.
    |
     */
    'permission'     => [
        // 'user'  => Illuminate\Support\Facades\Auth::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Database settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for builtin model & tables.
    |
     */
    'database'       => [

        // Database connection for following tables.
        'connection'               => '',

        // Role table and model.
        'roles_table'              => 'roles',
        'roles_model'              => Huztw\Permission\Models\Role::class,

        // Permission table and model.
        'permissions_table'        => 'permissions',
        'permissions_model'        => Huztw\Permission\Models\Permission::class,

        // Route table and model.
        'routes_table'             => 'routes',
        'routes_model'             => Huztw\Permission\Models\Route::class,

        // Action table and model.
        'actions_table'            => 'actions',
        'actions_model'            => Huztw\Permission\Models\Action::class,

        // Pivot table for table above.
        'permission_roles_table'   => 'permission_roles',
        'permission_routes_table'  => 'permission_routes',
        'action_permissions_table' => 'action_permissions',
    ],
];
