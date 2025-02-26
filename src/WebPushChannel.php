<?php

namespace NotificationChannels\WebPush;

use Generator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushChannel
{
    /**
     * @return void
     */
    public function __construct(protected WebPush $webPush, protected ReportHandlerInterface $reportHandler)
    {
        //
    }

    /**
     * Send the given notification.
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection<array-key, PushSubscription> $subscriptions */
        $subscriptions = $notifiable->routeNotificationFor('WebPush', $notification);

        if ($subscriptions->isEmpty()) {
            return;
        }

        /** @var \NotificationChannels\WebPush\WebPushMessage $message */
        // @phpstan-ignore-next-line
        $message = $notification->toWebPush($notifiable, $notification);
        $payload = json_encode($message->toArray());
        $options = $message->getOptions();

        /** @var \NotificationChannels\WebPush\PushSubscription $subscription */
        foreach ($subscriptions as $subscription) {
            $this->webPush->queueNotification(new Subscription(
                $subscription->endpoint,
                $subscription->public_key,
                $subscription->auth_token,
                $subscription->content_encoding
            ), $payload, $options);
        }

        $reports = $this->webPush->flush();

        $this->handleReports($reports, $subscriptions, $message);
    }

    /**
     * Handle the reports.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<array-key, PushSubscription>  $subscriptions
     */
    protected function handleReports(Generator $reports, Collection $subscriptions, WebPushMessage $message): void
    {
        foreach ($reports as $report) {
            /** @var \Minishlink\WebPush\MessageSentReport $report */
            $subscription = $this->findSubscription($subscriptions, $report);

            if (filled($subscription)) {
                $this->reportHandler->handleReport($report, $subscription, $message);
            }
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<array-key, PushSubscription>  $subscriptions
     */
    protected function findSubscription(Collection $subscriptions, MessageSentReport $report): ?PushSubscription
    {
        foreach ($subscriptions as $subscription) {
            if ($subscription->endpoint === $report->getEndpoint()) {
                return $subscription;
            }
        }

        return null;
    }
}
