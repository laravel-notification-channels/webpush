<?php

namespace NotificationChannels\WebPush\Test;

use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasPushSubscriptions;

    public $timestamps = false;

    protected $fillable = ['email'];
}
