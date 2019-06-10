<?php

namespace NotificationChannels\WebPush;

trait HasPushSubscriptions
{
    /**
     * Get the user's subscriptions.
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
     * @param  string|null $contentEncoding
     * @return \NotificationChannels\WebPush\PushSubscription
     */
    public function updatePushSubscription($endpoint, $key = null, $token = null, $contentEncoding = null)
    {
        $subscription = PushSubscription::findByEndpoint($endpoint);

        if ($subscription && $this->pushSubscriptionBelongsToUser($subscription)) {
            $subscription->public_key = $key;
            $subscription->auth_token = $token;
            $subscription->content_encoding = $contentEncoding;
            $subscription->save();

            return $subscription;
        }

        if ($subscription && ! $this->pushSubscriptionBelongsToUser($subscription)) {
            $subscription->delete();
        }

        return $this->pushSubscriptions()->save(new PushSubscription([
            'endpoint' => $endpoint,
            'public_key' => $key,
            'auth_token' => $token,
            'content_encoding' => $contentEncoding,
        ]));
    }

    /**
     * Determine if the given subscription belongs to this user.
     *
     * @param  \NotificationChannels\WebPush\PushSubscription $subscription
     * @return bool
     */
    public function pushSubscriptionBelongsToUser($subscription)
    {
        return (int) $subscription->user_id === (int) $this->getAuthIdentifier();
    }

    /**
     * Delete subscription by endpoint.
     *
     * @param  string $endpoint
     * @return void
     */
    public function deletePushSubscription($endpoint)
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
