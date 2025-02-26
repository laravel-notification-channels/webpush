<?php

namespace NotificationChannels\WebPush\Test;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class TestNotification extends Notification
{
    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush(mixed $notifiable, mixed $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->data(['id' => 1])
            ->title('Title')
            ->icon('Icon')
            ->body('Body')
            ->action('Title', 'Action')
            ->options(['ttl' => 60]);
    }
}
