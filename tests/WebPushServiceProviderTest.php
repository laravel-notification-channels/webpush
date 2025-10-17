<?php

namespace NotificationChannels\WebPush\Test;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;

class WebPushServiceProviderTest extends TestCase
{
    #[Test]
    public function it_publishes_config(): void
    {
        // Ensure config directory exists in the application config path
        $configPath = config_path('webpush.php');

        if (File::exists($configPath)) {
            File::delete($configPath);
        }

        $exit = Artisan::call('vendor:publish', [
            '--provider' => \NotificationChannels\WebPush\WebPushServiceProvider::class,
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
        $migrationsPath = $this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR;

        // Remove any existing matching migrations in the database/migrations folder for a clean test
        $existing = glob($migrationsPath.'*_' . 'create_push_subscriptions_table.php');
        foreach ($existing as $file) {
            @unlink($file);
        }

        $exit = Artisan::call('vendor:publish', [
            '--provider' => \NotificationChannels\WebPush\WebPushServiceProvider::class,
            '--tag' => 'migrations',
        ]);

        $this->assertEquals(0, $exit);

        $found = glob($migrationsPath.'*_' . 'create_push_subscriptions_table.php');

        $this->assertNotEmpty($found, 'No migration was published to the database/migrations path');

        // Ensure exactly one migration file exists and has the expected suffix
        $this->assertStringEndsWith('_create_push_subscriptions_table.php', basename($found[0]));

        // Cleanup
        foreach ($found as $file) {
            @unlink($file);
        }
    }

    #[Test]
    public function it_does_not_generate_duplicate_migration_if_one_already_exists(): void
    {
        $migrationsPath = $this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR;

        // Ensure migrations directory exists
        if (! is_dir($migrationsPath)) {
            mkdir($migrationsPath, 0755, true);
        }

        // Ensure no existing matching migrations are present before the test
        $existing = glob($migrationsPath . '*_create_push_subscriptions_table.php');
        foreach ($existing as $file) {
            @unlink($file);
        }
        
        // Create a fake existing migration file in the app migrations directory that should be detected and reused
        $existingFilename = $migrationsPath.'2020_01_01_000000_create_push_subscriptions_table.php';
        file_put_contents($existingFilename, "<?php\n// existing migration\n");

        // Recompute the provider's publishes mapping so it detects the migration file we just created.
        // The service provider computes the destination filename during boot, so we need to refresh it
        // after creating the fake file to ensure vendor:publish reuses the existing file.
        $provider = new \NotificationChannels\WebPush\WebPushServiceProvider($this->app);
        $ref = new \ReflectionClass($provider);
        $method = $ref->getMethod('definePublishing');
        $method->setAccessible(true);
        $method->invoke($provider);

        // Run vendor:publish which should reuse the existing filename rather than create a new one
        $exit = Artisan::call('vendor:publish', [
            '--provider' => \NotificationChannels\WebPush\WebPushServiceProvider::class,
            '--tag' => 'migrations',
        ]);

        $this->assertEquals(0, $exit);

        // After publishing, ensure no additional migration with a different timestamp suffix was created
        $found = glob($migrationsPath.'*_' . 'create_push_subscriptions_table.php');

        // There should be exactly one matching migration (the existing one we created)
        $this->assertCount(1, $found, 'A duplicate migration file was created instead of reusing the existing one');

        // Cleanup
        foreach ($found as $file) {
            @unlink($file);
        }
    }

    #[Test]
    public function it_reuses_existing_migration_filename_when_present(): void
    {
        $migrationsPath = $this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR;

        if (! is_dir($migrationsPath)) {
            mkdir($migrationsPath, 0755, true);
        }

        $existingFilename = $migrationsPath.'2020_01_01_000000_create_push_subscriptions_table.php';
        file_put_contents($existingFilename, "<?php\n// existing migration\n");

        // Instantiate provider and call protected method getMigrationFileName via reflection
        $provider = new \NotificationChannels\WebPush\WebPushServiceProvider($this->app);

        $ref = new \ReflectionClass($provider);
        $method = $ref->getMethod('getMigrationFileName');
        $method->setAccessible(true);

        $result = $method->invokeArgs($provider, ['create_push_subscriptions_table.php']);

        $this->assertEquals($existingFilename, $result, 'getMigrationFileName did not return the existing migration filename');

        @unlink($existingFilename);
    }
}
