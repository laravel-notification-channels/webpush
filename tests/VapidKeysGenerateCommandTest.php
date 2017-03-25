<?php

namespace NotificationChannels\WebPush\Test;

use Illuminate\Support\Facades\Artisan;

class VapidKeysGenerateCommandTest extends TestCase
{
    /** @test */
    public function it_can_generate_and_show_vapid_keys1()
    {
        $exitCode = Artisan::call('webpush:vapid', ['--show' => true]);

        $this->assertEquals(0, $exitCode);
        $this->seeInConsoleOutput('VAPID_PUBLIC_KEY=');
    }

    /** @test */
    public function it_can_generate_and_show_vapid_keys2()
    {
        $exitCode = Artisan::call('webpush:vapid', ['--show' => true]);

        $this->assertEquals(0, $exitCode);
        $this->seeInConsoleOutput('VAPID_PRIVATE_KEY=');
    }

    /** @test */
    public function it_can_generate_and_set_vapid_keys()
    {
        file_put_contents($envPath = __DIR__.'/temp/.env', 'APP_ENV=testing');
        $this->app->useEnvironmentPath(__DIR__.'/temp');

        $exitCode = Artisan::call('webpush:vapid');

        $this->assertEquals(0, $exitCode);
        $this->seeInConsoleOutput('VAPID keys set successfully.');

        $envContents = file_get_contents($envPath);

        $this->assertRegExp('/^VAPID_PUBLIC_KEY=/m', $envContents);
        $this->assertRegExp('/^VAPID_PRIVATE_KEY=/m', $envContents);
    }
}
