# Web push notifications channel for Laravel 5.3

## Installation

You can install the package via composer:

``` bash
composer require laravel-notification-channels/web-push-notifications
```

First you must install the service provider:

``` php
// config/app.php
'providers' => [
    ...
    NotificationChannels\WebPushNotifications\Provider::class,
];
```

Then configure [Google Cloud Messaging](https://console.cloud.google.com) by setting your `key` and `sender_id`:

``` php
'gcm' => [
    'key' => '',
    'sender_id' => ',
],
```

You need to add the `NotificationChannels\WebPushNotifications\HasPushSubscriptions` in your `User` model:

``` php
use NotificationChannels\WebPushNotifications\HasPushSubscriptions;

class User extends Model
{
    use HasPushSubscriptions;
}
```

Next publish the migration with:

``` bash
php artisan vendor:publish --provider="NotificationChannels\WebPushNotifications\Provider" --tag="migrations"
```

After the migration has been published you can create the `push_subscriptions` table by running the migrations:

``` bash
php artisan migrate
```

## Usage

Now you can use the channel in your `via()` method inside the notification as well as send a web push notification:

``` php
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPushNotifications\Message;
use NotificationChannels\WebPushNotifications\Channel as WebPushChannel;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage())
            // ->id($notification->id)
            ->title('Approved!')
            ->icon('/approved-icon.png')
            ->body('Your account was approved!')
            ->action('View account', 'view_account');
    }
}
```

### Save/Update Subscriptions

To save or update a subscription use the `updatePushSubscription($endpoint, $key = null, $token = null)` method on your user:

``` php
$user = \App\User::find(1);

$user->updatePushSubscription($endpoint, $key, $token);
```

The `$key` and `$token` are optional and are used to encrypt your notifications. Only encrypted notifications can have a payload.

### Delete Subscriptions

To delete a subscription use the `deleteSubscription($endpoint)` method on your user:

``` php
$user = \App\User::find(1);

$user->deleteSubscription($endpoint);
```

## Demo

For a complete implementation with a Service Worker check this [demo](https://github.com/cretueusebiu/laravel-web-push-demo). 

## Browser Compatibility

The [Push API](https://developer.mozilla.org/en/docs/Web/API/Push_API) currently works on Chrome and Firefox.

## Credits

- [Cretu Eusebiu](https://github.com/cretueusebiu)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
