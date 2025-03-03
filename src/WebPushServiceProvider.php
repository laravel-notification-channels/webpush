<?php

namespace NotificationChannels\WebPush;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Minishlink\WebPush\WebPush;

class WebPushServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->commands([VapidKeysGenerateCommand::class]);

        $this->mergeConfigFrom(__DIR__.'/../config/webpush.php', 'webpush');
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->app->when(WebPushChannel::class)
            ->needs(WebPush::class)
            ->give(function () {
                return (new WebPush(
                    $this->webPushAuth(), [], 30, config('webpush.client_options', [])
                ))
                    ->setReuseVAPIDHeaders(true)
                    ->setAutomaticPadding(config('webpush.automatic_padding'));
            });

        $this->app->when(WebPushChannel::class)
            ->needs(ReportHandlerInterface::class)
            ->give(ReportHandler::class);

        if ($this->app->runningInConsole()) {
            $this->definePublishing();
        }
    }

    /**
     * Get the authentication details.
     *
     * @return array<string, mixed>
     */
    protected function webPushAuth(): array
    {
        $config = [];
        $webpush = config('webpush');
        $publicKey = $webpush['vapid']['public_key'];
        $privateKey = $webpush['vapid']['private_key'];

        if (empty($publicKey) || empty($privateKey)) {
            return $config;
        }

        $config['VAPID'] = ['publicKey' => $publicKey, 'privateKey' => $privateKey];
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
                __DIR__.'/../migrations/create_push_subscriptions_table.php.stub' => database_path(sprintf('migrations/%s_create_push_subscriptions_table.php', $timestamp)),
            ], 'migrations');
        }
    }
}
