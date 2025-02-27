<?php

namespace NotificationChannels\WebPush\Test;

use NotificationChannels\WebPush\WebPushMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    protected WebPushMessage $message;

    protected function setUp(): void
    {
        parent::setUp();

        $this->message = new WebPushMessage;
    }

    #[Test]
    public function title_can_be_set(): void
    {
        $this->message->title('Message title');

        $this->assertEquals('Message title', $this->message->toArray()['title']);
    }

    #[Test]
    public function action_can_be_set(): void
    {
        $this->message->action('Some Action', 'some_action');

        $this->assertEquals(
            [['title' => 'Some Action', 'action' => 'some_action']], $this->message->toArray()['actions']
        );
    }

    #[Test]
    public function action_can_be_set_with_icon(): void
    {
        $this->message->action('Some Action', 'some_action', '/icon.png');

        $this->assertEquals(
            [['title' => 'Some Action', 'action' => 'some_action', 'icon' => '/icon.png']], $this->message->toArray()['actions']
        );
    }

    #[Test]
    public function badge_can_be_set(): void
    {
        $this->message->badge('/badge.jpg');

        $this->assertEquals('/badge.jpg', $this->message->toArray()['badge']);
    }

    #[Test]
    public function body_can_be_set(): void
    {
        $this->message->body('Message body');

        $this->assertEquals('Message body', $this->message->toArray()['body']);
    }

    #[Test]
    public function direction_can_be_set(): void
    {
        $this->message->dir('rtl');

        $this->assertEquals('rtl', $this->message->toArray()['dir']);
    }

    #[Test]
    public function icon_can_be_set(): void
    {
        $this->message->icon('/icon.jpg');

        $this->assertEquals('/icon.jpg', $this->message->toArray()['icon']);
    }

    #[Test]
    public function image_can_be_set(): void
    {
        $this->message->image('/image.jpg');

        $this->assertEquals('/image.jpg', $this->message->toArray()['image']);
    }

    #[Test]
    public function lang_can_be_set(): void
    {
        $this->message->lang('en');

        $this->assertEquals('en', $this->message->toArray()['lang']);
    }

    #[Test]
    public function renotify_can_be_set(): void
    {
        $this->message->renotify();

        $this->assertTrue($this->message->toArray()['renotify']);
    }

    #[Test]
    public function require_interaction_can_be_set(): void
    {
        $this->message->requireInteraction();

        $this->assertTrue($this->message->toArray()['requireInteraction']);
    }

    #[Test]
    public function tag_can_be_set(): void
    {
        $this->message->tag('tag1');

        $this->assertEquals('tag1', $this->message->toArray()['tag']);
    }

    #[Test]
    public function vibration_pattern_can_be_set(): void
    {
        $this->message->vibrate([1, 2, 3]);

        $this->assertEquals([1, 2, 3], $this->message->toArray()['vibrate']);
    }

    #[Test]
    public function arbitrary_data_can_be_set(): void
    {
        $this->message->data(['id' => 1]);

        $this->assertEquals(['id' => 1], $this->message->toArray()['data']);
    }

    #[Test]
    public function options_can_be_set(): void
    {
        $this->message->options(['ttl' => 60]);

        $this->assertEquals(['ttl' => 60], $this->message->getOptions());
        $this->assertArrayNotHasKey('options', $this->message->toArray());
    }
}
