<?php

declare(strict_types=1);

namespace NotificationChannels\WebPush;

use Illuminate\Contracts\Events\Dispatcher;
use Minishlink\WebPush\MessageSentReport;
use NotificationChannels\WebPush\Events\NotificationFailed;
use NotificationChannels\WebPush\Events\NotificationSent;

class ReportHandler implements ReportHandlerInterface
{
    /**
     * Create a new report handler.
     */
    public function __construct(protected Dispatcher $events)
    {
        //
    }

    /**
     * Handle a message sent report.
     */
    public function handleReport(MessageSentReport $report, PushSubscription $subscription, WebPushMessageInterface $message): void
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
