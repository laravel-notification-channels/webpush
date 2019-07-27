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
        'content_encoding',
    ];

    /**
     * Create a new model instance.
     *
     * @param  array $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        if (! isset($this->connection)) {
            $this->setConnection(config('webpush.database_connection'));
        }

        if (! isset($this->table)) {
            $this->setTable(config('webpush.table_name'));
        }

        parent::__construct($attributes);
    }

    /**
     * Get the user that owns the subscription.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function user()
    {
        return $this->belongsTo(Config::get('auth.providers.users.model'));
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
