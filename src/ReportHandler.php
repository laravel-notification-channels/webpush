<?php

namespace NotificationChannels\WebPush;

use Illuminate\Contracts\Events\Dispatcher;
use NotificationChannels\WebPush\Events\NotificationFailed;
use NotificationChannels\WebPush\Events\NotificationSent;

class ReportHandler implements ReportHandlerInterface
{
    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * Create a new report handler.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Handle a message sent report.
     *
     * @param  \Minishlink\WebPush\MessageSentReport  $report
     * @param  \NotificationChannels\WebPush\PushSubscription  $subscription
     * @param  \NotificationChannels\WebPush\WebPushMessage  $message
     * @return void
     */
    public function handleReport($report, $subscription, $message)
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
