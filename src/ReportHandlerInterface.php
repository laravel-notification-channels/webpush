<?php

namespace NotificationChannels\WebPush;

use Minishlink\WebPush\MessageSentReport;

interface ReportHandlerInterface
{
    /**
     * Handle a message sent report.
     */
    public function handleReport(MessageSentReport $report, PushSubscription $subscription, WebPushMessage $message): void;
}
