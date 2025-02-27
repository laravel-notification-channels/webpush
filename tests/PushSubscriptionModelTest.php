<?php

namespace NotificationChannels\WebPush\Test;

use NotificationChannels\WebPush\PushSubscription;
use PHPUnit\Framework\Attributes\Test;

class PushSubscriptionModelTest extends TestCase
{
    #[Test]
    public function attributes_are_fillable(): void
    {
        $subscription = new PushSubscription([
            'endpoint' => 'endpoint',
            'public_key' => 'key',
            'auth_token' => 'token',
            'content_encoding' => 'aesgcm',
        ]);

        $this->assertEquals('endpoint', $subscription->endpoint);
        $this->assertEquals('key', $subscription->public_key);
        $this->assertEquals('token', $subscription->auth_token);
        $this->assertEquals('aesgcm', $subscription->content_encoding);
    }

    #[Test]
    public function subscription_can_be_found_by_endpoint(): void
    {
        $this->testUser->updatePushSubscription('endpoint');
        $subscription = PushSubscription::findByEndpoint('endpoint');

        $this->assertEquals('endpoint', $subscription->endpoint);
    }

    #[Test]
    public function subscription_has_owner_model(): void
    {
        $this->testUser->updatePushSubscription('endpoint');
        $subscription = PushSubscription::findByEndpoint('endpoint');

        $this->assertEquals($this->testUser->id, $subscription->subscribable_id);
        $this->assertEquals($this->testUser::class, $subscription->subscribable_type);
    }
}
