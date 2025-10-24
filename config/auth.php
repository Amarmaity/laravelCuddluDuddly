<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | The default guard for users and the password reset broker.
    | For normal users, we'll use 'web'. Admins use 'admin' guard.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Here we define separate guards for customers and admins.
    | Both use the session driver since they log in via web UI.
    |
    */

    'guards' => [
        // Customer (normal user)
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // Admin users (from admin_users table)
        'admin' => [
            'driver' => 'session',
            'provider' => 'admin_users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Providers define how we fetch users for each guard.
    | Customers come from App\Models\User,
    | Admins come from App\Models\AdminUser.
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'admin_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\AdminUser::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These control how password resets work.
    | You can safely leave defaults as is.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'admin_users' => [
            'provider' => 'admin_users',
            'table' => 'admin_password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Number of seconds before re-authentication is required.
    | Default is 3 hours (10800 seconds).
    |
    */

    'password_timeout' => 10800,

];
