<?php

namespace NotificationChannels\WebPush\Test;

use NotificationChannels\WebPush\WebPushMessage;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    /** @var \NotificationChannels\WebPush\WebPushMessage */
    protected $message;

    public function setUp(): void
    {
        parent::setUp();

        $this->message = new WebPushMessage;
    }

    /** @test */
    public function title_can_be_set()
    {
        $this->message->title('Message title');

        $this->assertEquals('Message title', $this->message->toArray()['title']);
    }

    /** @test */
    public function action_can_be_set()
    {
        $this->message->action('Some Action', 'some_action', '/icon.png');

        $this->assertEquals(
            [['title' => 'Some Action', 'action' => 'some_action', 'icon' => '/icon.png']], $this->message->toArray()['actions']
        );
    }

    /** @test */
    public function badge_can_be_set()
    {
        $this->message->badge('/badge.jpg');

        $this->assertEquals('/badge.jpg', $this->message->toArray()['badge']);
    }

    /** @test */
    public function body_can_be_set()
    {
        $this->message->body('Message body');

        $this->assertEquals('Message body', $this->message->toArray()['body']);
    }

    /** @test */
    public function direction_can_be_set()
    {
        $this->message->dir('rtl');

        $this->assertEquals('rtl', $this->message->toArray()['dir']);
    }

    /** @test */
    public function icon_can_be_set()
    {
        $this->message->icon('/icon.jpg');

        $this->assertEquals('/icon.jpg', $this->message->toArray()['icon']);
    }

    /** @test */
    public function image_can_be_set()
    {
        $this->message->image('/image.jpg');

        $this->assertEquals('/image.jpg', $this->message->toArray()['image']);
    }

    /** @test */
    public function lang_can_be_set()
    {
        $this->message->lang('en');

        $this->assertEquals('en', $this->message->toArray()['lang']);
    }

    /** @test */
    public function renotify_can_be_set()
    {
        $this->message->renotify();

        $this->assertTrue($this->message->toArray()['renotify']);
    }

    /** @test */
    public function requireInteraction_can_be_set()
    {
        $this->message->requireInteraction();

        $this->assertTrue($this->message->toArray()['requireInteraction']);
    }

    /** @test */
    public function tag_can_be_set()
    {
        $this->message->tag('tag1');

        $this->assertEquals('tag1', $this->message->toArray()['tag']);
    }

    /** @test */
    public function vibration_pattern_can_be_set()
    {
        $this->message->vibrate([1, 2, 3]);

        $this->assertEquals([1, 2, 3], $this->message->toArray()['vibrate']);
    }

    /** @test */
    public function arbitrary_data_can_be_set()
    {
        $this->message->data(['id' => 1]);

        $this->assertEquals(['id' => 1], $this->message->toArray()['data']);
    }

    /** @test */
    public function options_can_be_set()
    {
        $this->message->options(['ttl' => 60]);

        $this->assertEquals(['ttl' => 60], $this->message->getOptions());
        $this->assertArrayNotHasKey('options', $this->message->toArray());
    }
}
