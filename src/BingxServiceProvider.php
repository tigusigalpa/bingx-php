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
            return new BingxClient(
                $config['api_key'] ?? '',
                $config['api_secret'] ?? '',
                $config['base_uri'] ?? 'https://open-api.bingx.com',
                $config['source_key'] ?? null,
                $config['signature_encoding'] ?? 'base64'
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