<?php

namespace NotificationChannels\WebPush;

use Illuminate\Support\Arr;

/**
 * @link https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerRegistration/showNotification#Parameters
 */
class WebPushMessage
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var array
     */
    protected $actions = [];

    /**
     * @var string
     */
    protected $badge;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $dir;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var string
     */
    protected $lang;

    /**
     * @var bool
     */
    protected $renotify;

    /**
     * @var bool
     */
    protected $requireInteraction;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @var array
     */
    protected $vibrate;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Set the notification title.
     *
     * @param  string $value
     * @return $this
     */
    public function title($value)
    {
        $this->title = $value;

        return $this;
    }

    /**
     * Add a notification action.
     *
     * @param  string $title
     * @param  string $action
     * @param string $icon
     * @return $this
     */
    public function action($title, $action, $icon = null)
    {
        $this->actions[] = array_filter(compact('title', 'action', 'icon'));

        return $this;
    }

    /**
     * Set the notification badge.
     *
     * @param  string $value
     * @return $this
     */
    public function badge($value)
    {
        $this->badge = $value;

        return $this;
    }

    /**
     * Set the notification body.
     *
     * @param  string $value
     * @return $this
     */
    public function body($value)
    {
        $this->body = $value;

        return $this;
    }

    /**
     * Set the notification direction.
     *
     * @param  string $value
     * @return $this
     */
    public function dir($value)
    {
        $this->dir = $value;

        return $this;
    }

    /**
     * Set the notification icon url.
     *
     * @param  string $value
     * @return $this
     */
    public function icon($value)
    {
        $this->icon = $value;

        return $this;
    }

    /**
     * Set the notification image url.
     *
     * @param  string $value
     * @return $this
     */
    public function image($value)
    {
        $this->image = $value;

        return $this;
    }

    /**
     * Set the notification language.
     *
     * @param  string $value
     * @return $this
     */
    public function lang($value)
    {
        $this->lang = $value;

        return $this;
    }

    /**
     * @param  bool $value
     * @return $this
     */
    public function renotify($value = true)
    {
        $this->renotify = $value;

        return $this;
    }

    /**
     * @param  bool $value
     * @return $this
     */
    public function requireInteraction($value = true)
    {
        $this->requireInteraction = $value;

        return $this;
    }

    /**
     * Set the notification tag.
     *
     * @param  string $value
     * @return $this
     */
    public function tag($value)
    {
        $this->tag = $value;

        return $this;
    }

    /**
     * Set the notification vibration pattern.
     *
     * @param  array $value
     * @return $this
     */
    public function vibrate($value)
    {
        $this->vibrate = $value;

        return $this;
    }

    /**
     * Set the notification arbitrary data.
     *
     * @param  mixed $value
     * @return $this
     */
    public function data($value)
    {
        $this->data = $value;

        return $this;
    }

    /**
     * Set the notification options.
     *
     * @link https://github.com/web-push-libs/web-push-php#notifications-and-default-options
     *
     * @param  array $value
     * @return $this
     */
    public function options(array $value)
    {
        $this->options = $value;

        return $this;
    }

    /**
     * Get the notification options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get an array representation of the message.
     *
     * @return array
     */
    public function toArray()
    {
        return Arr::except(array_filter(get_object_vars($this)), ['options']);
    }
}
