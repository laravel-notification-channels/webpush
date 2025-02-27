<?php

namespace NotificationChannels\WebPush\Test;

use PHPUnit\Framework\Attributes\Test;

class HasPushSubscriptionsTest extends TestCase
{
    #[Test]
    public function model_has_subscriptions(): void
    {
        $this->createSubscription($this->testUser, 'foo');
        $this->createSubscription($this->testUser, 'bar');

        $this->assertEquals(2, count($this->testUser->routeNotificationForWebPush()));
        $this->assertTrue($this->testUser->pushSubscriptions()->where('endpoint', 'foo')->exists());
        $this->assertTrue($this->testUser->pushSubscriptions()->where('endpoint', 'bar')->exists());
    }

    #[Test]
    public function subscription_can_be_created(): void
    {
        $this->testUser->updatePushSubscription('foo', 'key', 'token', 'aesgcm');
        $subscription = $this->testUser->pushSubscriptions()->first();

        $this->assertEquals('foo', $subscription->endpoint);
        $this->assertEquals('key', $subscription->public_key);
        $this->assertEquals('token', $subscription->auth_token);
        $this->assertEquals('aesgcm', $subscription->content_encoding);
    }

    #[Test]
    public function exiting_subscription_can_be_updated_by_endpoint(): void
    {
        $this->testUser->updatePushSubscription('foo', 'key', 'token');
        $this->testUser->updatePushSubscription('foo', 'major-key', 'another-token');

        $subscriptions = $this->testUser->pushSubscriptions()->where('endpoint', 'foo')->get();

        $this->assertEquals(1, count($subscriptions));
        $this->assertEquals('major-key', $subscriptions[0]->public_key);
        $this->assertEquals('another-token', $subscriptions[0]->auth_token);
    }

    #[Test]
    public function determinte_if_model_owns_subscription(): void
    {
        $subscription = $this->testUser->updatePushSubscription('foo');

        $this->assertTrue($this->testUser->ownsPushSubscription($subscription));
    }

    #[Test]
    public function subscription_owned_by_another_model_is_deleted_and_saved_for_the_new_model(): void
    {
        $otherUser = $this->createUser(['email' => 'other@user.com']);
        $otherUser->updatePushSubscription('foo');

        $this->testUser->updatePushSubscription('foo');

        $this->assertEquals(0, count($otherUser->pushSubscriptions));
        $this->assertEquals(1, count($this->testUser->pushSubscriptions));
    }

    #[Test]
    public function subscription_can_be_deleted_by_endpoint(): void
    {
        $this->testUser->updatePushSubscription('foo');
        $this->testUser->deletePushSubscription('foo');

        $this->assertEquals(0, count($this->testUser->pushSubscriptions));
    }
}
