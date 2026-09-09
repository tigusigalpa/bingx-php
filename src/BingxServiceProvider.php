<?php
namespace Tigusigalpa\BingX;

use Illuminate\Support\ServiceProvider;

class BingxServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/bingx.php', 'bingx');
        $this->app->singleton(BingxClient::class, function ($app) {
            $config = $app['config']->get('bingx', []);
            $isDemo = filter_var($config['demo'] ?? false, FILTER_VALIDATE_BOOLEAN);

            return new BingxClient(
                $config['api_key'] ?? '',
                $config['api_secret'] ?? '',
                $isDemo
                    ? BingxClient::DEMO_BASE_URI
                    : ($config['base_uri'] ?? BingxClient::LIVE_BASE_URI),
                $config['source_key'] ?? null,
                $config['signature_encoding'] ?? 'hex'
            );
        });
        $this->app->alias(BingxClient::class, 'bingx');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/bingx.php' => $this->app->configPath('bingx.php'),
        ], 'bingx-config');
    }
}
