<?php

namespace NotificationChannels\WebPush;

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
     *
     * @var array
     */
   protected $data = [];
   
   protected $image = null;
   protected $badge = null;
   protected $vibrate = [ ];
   protected $timestamp = null;
   protected $tag = null;

    /**
     * @param string $body
     *
     * @return static
     */
    public static function create($body = '')
    {
        return new static($body);
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
    public function action($title, $action, $icon = null, $placeholder = null, $type = "button")
    {
        $this->actions[] = array_filter(compact('title', 'action', 'icon', 'placeholder', 'type'));

        return $this;
    }

    /**
     * Set the arbitrary data payload.
     *
     * @param  array $value
     * @return $this
     */
    public function data($value)
    {
        $this->data = $value;

        return $this;
    }

    /**
     * Set the badge.
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
     * Set the image.
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
     * Set the vibrate pattern.
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
     * Set the timestamp pattern.
     *
     * @param  int $value
     * @return $this
     */
    public function timestamp($value)
    {
        $this->timestamp = $value;

        return $this;
    }

    /**
     * Set the tag pattern.
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
     * Get an array representation of the message.
     *
     * @return array
     */
    public function toArray()
    {
        $rtn = [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'actions' => $this->actions,
            'icon' => $this->icon,
            'image' => $this->image,
            'badge' => $this->badge,
            'vibrate' => $this->vibrate,
            'timestamp' => $this->timestamp,
            'tag' => $this->tag,
            'data' => $this->data,
        ];
        // remove null values
        $rtn = array_filter($rtn);
        return $rtn;
    }
}
