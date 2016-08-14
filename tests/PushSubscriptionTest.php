<?php

namespace NotificationChannels\WebPush\Test;

use NotificationChannels\WebPush\PushSubscription;

class PushSubscriptionTest extends TestCase
{
    /** @test */
    public function it_has_fillable_attributes()
    {
        $sub = new PushSubscription([
            'endpoint' => 'endpoint',
            'public_key' => 'key',
            'auth_token' => 'token',
        ]);

        $this->assertEquals('endpoint', $sub->endpoint);
        $this->assertEquals('key', $sub->public_key);
        $this->assertEquals('token', $sub->auth_token);
    }

    /** @test */
    public function it_can_find_subscription_by_endpoint()
    {
        $this->testUser->updatePushSubscription('endpoint');
        $sub = PushSubscription::findByEndpoint('endpoint');

        $this->assertEquals('endpoint', $sub->endpoint);
    }

    /** @test */
    public function it_can_get_user()
    {
        $this->testUser->updatePushSubscription('endpoint');
        $sub = PushSubscription::findByEndpoint('endpoint');

        $this->assertEquals($this->testUser->id, $sub->user->id);
    }
}
