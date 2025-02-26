<?php

namespace NotificationChannels\WebPush\Test;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasPushSubscriptions;
    use Notifiable;

    public $timestamps = false;

    protected $fillable = ['email'];
}
