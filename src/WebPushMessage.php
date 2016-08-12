<?php

namespace NotificationChannels\WebPush;

use Illuminate\Support\Arr;

class WebPushMessage
{
    /**
     * The notification id.
     *
     * @var string
     */
    protected $id = null;

    /**
     * The notification title.
     *
     * @var string
     */
    protected $title;

    /**
     * The notification body.
     *
     * @var string
     */
    protected $body;

    /**
     * The notification icon.
     *
     * @var string
     */
    protected $icon = null;

    /**
     * The notification actions.
     *
     * @var array
     */
    protected $actions = [];

    /**
     * @param string $body
     * @return static
     */
    public static function create($body = '')
    {
        return static ($body);
    }

    /**
     * @param string $body
     */
    public function __construct($body = '')
    {
        $this->title = '';
        $this->body = $body;
    }

    /**
     * Set the notification id.
     *
     * @param  string $value
     * @return $this
     */
    public function id($value)
    {
        $this->id = $value;

        return $this;
    }

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
     * Set the notification icon.
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
     * Set an action.
     *
     * @param  string $title
     * @param  string $action
     * @return $this
     */
    public function action($title, $action)
    {
        $this->actions[] = compact('title', 'action');

        return $this;
    }

    /**
     * Get an array representation of the message.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'actions' => $this->actions,
            'icon' => $this->icon,
        ];
    }
}
