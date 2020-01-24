<?php

namespace NotificationChannels\WebPush\Test;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use Notifiable, HasPushSubscriptions;

    public $timestamps = false;

    protected $fillable = ['email'];
}
