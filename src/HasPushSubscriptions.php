<?php

namespace NotificationChannels\WebPush;

trait HasPushSubscriptions
{
    /**
     * Get the user's push subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    /**
     * Update (or create) user subscription.
     *
     * @param  string $endpoint
     * @param  string|null $key
     * @param  string|null $token
     *
     * @return PushSubscription
     */
    public function updatePushSubscription($endpoint, $key = null, $token = null)
    {
        $subscription = PushSubscription::findByEndpoint($endpoint);

        if ($subscription && ! $this->subscriptionBelongsToDifferentUser($subscription)) {
            $subscription->public_key = $key;
            $subscription->auth_token = $token;
            $subscription->save();

            return $subscription;
        }

        if ($subscription && $this->subscriptionBelongsToDifferentUser($subscription)) {
            $subscription->delete();
        }


        return $this->pushSubscriptions()->save(new PushSubscription([
            'endpoint' => $endpoint,
            'public_key' => $key,
            'auth_token' => $token,
        ]));
    }

    /**
     * @param PushSubscription $subscription
     * @return bool
     */
    public function subscriptionBelongsToDifferentUser($subscription)
    {
        return (int) $subscription->user_id !== (int) $this->getAuthIdentifier();
    }

    /**
     * Delete subscription by endpoint.
     *
     * @param  string $endpoint
     * @return void
     */
    public function deleteSubscription($endpoint)
    {
        $this->pushSubscriptions()
            ->where('endpoint', $endpoint)
            ->delete();
    }

    /**
     * Get all subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function routeNotificationForWebPush()
    {
        return $this->pushSubscriptions;
    }
}
