<?php

namespace WebDEV\QuickBooks\Payments\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use WebDEV\QuickBooks\Payments\Http\Middleware\Filter;

/**
 * Class ServiceProvider
 *
 * @package WebDEV\QuickBooks\Payments
 */
class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMiddleware();
        $this->registerPublishes();
        $this->registerRoutes();
        $this->registerViews();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/quickbooks_payments.php', 'quickbooks_payments');
    }

    /**
     * Register the middleware
     *
     * If a route needs to have the QuickBooks client, then make sure that the user has linked their account.
     *
     */
    public function registerMiddleware()
    {
        $this->app->router->aliasMiddleware('quickbooks_payments', Filter::class);
    }

    /**
     * There are several resources that get published
     *
     * Only worry about telling the application about them if running in the console.
     *
     */
    protected function registerPublishes()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

            $this->publishes([
                __DIR__ . '/../../config/quickbooks_payments.php' => config_path('quickbooks_payments.php'),
            ], 'quickbooks-payments-config');

            $this->publishes([
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'quickbooks-payments-migrations');

            $this->publishes([
                __DIR__ . '/../../resources/views' => base_path('resources/views/vendor/quickbooks_payments'),
            ], 'quickbooks-payments-views');
        }
    }

    /**
     * Register the routes needed for the registration flow
     */
    protected function registerRoutes()
    {
        $config = $this->app->config->get('quickbooks_payments.route');

        $this->app->router->prefix($config['prefix'])
            ->as('quickbooks_payments.')
            ->middleware($config['middleware']['default'])
            ->namespace('WebDEV\QuickBooks\Payments\Http\Controllers')
            ->group(function (Router $router) use ($config) {
                $router->get($config['paths']['connect'], 'Controller@connect')
                    ->middleware($config['middleware']['authenticated'])
                    ->name('connect');

                $router->delete($config['paths']['disconnect'], 'Controller@disconnect')
                    ->middleware($config['middleware']['authenticated'])
                    ->name('disconnect');

                $router->get($config['paths']['token'], 'Controller@token')
                    ->name('token');
            });
    }

    /**
     * Register the views
     */
    protected function registerViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'quickbooks_payments');
    }
}
