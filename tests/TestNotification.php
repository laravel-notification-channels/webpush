<?php

namespace NotificationChannels\WebPush\Test;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class TestNotification extends Notification
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
        return (new WebPushMessage)
            ->data(['id' => 1])
            ->title('Title')
            ->icon('Icon')
            ->body('Body')
            ->action('Title', 'Action');
    }
}
