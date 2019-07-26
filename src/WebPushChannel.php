<?php

namespace NotificationChannels\WebPush;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
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

        $subscriptions->each(function (PushSubscription $pushSubscription) use ($payload) {
            $this->webPush->sendNotification(new Subscription(
                $pushSubscription->endpoint,
                $pushSubscription->public_key,
                $pushSubscription->auth_token,
                $pushSubscription->content_encoding
            ), $payload);
        });

        $reports = $this->webPush->flush();

        $this->deleteInvalidSubscriptions($reports, $subscriptions);
    }

    /**
     * @param  \Minishlink\WebPush\MessageSentReport[] $reports
     * @param  \Illuminate\Database\Eloquent\Collection $subscriptions
     * @return void
     */
    protected function deleteInvalidSubscriptions($reports, $subscriptions)
    {
        foreach ($reports as $report) {
            if (is_null($report) || $report->isSuccess()) {
                continue;
            }

            /* @var \Minishlink\WebPush\MessageSentReport $report */
            $subscriptions->each(function ($subscription) use ($report) {
                if ($subscription->endpoint === $report->getEndpoint()) {
                    logger()->warning('deleting subscription cause of '.$report->getReason());
                    $subscription->delete();
                }
            });
        }
    }
}
