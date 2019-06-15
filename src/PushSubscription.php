<?php

namespace NotificationChannels\WebPush;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'endpoint',
        'public_key',
        'auth_token',
    ];

    /**
     * Get the user that owns the subscription.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function user()
    {
        $model = Config::get('webpush.subscriber_model', Config::get('auth.providers.users.model'));
        return $this->belongsTo($model);
    }

    /**
     * Find a subscription by the given endpint.
     *
     * @param  string $endpoint
     * @return static|null
     */
    public static function findByEndpoint($endpoint)
    {
        return static::where('endpoint', $endpoint)->first();
    }
}
