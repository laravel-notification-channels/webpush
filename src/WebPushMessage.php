<?php

namespace NotificationChannels\WebPush;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

/**
 * @implements \Illuminate\Contracts\Support\Arrayable<string, mixed>
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerRegistration/showNotification#Parameters
 */
class WebPushMessage implements Arrayable
{
    protected string $title;

    /**
     * @var array<array-key, array{'title': string, 'action': string, 'icon'?: string}>
     */
    protected array $actions = [];

    protected string $badge;

    protected string $body;

    protected string $dir;

    protected string $icon;

    protected string $image;

    protected string $lang;

    protected bool $renotify;

    protected bool $requireInteraction;

    protected string $tag;

    /**
     * @var array<int>
     */
    protected array $vibrate;

    protected mixed $data;

    /**
     * @var array<string, mixed>
     */
    protected array $options = [];

    /**
     * Set the notification title.
     *
     * @return $this
     */
    public function title(string $value): static
    {
        $this->title = $value;

        return $this;
    }

    /**
     * Add a notification action.
     *
     * @return $this
     */
    public function action(string $title, string $action, ?string $icon = null): static
    {
        $this->actions[] = array_filter(['title' => $title, 'action' => $action, 'icon' => $icon]);

        return $this;
    }

    /**
     * Set the notification badge.
     *
     * @return $this
     */
    public function badge(string $value): static
    {
        $this->badge = $value;

        return $this;
    }

    /**
     * Set the notification body.
     *
     * @return $this
     */
    public function body(string $value): static
    {
        $this->body = $value;

        return $this;
    }

    /**
     * Set the notification direction.
     *
     * @return $this
     */
    public function dir(string $value): static
    {
        $this->dir = $value;

        return $this;
    }

    /**
     * Set the notification icon url.
     *
     * @return $this
     */
    public function icon(string $value): static
    {
        $this->icon = $value;

        return $this;
    }

    /**
     * Set the notification image url.
     *
     * @return $this
     */
    public function image(string $value): static
    {
        $this->image = $value;

        return $this;
    }

    /**
     * Set the notification language.
     *
     * @return $this
     */
    public function lang(string $value): static
    {
        $this->lang = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function renotify(bool $value = true): static
    {
        $this->renotify = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function requireInteraction(bool $value = true): static
    {
        $this->requireInteraction = $value;

        return $this;
    }

    /**
     * Set the notification tag.
     *
     * @return $this
     */
    public function tag(string $value): static
    {
        $this->tag = $value;

        return $this;
    }

    /**
     * Set the notification vibration pattern.
     *
     * @param  array<int>  $value
     * @return $this
     */
    public function vibrate(array $value): static
    {
        $this->vibrate = $value;

        return $this;
    }

    /**
     * Set the notification arbitrary data.
     *
     * @return $this
     */
    public function data(mixed $value): static
    {
        $this->data = $value;

        return $this;
    }

    /**
     * Set the notification options.
     *
     * @link https://github.com/web-push-libs/web-push-php#notifications-and-default-options
     *
     * @param  array<string, mixed>  $value
     * @return $this
     */
    public function options(array $value): static
    {
        $this->options = $value;

        return $this;
    }

    /**
     * Get the notification options.
     *
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get an array representation of the message.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return Arr::except(array_filter(get_object_vars($this)), ['options']);
    }
}
