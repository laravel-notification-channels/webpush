<?php

namespace NotificationChannels\WebPush;

use Illuminate\Notifications\Notification;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushChannel
{
    /**
     * @var \Minishlink\WebPush\WebPush
     */
    protected $webPush;

    /**
     * @var \NotificationChannels\WebPush\ReportHandlerInterface
     */
    protected $reportHandler;

    /**
     * @param  \Minishlink\WebPush\WebPush $webPush
     * @param  \NotificationChannels\WebPush\ReportHandlerInterface $webPush
     * @return void
     */
    public function __construct(WebPush $webPush, ReportHandlerInterface $reportHandler)
    {
        $this->webPush = $webPush;
        $this->reportHandler = $reportHandler;
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
        /** @var \Illuminate\Database\Eloquent\Collection $subscriptions */
        $subscriptions = $notifiable->routeNotificationFor('WebPush', $notification);

        if ($subscriptions->isEmpty()) {
            return;
        }

        /** @var \NotificationChannels\WebPush\WebPushMessage $message */
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
     * @param  \Generator $reports
     * @param  \Illuminate\Database\Eloquent\Collection $subscriptions
     * @param  \NotificationChannels\WebPush\WebPushMessage $message
     * @return void
     */
    protected function handleReports($reports, $subscriptions, $message)
    {
        /** @var \Minishlink\WebPush\MessageSentReport $report */
        foreach ($reports as $report) {
            if ($report && $subscription = $this->findSubscription($subscriptions, $report)) {
                $this->reportHandler->handleReport($report, $subscription, $message);
            }
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $subscriptions
     * @param \Minishlink\WebPush\MessageSentReport $report
     * @return void
     */
    protected function findSubscription($subscriptions, $report)
    {
        foreach ($subscriptions as $subscription) {
            if ($subscription->endpoint === $report->getEndpoint()) {
                return $subscription;
            }
        }
    }
}
