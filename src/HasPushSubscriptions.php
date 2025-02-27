<?php

namespace NotificationChannels\WebPush;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasPushSubscriptions
{
    /**
     *  Get all of the subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<PushSubscription, $this>
     */
    public function pushSubscriptions(): MorphMany
    {
        return $this->morphMany(config('webpush.model'), 'subscribable');
    }

    /**
     * Update (or create) subscription.
     */
    public function updatePushSubscription(string $endpoint, ?string $key = null, ?string $token = null, ?string $contentEncoding = null): PushSubscription
    {
        $subscription = app(config('webpush.model'))->findByEndpoint($endpoint);

        if ($subscription && $this->ownsPushSubscription($subscription)) {
            $subscription->public_key = $key;
            $subscription->auth_token = $token;
            $subscription->content_encoding = $contentEncoding;
            $subscription->save();

            return $subscription;
        }

        if ($subscription && ! $this->ownsPushSubscription($subscription)) {
            $subscription->delete();
        }

        return $this->pushSubscriptions()->create([
            'endpoint' => $endpoint,
            'public_key' => $key,
            'auth_token' => $token,
            'content_encoding' => $contentEncoding,
        ]);
    }

    /**
     * Determine if the model owns the given subscription.
     */
    public function ownsPushSubscription(PushSubscription $subscription): bool
    {
        return (string) $subscription->subscribable_id === (string) $this->getKey() &&
                        $subscription->subscribable_type === $this->getMorphClass();
    }

    /**
     * Delete subscription by endpoint.
     */
    public function deletePushSubscription(string $endpoint): void
    {
        $this->pushSubscriptions()
            ->where('endpoint', $endpoint)
            ->delete();
    }

    /**
     * Get all of the subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Collection<array-key, \NotificationChannels\WebPush\PushSubscription>
     */
    public function routeNotificationForWebPush(): Collection
    {
        return $this->pushSubscriptions;
    }
}
