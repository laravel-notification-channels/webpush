<?php

namespace NotificationChannels\WebPush\Test;

use NotificationChannels\WebPush\PushSubscription;

class PushSubscriptionModelTest extends TestCase
{
    /** @test */
    public function attributes_are_fillable()
    {
        $subscription = new PushSubscription([
            'endpoint' => 'endpoint',
            'keys' => 'keys',
            'content_encoding' => 'aesgcm',
        ]);

        $this->assertEquals('endpoint', $subscription->endpoint);
        $this->assertEquals('keys', $subscription->keys);
        $this->assertEquals('aesgcm', $subscription->content_encoding);
    }

    /** @test */
    public function subscription_can_be_found_by_endpoint()
    {
        $this->testUser->updatePushSubscription('endpoint');
        $subscription = PushSubscription::findByEndpoint('endpoint');

        $this->assertEquals('endpoint', $subscription->endpoint);
    }

    /** @test */
    public function subscription_has_owner_model()
    {
        $this->testUser->updatePushSubscription('endpoint');
        $subscription = PushSubscription::findByEndpoint('endpoint');

        $this->assertEquals($this->testUser->id, $subscription->subscribable_id);
        $this->assertEquals(get_class($this->testUser), $subscription->subscribable_type);
    }
}
