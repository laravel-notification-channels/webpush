# Web push notifications channel for Laravel 5.3 [WIP]

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/webpush.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/webpush)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-notification-channels/webpush/master.svg?style=flat-square)](https://travis-ci.org/laravel-notification-channels/webpush)
[![StyleCI](https://styleci.io/repos/65542206/shield)](https://styleci.io/repos/65542206)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/6ac8b6d5-c215-4ba5-9a47-d1b312ec196d.svg?style=flat-square)](https://insight.sensiolabs.com/projects/6ac8b6d5-c215-4ba5-9a47-d1b312ec196d)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/webpush.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/webpush)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/webpush/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/webpush/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/webpush.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/webpush)

This package makes it easy to send web push notifications with Laravel 5.3.


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
    NotificationChannels\WebPush\WebPushServiceProvider::class,
],
```

Then configure [Google Cloud Messaging](https://console.cloud.google.com) by setting your `key` and `sender_id`:

``` php
// config/services.php
'gcm' => [
    'key' => '',
    'sender_id' => ',
],
```

You need to add the `NotificationChannels\WebPush\HasPushSubscriptions` in your `User` model:

``` php
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Model
{
    use HasPushSubscriptions;
}
```

Next publish the migration with:

``` bash
php artisan vendor:publish --provider="NotificationChannels\WebPush\WebPushServiceProvider" --tag="migrations"
```

After the migration has been published you can create the `push_subscriptions` table by running the migrations:

``` bash
php artisan migrate
```

## Usage

Now you can use the channel in your `via()` method inside the notification as well as send a web push notification:

``` php
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
        return WebPushMessage::create()
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

To delete a subscription use the `deletePushSubscription($endpoint)` method on your user:

``` php
$user = \App\User::find(1);

$user->deletePushSubscription($endpoint);
```

## Demo

For a complete implementation with a Service Worker check this [demo](https://github.com/cretueusebiu/laravel-web-push-demo).

## Browser Compatibility

The [Push API](https://developer.mozilla.org/en/docs/Web/API/Push_API) currently works on Chrome and Firefox.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email themsaid@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Cretu Eusebiu](https://github.com/cretueusebiu)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
