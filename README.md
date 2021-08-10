# Laravel QuickBooks Payments Client

Based on [SPINEN's Laravel QuickBooks Client](https://github.com/spinen/laravel-quickbooks-client)

PHP client wrapping the [QuickBooks Payment SDK](https://github.com/intuit/PHP-Payments-SDK).

This package was designed to work with [Laravel](https://www.laravel.com), so this package is written with Laravel in mind.

## Installation

1. Install Laravel QuickBooks Payments:

```bash
$ composer require waltersilvacruz/laravel-quickbooks-payments
```

2. Run our migration to install the `quickbooks_tokens` table:

```bash
$ php artisan migrate --package=waltersilvacruz/laravel-quickbooks-payments
```

The package uses the [auto registration feature](https://laravel.com/docs/packages#package-discovery) of Laravel.

## Configuration

1. Add the appropriate values to your ```.env```

    #### Minimal Keys
    ```bash
    QUICKBOOKS_CLIENT_ID=<Production Client ID given by QuickBooks>
    QUICKBOOKS_CLIENT_SECRET=<Production Client Secret>
    QUICKBOOKS_SANDBOX_CLIENT_ID=<Sandbox Client ID given by QuickBooks>
    QUICKBOOKS_SANDBOX_CLIENT_SECRET=<Sandbox Client Secret>
    ```

    #### Optional Keys
    ```bash
    QUICKBOOKS_API_URL=<Development|Production> # Defaults to App's env value
    QUICKBOOKS_DEBUG=<true|false>               # Defaults to App's debug value
    QUICKBOOKS_REDIRECT_ROUTE=<string>          # A named route to force redirect after disconnecting
    ```

2. _[Optional]_ Publish configs & views

    #### Config
    A configuration file named ```quickbooks_payments.php``` can be published to ```config/``` by running...
    
    ```bash
    php artisan vendor:publish --tag=quickbooks-payments-config
    ```
    
    #### Views
    View files can be published by running...
    
    ```bash
    php artisan vendor:publish --tag=quickbooks-payments-views
    ```

## Usage

Here is an example of getting the company information from QuickBooks:

```php
$client = app('QuickBooksPayments');
$array = [
  "amount" => "10.55",
  "currency" => "USD",
  "card" => [
      "name" => "emulate=0",
      "number" => "4111111111111111",
      "address" => [
        "streetAddress" => "1130 Kifer Rd",
        "city" => "Sunnyvale",
        "region" => "CA",
        "country" => "US",
        "postalCode" => "94086"
      ],
      "expMonth" => "02",
      "expYear" => "2020",
      "cvc" => "123"
  ],
  "context" => [
    "mobile" => "false",
    "isEcommerce" => "true"
  ]
];
$response = $client->charge($array);
dd($response);
```

## Middleware

If you have routes that will be dependent on the user's account having a usable QuickBooks OAuth token, there is an included middleware ```WebDEV\QuickBooks\Payments\Http\Middleware\Filter``` that gets registered as ```quickbooks_payments``` that will ensure the account is linked and redirect them to the `connect` route if needed.

Here is an example route definition:

```php
Route::view('some/route/needing/quickbooks/token/before/using', 'some.view')
     ->middleware('quickbooks_payments');
```
