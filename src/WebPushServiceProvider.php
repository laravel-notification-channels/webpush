<?php

namespace NotificationChannels\WebPush;

use Illuminate\Support\Str;
use Minishlink\WebPush\WebPush;
use Illuminate\Support\ServiceProvider;

class WebPushServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([VapidKeysGenerateCommand::class]);

        $this->mergeConfigFrom(__DIR__.'/../config/webpush.php', 'webpush');
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->when(WebPushChannel::class)
            ->needs(WebPush::class)
            ->give(function () {
                return new WebPush($this->webPushConfig());
            });

        if ($this->app->runningInConsole()) {
            $this->definePublishing();
        }
    }

    /**
     * @return array
     */
    protected function webPushConfig()
    {
        $config = [];
        $webpush = config('webpush');
        $publicKey = $webpush['vapid']['public_key'];
        $privateKey = $webpush['vapid']['private_key'];

        if (! empty($webpush['gcm']['key'])) {
            $config['GCM'] = $webpush['gcm']['key'];
        }

        if (empty($publicKey) || empty($privateKey)) {
            return $config;
        }

        $config['VAPID'] = compact('publicKey', 'privateKey');
        $config['VAPID']['subject'] = $webpush['vapid']['subject'];

        if (empty($config['VAPID']['subject'])) {
            $config['VAPID']['subject'] = url('/');
        }

        if (! empty($webpush['vapid']['pem_file'])) {
            $config['VAPID']['pemFile'] = $webpush['vapid']['pem_file'];

            if (Str::startsWith($config['VAPID']['pemFile'], 'storage')) {
                $config['VAPID']['pemFile'] = base_path($config['VAPID']['pemFile']);
            }
        }

        return $config;
    }

    /**
     * Define the publishable migrations and resources.
     *
     * @return void
     */
    protected function definePublishing()
    {
        $this->publishes([
            __DIR__.'/../config/webpush.php' => config_path('webpush.php'),
        ], 'config');

        if (! class_exists('CreatePushSubscriptionsTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../migrations/create_push_subscriptions_table.php.stub' => $this->app->databasePath().'/migrations/'.$timestamp.'_create_push_subscriptions_table.php',
            ], 'migrations');
        }
    }
}
