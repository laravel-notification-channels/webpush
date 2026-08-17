<?php

namespace NotificationChannels\WebPush;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Minishlink\WebPush\WebPush;
use Psr\Http\Client\ClientInterface;

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
        $config = $this->webPushConfig();

        $this->app->when(WebPushChannel::class)
            ->needs(WebPush::class)
            ->give(fn (): WebPush => (new WebPush(
                $this->webPushAuth(), [], $this->webPushClient($config['client_options']), new HttpFactory, new HttpFactory
            ))
                ->setReuseVAPIDHeaders(true)
                ->setAutomaticPadding($config['automatic_padding']));

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
        $webpush = $this->webPushConfig();
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
     * Create the HTTP client used to deliver push notifications.
     *
     * @param  array<mixed>  $options
     */
    protected function webPushClient(array $options): ClientInterface
    {
        // @phpstan-ignore-next-line Laravel 12 remains supported and requires the Guzzle fallback.
        if (version_compare(Application::VERSION, '13.13.0', '<')) {
            return new Client(['timeout' => 30, ...$options]);
        }

        return Http::timeout(30)->withOptions($options)->buildClient();
    }

    /**
     * @return array{
     *     vapid: array{subject: string|null, public_key: string|null, private_key: string|null, pem_file: string|null},
     *     client_options: array<mixed>,
     *     automatic_padding: bool|int
     * }
     */
    protected function webPushConfig(): array
    {
        /** @var array{
         *     vapid: array{subject: string|null, public_key: string|null, private_key: string|null, pem_file: string|null},
         *     client_options: array<mixed>,
         *     automatic_padding: bool|int
         * } $config
         */
        $config = config('webpush');

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

        $this->publishes([
            __DIR__.'/../migrations/create_push_subscriptions_table.php.stub' => $this->getMigrationFileName('create_push_subscriptions_table.php'),
            __DIR__.'/../migrations/increase_push_subscriptions_endpoint_length.php.stub' => $this->getMigrationFileName('increase_push_subscriptions_endpoint_length.php'),
        ], 'migrations');
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     */
    protected function getMigrationFileName(string $migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make([$this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR])
            ->flatMap(fn ($path) => $filesystem->glob($path.'*_'.$migrationFileName))
            ->push($this->app->databasePath().sprintf('/migrations/%s_%s', $timestamp, $migrationFileName))
            ->first();
    }
}
