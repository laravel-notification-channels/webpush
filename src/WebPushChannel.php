<?php

namespace NotificationChannels\WebPush;

use Generator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;
use Minishlink\WebPush\ContentEncoding;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushChannel
{
    public function __construct(protected WebPush $webPush, protected ReportHandlerInterface $reportHandler)
    {
        //
    }

    /**
     * Send the given notification.
     *
     * @return array<int, MessageSentReport>
     */
    public function send(mixed $notifiable, Notification $notification): array
    {
        if (! is_object($notifiable) || ! method_exists($notifiable, 'routeNotificationFor')) {
            return [];
        }

        /** @var Collection<array-key, PushSubscription> $subscriptions */
        $subscriptions = $notifiable->routeNotificationFor('WebPush', $notification);

        if ($subscriptions->isEmpty()) {
            return [];
        }

        /** @var WebPushMessageInterface $message */
        // @phpstan-ignore-next-line
        $message = $notification->toWebPush($notifiable, $notification);
        $payload = json_encode($message->toArray(), JSON_THROW_ON_ERROR);
        $options = $message->getOptions();

        /** @var PushSubscription $subscription */
        foreach ($subscriptions as $subscription) {
            $this->webPush->queueNotification(new Subscription(
                $subscription->endpoint,
                $subscription->public_key,
                $subscription->auth_token,
                $subscription->content_encoding ?? ContentEncoding::aes128gcm
            ), $payload, $options);
        }

        $reports = $this->webPush->flush();

        return $this->handleReports($reports, $subscriptions, $message);
    }

    /**
     * Handle the reports.
     *
     * @param  Collection<array-key, PushSubscription>  $subscriptions
     * @return array<int, MessageSentReport>
     */
    protected function handleReports(Generator $reports, Collection $subscriptions, WebPushMessageInterface $message): array
    {
        $handledReports = [];

        foreach ($reports as $report) {
            /** @var MessageSentReport $report */
            $subscription = $this->findSubscription($subscriptions, $report);

            if (filled($subscription)) {
                $this->reportHandler->handleReport($report, $subscription, $message);
            }

            $handledReports[] = $report;
        }

        return $handledReports;
    }

    /**
     * @param  Collection<array-key, PushSubscription>  $subscriptions
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
