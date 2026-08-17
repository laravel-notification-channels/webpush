<?php

namespace NotificationChannels\WebPush\Test;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use NotificationChannels\WebPush\PushSubscription;
use PHPUnit\Framework\Attributes\Test;

class PushSubscriptionEndpointTest extends TestCase
{
    #[Test]
    public function fresh_install_migration_supports_endpoints_longer_than_500_characters(): void
    {
        $endpoint = $this->endpointExceedingLegacyLimit();

        $this->testUser->updatePushSubscription($endpoint);

        $this->assertNotNull(PushSubscription::findByEndpoint($endpoint));
    }

    #[Test]
    public function upgrade_migration_expands_legacy_endpoint_column(): void
    {
        /** @var string|null $connection */
        $connection = config('webpush.database_connection');
        /** @var string $table */
        $table = config('webpush.table_name');

        Schema::connection($connection)->drop($table);

        Schema::connection($connection)->create($table, function (Blueprint $blueprint): void {
            $blueprint->bigIncrements('id');
            $blueprint->morphs('subscribable', 'push_subscriptions_subscribable_morph_idx');
            $blueprint->string('endpoint', 500)->unique();
            $blueprint->string('public_key')->nullable();
            $blueprint->string('auth_token')->nullable();
            $blueprint->string('content_encoding')->nullable();
            $blueprint->timestamps();
        });

        $upgradeMigration = require __DIR__.'/../migrations/increase_push_subscriptions_endpoint_length.php.stub';
        $upgradeMigration->up();

        $longEndpoint = $this->endpointExceedingLegacyLimit();

        $this->testUser->updatePushSubscription($longEndpoint);

        $this->assertNotNull(PushSubscription::findByEndpoint($longEndpoint));
    }

    private function endpointExceedingLegacyLimit(): string
    {
        return 'https://push.example/'.str_repeat('a', 520);
    }
}
