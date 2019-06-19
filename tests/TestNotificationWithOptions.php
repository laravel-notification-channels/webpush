<?php

namespace NotificationChannels\WebPush\Test;

class TestNotificationWithOptions extends TestNotification
{
    /**
     * Get the web push representation of the notification.
     *
     * @param  mixed  $notifiable
     * @param  mixed  $notification
     * @return \NotificationChannels\WebPush\WebPushMessage
     */
    public function toWebPush($notifiable, $notification)
    {
        return parent::toWebPush($notifiable, $notification)
            ->options(['ttl' => 60]);
    }
}
