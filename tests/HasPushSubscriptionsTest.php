<?php

namespace NotificationChannels\WebPush\Test;

class HasPushSubscriptionsTest extends TestCase
{
    /** @test */
    public function model_has_subscriptions()
    {
        $this->createSubscription($this->testUser, 'foo');
        $this->createSubscription($this->testUser, 'bar');

        $this->assertEquals(2, count($this->testUser->routeNotificationForWebPush()));
        $this->assertTrue($this->testUser->pushSubscriptions()->where('endpoint', 'foo')->exists());
        $this->assertTrue($this->testUser->pushSubscriptions()->where('endpoint', 'bar')->exists());
    }

    /** @test */
    public function subscription_can_be_created()
    {
        $this->testUser->updatePushSubscription('foo', 'keys', 'aesgcm');
        $subscription = $this->testUser->pushSubscriptions()->first();

        $this->assertEquals('foo', $subscription->endpoint);
        $this->assertEquals('keys', $subscription->keys);
        $this->assertEquals('aesgcm', $subscription->content_encoding);
    }

    /** @test */
    public function exiting_subscription_can_be_updated_by_endpoint()
    {
        $this->testUser->updatePushSubscription('foo', 'keys', 'aesgcm');
        $this->testUser->updatePushSubscription('foo', 'major-keys', 'aesgcm');
        $subscriptions = $this->testUser->pushSubscriptions()->where('endpoint', 'foo')->get();

        $this->assertEquals(1, count($subscriptions));
        $this->assertEquals('major-keys', $subscriptions[0]->keys);
    }

    /** @test */
    public function determinte_if_model_owns_subscription()
    {
        $subscription = $this->testUser->updatePushSubscription('foo');

        $this->assertTrue($this->testUser->ownsPushSubscription($subscription));
    }

    /** @test */
    public function subscription_owned_by_another_model_is_deleted_and_saved_for_the_new_model()
    {
        $otherUser = $this->createUser(['email' => 'other@user.com']);
        $otherUser->updatePushSubscription('foo');
        $this->testUser->updatePushSubscription('foo');

        $this->assertEquals(0, count($otherUser->pushSubscriptions));
        $this->assertEquals(1, count($this->testUser->pushSubscriptions));
    }

    /** @test */
    public function subscription_can_be_deleted_by_endpoint()
    {
        $this->testUser->updatePushSubscription('foo');
        $this->testUser->deletePushSubscription('foo');

        $this->assertEquals(0, count($this->testUser->pushSubscriptions));
    }
}
