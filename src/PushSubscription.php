<?php

namespace NotificationChannels\WebPush;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int|string $subscribable_id
 * @property class-string $subscribable_type
 * @property string $endpoint
 * @property string|null $public_key
 * @property string|null $auth_token
 * @property string|null $content_encoding
 */
class PushSubscription extends Model
{
    /**
     * The attributes that are mass assignable.
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
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        if ($this->connection === null) {
            $this->setConnection(config('webpush.database_connection'));
        }

        if ($this->table === null) {
            $this->setTable(config('webpush.table_name'));
        }

        parent::__construct($attributes);
    }

    /**
     * Get the model related to the subscription.
     */
    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Find a subscription by the given endpoint.
     */
    public static function findByEndpoint(string $endpoint): ?static
    {
        return static::firstWhere('endpoint', $endpoint);
    }
}
