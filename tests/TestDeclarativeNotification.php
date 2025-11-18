<?php

namespace NotificationChannels\WebPush\Test;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\DeclarativeWebPushMessage;

class TestDeclarativeNotification extends Notification
{
    /**
     * Get the declarative web push representation of the notification.
     */
    public function toWebPush(mixed $notifiable, mixed $notification): DeclarativeWebPushMessage
    {
        return (new DeclarativeWebPushMessage)
            ->data(['id' => 1])
            ->title('Title')
            ->navigate('https://example.com')
            ->icon('Icon')
            ->body('Body')
            ->action('Title', 'Action', 'https://example.com/action')
            ->options(['ttl' => 60]);
    }
}
