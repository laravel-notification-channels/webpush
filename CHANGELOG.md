# Changelog

All Notable changes to `laravel-notification-channels/webpush` will be documented in this file

## 5.0.0 - 2019-09-06

- Laravel 6.0 compatibility
- PHP 7.2 or greater is required
- Pass `$message` to [ReportHandler](/src/ReportHandler.php)

## 4.0.0 - 2019-07-28

- Upgraded to [minishlink/web-push](https://github.com/web-push-libs/web-push-php/releases) to v5
- Added `WebPushMessage::options()`
- Added [ReportHandler](/src/ReportHandler.php) to handle notification sent reports.
- Added options for customizing the model, table and connection.
- Added polymorphic relation. `HasPushSubscriptions` can now be used on any model.

## 3.0.0 - 2017-11-15

- Removed `id` and `create` methods from `WebPushMessage`.
- Added `badge`, `dir`, `image`, `lang`, `renotify`, `requireInteraction`, `tag`, `vibrate`, `data` methods on `WebPushMessage`.

## 2.0.0 - 2017-10-23

- Added support for package discovery.
- Removed compatibility with PHP<7 and upgrade deps.

## 1.0.0 - 2017-03-25

- Added support for VAPID.
- Added dedicated config file.

## 0.2.0 - 2017-01-26

- Added Laravel 5.4 compatibility.
