<?php

declare(strict_types=1);

namespace NotificationChannels\WebPush;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @extends Arrayable<string, mixed>
 */
interface WebPushMessageInterface extends Arrayable
{
    /** @return array<string, mixed> */
    public function getOptions(): array;
}
