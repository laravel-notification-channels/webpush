<?php

namespace NotificationChannels\WebPush\Test;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /** @var \NotificationChannels\WebPush\Test\User */
    protected $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();

        $this->testUser = User::first();
    }

    /**
     * Set up the environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', User::class);
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<array-key, class-string>
     */
    protected function getPackageProviders($app)
    {
        return [
            \NotificationChannels\WebPush\WebPushServiceProvider::class,
        ];
    }

    /**
     * @return void
     */
    protected function setUpDatabase()
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('email');
        });

        $migration = require __DIR__.'/../migrations/create_push_subscriptions_table.php.stub';
        $migration->up();

        $this->createUser(['email' => 'test@user.com']);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return \NotificationChannels\WebPush\Test\User
     */
    public function createUser(array $attributes)
    {
        return User::create($attributes);
    }

    /**
     * @param  \NotificationChannels\WebPush\Test\User  $user
     * @param  string  $endpoint
     * @return \NotificationChannels\WebPush\PushSubscription
     */
    public function createSubscription($user, $endpoint = 'endpoint')
    {
        return $user->pushSubscriptions()->create([
            'user_id' => $user->id,
            'endpoint' => $endpoint,
            'public_key' => 'key',
            'auth_token' => 'token',
        ]);
    }

    /**
     * @return void
     */
    protected function seeInConsoleOutput(string $expectedText)
    {
        $consoleOutput = $this->app[Kernel::class]->output();

        $this->assertStringContainsString($expectedText, $consoleOutput, sprintf('Did not see `%s` in console output: `%s`', $expectedText, $consoleOutput));
    }
}
