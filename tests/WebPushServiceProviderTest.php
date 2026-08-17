<?php

namespace NotificationChannels\WebPush\Test;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Minishlink\WebPush\WebPush;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushServiceProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Client\ClientInterface;

class WebPushServiceProviderTest extends TestCase
{
    #[Test]
    public function it_injects_a_psr_http_client_into_web_push(): void
    {
        /** @var WebPushChannel $channel */
        $channel = $this->application->make(WebPushChannel::class);

        /** @var WebPush $webPush */
        $webPush = (new \ReflectionProperty($channel, 'webPush'))->getValue($channel);
        $client = (new \ReflectionProperty($webPush, 'client'))->getValue($webPush);

        $this->assertInstanceOf(ClientInterface::class, $client);
        $this->assertInstanceOf(Client::class, $client);
    }

    #[Test]
    public function it_publishes_config(): void
    {
        // Ensure config directory exists in the application config path
        $configPath = config_path('webpush.php');

        if (File::exists($configPath)) {
            File::delete($configPath);
        }

        $exit = Artisan::call('vendor:publish', [
            '--provider' => WebPushServiceProvider::class,
            '--tag' => 'config',
        ]);

        $this->assertEquals(0, $exit);
        $this->assertFileExists($configPath, 'The webpush config file was not published to the config path.');

        // Cleanup
        if (File::exists($configPath)) {
            File::delete($configPath);
        }
    }

    #[Test]
    public function it_publishes_migration(): void
    {
        $migrationsPath = $this->application->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR;

        // Remove any existing matching migrations in the database/migrations folder for a clean test
        foreach (glob($migrationsPath.'*_create_push_subscriptions_table.php') ?: [] as $file) {
            @unlink($file);
        }

        foreach (glob($migrationsPath.'*_increase_push_subscriptions_endpoint_length.php') ?: [] as $file) {
            @unlink($file);
        }

        $exit = Artisan::call('vendor:publish', [
            '--provider' => WebPushServiceProvider::class,
            '--tag' => 'migrations',
        ]);

        $this->assertEquals(0, $exit);

        $createMigrations = glob($migrationsPath.'*_create_push_subscriptions_table.php');
        $upgradeMigrations = glob($migrationsPath.'*_increase_push_subscriptions_endpoint_length.php');

        $this->assertNotEmpty($createMigrations, 'No create migration was published to the database/migrations path');
        $this->assertNotEmpty($upgradeMigrations, 'No endpoint upgrade migration was published to the database/migrations path');

        $this->assertStringEndsWith('_create_push_subscriptions_table.php', basename($createMigrations[0]));
        $this->assertStringEndsWith('_increase_push_subscriptions_endpoint_length.php', basename($upgradeMigrations[0]));

        // Cleanup
        foreach (array_merge($createMigrations, $upgradeMigrations) as $file) {
            @unlink($file);
        }
    }

    #[Test]
    public function it_does_not_generate_duplicate_migration_if_one_already_exists(): void
    {
        $migrationsPath = $this->application->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR;

        // Ensure migrations directory exists
        if (! is_dir($migrationsPath)) {
            mkdir($migrationsPath, 0755, true);
        }

        // Ensure no existing matching migrations are present before the test
        $existing = glob($migrationsPath.'*_create_push_subscriptions_table.php');
        if (is_array($existing)) {
            foreach ($existing as $file) {
                @unlink($file);
            }
        }

        // Create a fake existing migration file in the app migrations directory that should be detected and reused
        $existingFilename = $migrationsPath.'2020_01_01_000000_create_push_subscriptions_table.php';
        file_put_contents($existingFilename, "<?php\n// existing migration\n");

        // Recompute the provider's publishes mapping so it detects the migration file we just created.
        // The service provider computes the destination filename during boot, so we need to refresh it
        // after creating the fake file to ensure vendor:publish reuses the existing file.
        $provider = new WebPushServiceProvider($this->application);
        $ref = new \ReflectionClass($provider);
        $method = $ref->getMethod('definePublishing');
        $method->invoke($provider);

        // Run vendor:publish which should reuse the existing filename rather than create a new one
        $exit = Artisan::call('vendor:publish', [
            '--provider' => WebPushServiceProvider::class,
            '--tag' => 'migrations',
        ]);

        $this->assertEquals(0, $exit);

        // After publishing, ensure no additional migration with a different timestamp suffix was created
        $found = glob($migrationsPath.'*_'.'create_push_subscriptions_table.php');

        // There should be exactly one matching migration (the existing one we created)
        $this->assertNotFalse($found, 'No migration was found after publishing, expected to find the existing one.');
        $this->assertCount(1, $found, 'A duplicate migration file was created instead of reusing the existing one');

        // Cleanup
        foreach ($found as $file) {
            @unlink($file);
        }
    }

    #[Test]
    public function it_reuses_existing_migration_filename_when_present(): void
    {
        $migrationsPath = $this->application->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR;

        if (! is_dir($migrationsPath)) {
            mkdir($migrationsPath, 0755, true);
        }

        $existingFilename = $migrationsPath.'2020_01_01_000000_create_push_subscriptions_table.php';
        file_put_contents($existingFilename, "<?php\n// existing migration\n");

        // Instantiate provider and call protected method getMigrationFileName via reflection
        $provider = new WebPushServiceProvider($this->application);

        $ref = new \ReflectionClass($provider);
        $method = $ref->getMethod('getMigrationFileName');

        $result = $method->invokeArgs($provider, ['create_push_subscriptions_table.php']);

        $this->assertEquals($existingFilename, $result, 'getMigrationFileName did not return the existing migration filename');

        @unlink($existingFilename);
    }
}
