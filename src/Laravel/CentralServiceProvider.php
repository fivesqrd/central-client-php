<?php

namespace Fivesqrd\Central\Laravel;

use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;
use Illuminate\Support;
use Aws\DynamoDb;
use Fivesqrd\Central;

/**
 * Atlas service provider
 */
class CentralServiceProvider extends Support\ServiceProvider
{
    
    /**
     * Bootstrap the configuration
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath($raw = __DIR__ . '/Config.php') ?: $raw;

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('central.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('central');
        }

        $this->mergeConfigFrom($source, 'central');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('central', function ($app) {
            $config = $app->make('config')->get('central');

            $config['options']['client'] = new DynamoDb\DynamoDbClient(
                $config['aws']
            );

            $config['options']['marshaler'] = new DynamoDb\Marshaler();

            return new Central\Factory($config);
        });

        $this->commands([
            Console\Test::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['central'];
    }
}