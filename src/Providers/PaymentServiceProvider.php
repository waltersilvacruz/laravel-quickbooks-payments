<?php

namespace WebDev\QuickBooks\Payments\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use WebDEV\QuickBooks\Payments\Models\Token;
use WebDEV\QuickBooks\Payments\Payment;

/**
 * Class PaymentServiceProvider
 *
 * @package WebDEV\QuickBooks\Payments
 */
class PaymentServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            Payment::class
        ];
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Payment::class, function (Application $app) {
            $token = Token::query()->first();
            if(!$token) {
                $token = Token::query()->make();
            }

            return new Payment($app->config->get('quickbooks_payments'), $token);
        });

        $this->app->alias(Payment::class, 'QuickBooksPayments');
    }
}
