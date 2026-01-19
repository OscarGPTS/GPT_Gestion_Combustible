<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Models
    |--------------------------------------------------------------------------
    |
    | This is the array that contains the information of the user models.
    | This information is used in the add-trait command, the migration command
    | and in all functions that need to know the class name of the user models
    | their primary key and the name of their tables.
    |
    */

    'user_models' => [
        'App\Models\User' => 'users',
    ],

    'use_teams' => false,

    /*
    |--------------------------------------------------------------------------
    | Roles, Permissions and team_permissions Table Names
    |--------------------------------------------------------------------------
    |
    | Choose the table names that you want to use for each one of the built
    | in models. When using the default names, the migration file's name
    | would have a generated hash in order to differentiate one project from
    | another within the same database.
    |
    */

    'tables' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'role_user' => 'role_user',
        'permission_user' => 'permission_user',
        'permission_role' => 'permission_role',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | These are the models used by laratrust for the permissions and roles.
    | If you want to use a different model you can change it here. Make sure
    | the model you want to use has the same methods and properties as the model
    | you are replacing.
    |
    */

    'models' => [
        'role' => 'App\Models\Role',
        'permission' => 'App\Models\Permission',
    ],

    /*
    |--------------------------------------------------------------------------
    | Infer Permissions
    |--------------------------------------------------------------------------
    |
    | Set this value to true to infer permissions based on the model name
    | and the methods available on the model.
    |
    */

    'infer_permissions' => true,

    /*
    |--------------------------------------------------------------------------
    | Permission and Role Pivot Table Names
    |--------------------------------------------------------------------------
    |
    | If you want to use different names for your pivot tables override the
    | option below. This is the case if you have a different database than the
    | one in the environment variable in your .env file.
    |
    */

    'pivot' => [
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
    ],

    /*
    |--------------------------------------------------------------------------
    | Morphs
    |--------------------------------------------------------------------------
    |
    | These are the morphs names for laratrust. If you want to use different
    | names for your morphs change them below.
    |
    */

    'morphs' => [
        'role' => 'role',
        'permission' => 'permission',
    ],

    /*
    |--------------------------------------------------------------------------
    | Seeding Options
    |--------------------------------------------------------------------------
    |
    | These are the seeding options. If you want to use this config to seed
    | your database, make sure to update the seeding options below.
    |
    */

    'seeding' => [
        'create_users' => false,
        'truncate_tables' => true,
    ],

    'cache' => [
        'expiration_time' => 5184000, // in seconds
        'cache_key' => 'laratrust_permissions_for_user_',
        'flush_cache' => true,
    ],
];
