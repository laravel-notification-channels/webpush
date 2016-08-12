<?php

namespace NotificationChannels\PusherPushNotifications\Test;

class Notifiable
{
    use \Illuminate\Notifications\Notifiable;

    public function getKey()
    {
        return 1;
    }
}
