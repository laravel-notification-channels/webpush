# Changelog

All Notable changes to `laravel-notification-channels/webpush` will be documented in this file

## 7.1.0 - 2023-03-14

- Added support for Laravel 10.

## 7.0.0 - 2022-03-29

- Upgrade web-push dependency [#172](https://github.com/laravel-notification-channels/webpush/pull/172). 

## 6.0.0 - 2022-01-26

- Added support for Laravel 9.
- Dropped support for Laravel < 8 and PHP < 8.

## 5.1.1 - 2021-01-08

- Fixed action without icon [#130](https://github.com/laravel-notification-channels/webpush/issues/130).

## 5.1.0 - 2021-01-08

- Added PHP 8.0 support [#150](https://github.com/laravel-notification-channels/webpush/pull/150).
- Added `NotificationSent` and `NotificationFailed` [events](/src/Events).
- Removed `Log::warning` from `ReportHandler`.
- Switched to GitHub actions. 

## 5.0.3 - 2020-08-19

- Laravel 8.0 compatibility 

## 5.0.2 - 2020-03-05

- Laravel 7.0 compatibility 

## 5.0.1 - 2020-01-24

- Added the icon parameter to the action method.

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
