<?php

namespace NotificationChannels\WebPush\Exceptions;

final class MessageValidationFailed extends \Exception
{
    public static function titleRequired(): self
    {
        return new self('"title" must be set for Declarative Web Push messages');
    }

    public static function navigateRequired(): self
    {
        return new self('"navigate" must be set for Declarative Web Push messages');
    }
}
