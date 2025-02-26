<?php

namespace NotificationChannels\WebPush\Events;

use Illuminate\Queue\SerializesModels;
use Minishlink\WebPush\MessageSentReport;
use NotificationChannels\WebPush\PushSubscription;
use NotificationChannels\WebPush\WebPushMessage;

class NotificationSent
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public MessageSentReport $report, public PushSubscription $subscription, public WebPushMessage $message)
    {
        //
    }
}
