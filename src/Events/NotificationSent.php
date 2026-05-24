<?php

declare(strict_types=1);

namespace NotificationChannels\WebPush\Events;

use Illuminate\Queue\SerializesModels;
use Minishlink\WebPush\MessageSentReport;
use NotificationChannels\WebPush\PushSubscription;
use NotificationChannels\WebPush\WebPushMessageInterface;

class NotificationSent
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public MessageSentReport $report, public PushSubscription $subscription, public WebPushMessageInterface $message)
    {
        //
    }
}
