# Web Push Notifications Channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/webpush.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/webpush)
![Build Status](https://github.com/laravel-notification-channels/webpush/workflows/tests/badge.svg)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/webpush.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/webpush)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/webpush/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/webpush/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/webpush.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/webpush)

This package makes it easy to send web push notifications with Laravel.

## Installation

You can install the package via Composer:

```bash
composer require laravel-notification-channels/webpush
```

First, add the `NotificationChannels\WebPush\HasPushSubscriptions` trait to your `User` model:

```php
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Model
{
    use HasPushSubscriptions;
}
```

Next, publish the migration with:

```bash
php artisan vendor:publish --provider="NotificationChannels\WebPush\WebPushServiceProvider" --tag="migrations"
```

Run the migrate command to create the necessary table:

```bash
php artisan migrate
```

You can also publish the config file with:

```bash
php artisan vendor:publish --provider="NotificationChannels\WebPush\WebPushServiceProvider" --tag="config"
```

Generate the VAPID keys (required for browser authentication) with:

```bash
php artisan webpush:vapid
```

This command will set `VAPID_PUBLIC_KEY` and `VAPID_PRIVATE_KEY` in your `.env` file. You need the `VAPID_PUBLIC_KEY` as `applicationServerKey` when using the [Push API](https://developer.mozilla.org/en-US/docs/Web/API/Push_API).

> **Note:** If targeting Safari or iOS after 2023, you will need to include the `VAPID_SUBJECT` variable as well, or Apple will return a `BadJwtToken` error.

__These keys must be safely stored and should not change.__

## Usage

Now you can use the channel in your `via()` method inside the notification and send a web push notification:

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Approved!')
            ->icon('/approved-icon.png')
            ->body('Your account was approved!')
            ->action('View account', 'view_account')
            ->options(['TTL' => 1000]);
            // ->data(['id' => $notification->id])
            // ->badge()
            // ->dir()
            // ->image()
            // ->lang()
            // ->renotify()
            // ->requireInteraction()
            // ->tag()
            // ->vibrate()
    }
}
```

You can find the available options [here](https://github.com/web-push-libs/web-push-php#notifications-and-default-options).

### Save/Update Subscriptions

To save or update a subscription, use the `updatePushSubscription($endpoint, $key = null, $token = null, $contentEncoding = null)` method on your user:

```php
$user = \App\User::find(1);

$user->updatePushSubscription($endpoint, $key, $token, $contentEncoding);
```

The `$key` and `$token` are optional and are used to encrypt your notifications. However, all major browsers require encryption when sending notifications.

When using the [Push API](https://developer.mozilla.org/en-US/docs/Web/API/Push_API), `$key` is the value of the `getKey('p256dh')` method, and `$token` is the value of the `getKey('auth')` method of the `PushSubscription` interface.

### Delete Subscriptions

To delete a subscription, use the `deletePushSubscription($endpoint)` method on your user:

```php
$user = \App\User::find(1);

$user->deletePushSubscription($endpoint);
```

## Browser Compatibility

See the [Push API](https://caniuse.com/#feat=push-api) browser compatibility.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information about what has changed recently.

## Testing

```bash
composer test
```

## Security

If you discover any security-related issues, please email themsaid@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Cretu Eusebiu](https://github.com/cretueusebiu)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
