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

        $payload = json_encode($notification->toWebPush($notifiable, $notification)->toArray());

        $subscriptions->each(function ($sub) use ($payload) {
            $this->webPush->sendNotification(
                $sub->endpoint,
                $payload,
                $sub->public_key,
                $sub->auth_token
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
        if (!is_array($response)) {
            return;
        }

        foreach ($response as $index => $value) {
            list($success, $statusCode) = $value;

            // Continue when the request was successful
            if ($success) {
                continue;
            }

            // Remove subscription if the server responded with a 404 or 410 code
            // https://developers.google.com/web/fundamentals/push-notifications/common-issues-and-reporting-bugs#http_status_codes
            if (in_array($statusCode, [404, 410])) {
                $subsciption = $subscriptions[$index];
                $subsciption->delete();
            }
        }
    }
}
