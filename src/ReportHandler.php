<?php

namespace NotificationChannels\WebPush;

use Minishlink\WebPush\MessageSentReport;
use NotificationChannels\WebPush\Events\NotificationFailed;
use NotificationChannels\WebPush\Events\NotificationSent;

class ReportHandler implements ReportHandlerInterface
{
    /**
     * Create a new report handler.
     *
     * @return void
     */
    public function __construct(protected \Illuminate\Contracts\Events\Dispatcher $events)
    {
        //
    }

    /**
     * Handle a message sent report.
     */
    public function handleReport(MessageSentReport $report, PushSubscription $subscription, WebPushMessage $message): void
    {
        if ($report->isSuccess()) {
            $this->events->dispatch(new NotificationSent($report, $subscription, $message));

            return;
        }

        if ($report->isSubscriptionExpired()) {
            $subscription->delete();
        }

        $this->events->dispatch(new NotificationFailed($report, $subscription, $message));
    }
}
