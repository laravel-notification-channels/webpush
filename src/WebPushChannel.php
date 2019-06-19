<?php

namespace NotificationChannels\WebPush;

use Minishlink\WebPush\WebPush;
use Illuminate\Notifications\Notification;

class WebPushChannel
{
    /** @var \Minishlink\WebPush\WebPush */
    protected $webPush;

    /**
     * @param  \Minishlink\WebPush\WebPush $webPush
     * @return void
     */
    public function __construct(WebPush $webPush)
    {
        $this->webPush = $webPush;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $subscriptions = $notifiable->routeNotificationFor('WebPush');

        if (! $subscriptions || $subscriptions->isEmpty()) {
            return;
        }

        $webPushMessage = $notification->toWebPush($notifiable, $notification);
        $webPushMessageArr = $webPushMessage->toArray();

        $options = [];
        if (isset($webPushMessageArr['options'])) {
            $options = $webPushMessageArr['options'];
            unset($webPushMessageArr['options']);
        }

        $payload = json_encode($webPushMessageArr);

        $subscriptions->each(function ($sub) use ($payload, $options) {
            $this->webPush->sendNotification(
                $sub->endpoint,
                $payload,
                $sub->public_key,
                $sub->auth_token,
                $options
            );
        });

        $response = $this->webPush->flush();

        $this->deleteInvalidSubscriptions($response, $subscriptions);
    }

    /**
     * @param  array|bool $response
     * @param  \Illuminate\Database\Eloquent\Collection $subscriptions
     * @return void
     */
    protected function deleteInvalidSubscriptions($response, $subscriptions)
    {
        if (! is_array($response)) {
            return;
        }

        foreach ($response as $index => $value) {
            if (! $value['success'] && isset($subscriptions[$index])) {
                $subscriptions[$index]->delete();
            }
        }
    }
}
