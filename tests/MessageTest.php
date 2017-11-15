<?php

namespace NotificationChannels\WebPush\Test;

use PHPUnit\Framework\TestCase;
use NotificationChannels\WebPush\WebPushMessage;

class MessageTest extends TestCase
{
    /** @var \NotificationChannels\WebPush\WebPushMessage */
    protected $message;

    public function setUp()
    {
        parent::setUp();

        $this->message = new WebPushMessage;
    }

    /** @test */
    public function can_set_title()
    {
        $this->message->title('Message title');

        $this->assertEquals('Message title', $this->message->toArray()['title']);
    }

    /** @test */
    public function can_set_an_action()
    {
        $this->message->action('Some Action', 'some_action');

        $this->assertEquals([['title' => 'Some Action', 'action' => 'some_action']], $this->message->toArray()['actions']);
    }

    /** @test */
    public function can_set_badge()
    {
        $this->message->badge('/badge.jpg');

        $this->assertEquals('/badge.jpg', $this->message->toArray()['badge']);
    }

    /** @test */
    public function can_set_body()
    {
        $this->message->body('Message body');

        $this->assertEquals('Message body', $this->message->toArray()['body']);
    }

    /** @test */
    public function can_set_direction()
    {
        $this->message->dir('rtl');

        $this->assertEquals('rtl', $this->message->toArray()['dir']);
    }

    /** @test */
    public function can_set_icon()
    {
        $this->message->icon('/icon.jpg');

        $this->assertEquals('/icon.jpg', $this->message->toArray()['icon']);
    }

    /** @test */
    public function can_set_image()
    {
        $this->message->image('/image.jpg');

        $this->assertEquals('/image.jpg', $this->message->toArray()['image']);
    }

    /** @test */
    public function can_set_lang()
    {
        $this->message->lang('en');

        $this->assertEquals('en', $this->message->toArray()['lang']);
    }

    /** @test */
    public function can_set_renotify()
    {
        $this->message->renotify();

        $this->assertTrue($this->message->toArray()['renotify']);
    }

    /** @test */
    public function can_set_requireInteraction()
    {
        $this->message->requireInteraction();

        $this->assertTrue($this->message->toArray()['requireInteraction']);
    }

    /** @test */
    public function can_set_tag()
    {
        $this->message->tag('tag1');

        $this->assertEquals('tag1', $this->message->toArray()['tag']);
    }

    /** @test */
    public function can_set_vibration_pattern()
    {
        $this->message->vibrate([1, 2, 3]);

        $this->assertEquals([1, 2, 3], $this->message->toArray()['vibrate']);
    }

    /** @test */
    public function can_set_arbitrary_data()
    {
        $this->message->data(['id' => 1]);

        $this->assertEquals(['id' => 1], $this->message->toArray()['data']);
    }
}
