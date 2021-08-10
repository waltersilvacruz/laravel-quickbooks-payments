<?php

use App\Databases\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Properties for the QuickBooks SDK DataService
    |--------------------------------------------------------------------------
    |
    | The configuration keys for the SDK are inconsistent in naming convention.
    | We are adhering to snake_case.  We make a sensible guess for 'base_url'
    | using the app's env, but you can can it with 'QUICKBOOKS_API_URL'.  Also,
    | the 'redirect_uri' is made in the client from the 'quickbooks.token'
    | named route, so it cannot be configured here.
    |
    | Most of the time, only 'QUICKBOOKS_CLIENT_ID' & 'QUICKBOOKS_CLIENT_SECRET'
    | needs to be set.
    |
    | See: https://intuit.github.io/QuickBooks-V3-PHP-SDK/configuration.html
    |
    */

    'data_service' => [
        'auth_mode'     => 'oauth2',
        'base_url'      => env('QUICKBOOKS_API_URL', config('app.env') === 'production' ? 'production' : 'sandbox'),
        'client_id'     => config('app.env') === 'production' ? env('QUICKBOOKS_CLIENT_ID') : env('QUICKBOOKS_SANDBOX_CLIENT_ID'),
        'client_secret' => config('app.env') === 'production' ? env('QUICKBOOKS_CLIENT_SECRET') : env('QUICKBOOKS_SANDBOX_CLIENT_SECRET'),
        'scope'         => 'com.intuit.quickbooks.accounting com.intuit.quickbooks.payment openid profile email phone address',
    ],

    /*
    |--------------------------------------------------------------------------
    | Properties to control logging
    |--------------------------------------------------------------------------
    |
    | Configures logging to <storage_path>/logs/quickbooks.log when in debug
    | mode or when 'QUICKBOOKS_DEBUG' is true.
    |
    */

    'logging' => [
        'enabled' => env('QUICKBOOKS_DEBUG', config('app.debug')),
        'location' => storage_path('logs'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Properties to configure the routes
    |--------------------------------------------------------------------------
    |
    | There are several routes that are needed for the package, so these
    | properties allow configuring them to fit the application as needed.
    |
    */

    'route' => [
        // Controls the middlewares for thr routes.  Can be a string or array of strings
        'middleware' => [
            // Added to the protected routes for the package (i.e. connect & disconnect)
            'authenticated' => 'auth',
            // Added to all of the routes for the package
            'default'       => 'web',
        ],
        'paths'      => [
            // Show forms to connect/disconnect
            'connect'    => 'connect',
            // The DELETE takes place to remove token
            'disconnect' => 'disconnect',
            // Return URI that QuickBooks sends code to allow getting OAuth token
            'token'      => 'token',
        ],
        'prefix'     => 'quickbooks_payments',
    ],

    /*
    |--------------------------------------------------------------------------
    | Properties to configure the redirect route after disconnect
    |--------------------------------------------------------------------------
    |
    | You can set a default route name to be redirected after disconnecting
    | from QuickBooks.
    |
    */

    'redirect_route' => env('QUICKBOOKS_REDIRECT_ROUTE'),

    /*
    |--------------------------------------------------------------------------
    | Properties for control the "user" relationship in Token
    |--------------------------------------------------------------------------
    |
    | The Token class has a "user" relationship, and these properties allow
    | configuring the relationship.
    |
    */

    'user' => [
        'keys'  => [
            'foreign' => 'user_id',
            'owner'   => 'id',
        ],
        'model' => User::class,
    ],

];
