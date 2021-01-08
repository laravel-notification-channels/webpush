<?php

namespace NotificationChannels\WebPush\Events;

use Illuminate\Queue\SerializesModels;

class NotificationFailed
{
    use SerializesModels;

    /**
     * @var \Minishlink\WebPush\MessageSentReport
     */
    public $report;

    /**
     * @var \NotificationChannels\WebPush\PushSubscription
     */
    public $subscription;

    /**
     * @var \NotificationChannels\WebPush\WebPushMessage
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param \Minishlink\WebPush\MessageSentReport $report
     * @param \NotificationChannels\WebPush\PushSubscription $subscription
     * @param \NotificationChannels\WebPush\WebPushMessage $message
     * @return void
     */
    public function __construct($report, $subscription, $message)
    {
        $this->report = $report;
        $this->subscription = $subscription;
        $this->message = $message;
    }
}
