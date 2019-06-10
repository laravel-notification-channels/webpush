<?php

namespace NotificationChannels\WebPush;

use Minishlink\WebPush\WebPush;
use Illuminate\Notifications\Notification;
use Minishlink\WebPush\MessageSentReport;

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
            $subscription = new \Minishlink\WebPush\Subscription($sub->endpoint, $sub->public_key, $sub->auth_token, $sub->content_encoding);
            $this->webPush->sendNotification($subscription, $payload);
        });

        $reports = $this->webPush->flush();

        $this->deleteInvalidSubscriptions($reports, $subscriptions);
    }

    /**
     * @param  \Generator|MessageSentReport $response
     * @param  \Illuminate\Database\Eloquent\Collection $subscriptions
     * @return void
     */
    protected function deleteInvalidSubscriptions($response, $subscriptions)
    {
        foreach ($response as $report) {
            /** @var MessageSentReport $report */
            if (! $report->isSuccess()) {
               $subscriptions->each(function($s) use($report) {
                    if($s->endpoint === $report->getEndpoint()) {
                        logger()->warning('deleting subscription cause of ' . $report->getReason());
                        $s->delete();
                    }
                });
            }
        }
    }
}
