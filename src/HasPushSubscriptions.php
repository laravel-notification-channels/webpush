<?php

namespace NotificationChannels\WebPush;

trait HasPushSubscriptions
{
    /**
     *  Get all of the subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function pushSubscriptions()
    {
        return $this->morphMany(config('webpush.model'), 'subscribable');
    }

    /**
     * Update (or create) subscription.
     *
     * @param  string  $endpoint
     * @param  string|null  $keys
     * @param  string|null  $contentEncoding
     * @return \NotificationChannels\WebPush\PushSubscription
     */
    public function updatePushSubscription($endpoint, $keys = null, $contentEncoding = null)
    {
        $subscription = app(config('webpush.model'))->findByEndpoint($endpoint);

        if ($subscription && $this->ownsPushSubscription($subscription)) {
            $subscription->update([
                'keys' => $keys,
                'content_encoding' => $contentEncoding,
            ]);

            return $subscription;
        }

        if ($subscription && ! $this->ownsPushSubscription($subscription)) {
            $subscription->delete();
        }

        return $this->pushSubscriptions()->create([
            'endpoint' => $endpoint,
            'keys' => $keys,
            'content_encoding' => $contentEncoding,
        ]);
    }

    /**
     * Determine if the model owns the given subscription.
     *
     * @param  \NotificationChannels\WebPush\PushSubscription  $subscription
     * @return bool
     */
    public function ownsPushSubscription($subscription)
    {
        return (string) $subscription->subscribable_id === (string) $this->getKey() &&
                        $subscription->subscribable_type === $this->getMorphClass();
    }

    /**
     * Delete subscription by endpoint.
     *
     * @param  string  $endpoint
     * @return void
     */
    public function deletePushSubscription($endpoint)
    {
        $this->pushSubscriptions()
            ->where('endpoint', $endpoint)
            ->delete();
    }

    /**
     * Get all of the subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function routeNotificationForWebPush()
    {
        return $this->pushSubscriptions;
    }
}
