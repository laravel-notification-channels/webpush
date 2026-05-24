<?php

declare(strict_types=1);

namespace NotificationChannels\WebPush;

use Minishlink\WebPush\MessageSentReport;

interface ReportHandlerInterface
{
    /**
     * Handle a message sent report.
     */
    public function handleReport(MessageSentReport $report, PushSubscription $subscription, WebPushMessageInterface $message): void;
}
