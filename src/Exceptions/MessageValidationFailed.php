<?php

namespace NotificationChannels\WebPush\Exceptions;

class MessageValidationFailed extends \Exception
{
    public static function titleRequired(): static
    {
        return new static('"title" must be set for Declarative Web Push messages');
    }

    public static function navigateRequired(): static
    {
        return new static('"navigate" must be set for Declarative Web Push messages');
    }
}
