<?php

namespace NotificationChannels\WebPush;

interface ReportHandlerInterface
{
    /**
     * Handle a message sent report.
     *
     * @param \Minishlink\WebPush\MessageSentReport $report
     * @param \NotificationChannels\WebPush\PushSubscription $subscription
     * @param \NotificationChannels\WebPush\WebPushMessage $message
     * @return void
     */
    public function handleReport($report, $subscription, $message);
}
