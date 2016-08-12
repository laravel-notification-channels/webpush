<?php

namespace NotificationChannels\WebPush;

use Minishlink\WebPush\WebPush;
use Illuminate\Support\ServiceProvider;

class WebPushServiceProvider extends ServiceProvider
{
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
                return new WebPush([
                    'GCM' => config('services.gcm.key'),
                ]);
            });

        if ($this->app->runningInConsole()) {
            $this->definePublishing();
        }
    }

    /**
     * Define the publishable migrations and resources.
     *
     * @return void
     */
    protected function definePublishing()
    {
        if (! class_exists('CreatePushSubscriptionsTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../migrations/create_push_subscriptions_table.php.stub' => $this->app->databasePath().'/migrations/'.$timestamp.'_create_push_subscriptions_table.php',
            ], 'migrations');
        }
    }
}
