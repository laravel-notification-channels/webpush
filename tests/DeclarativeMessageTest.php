<?php

namespace NotificationChannels\WebPush\Test;

use NotificationChannels\WebPush\DeclarativeWebPushMessage;
use NotificationChannels\WebPush\Exceptions\MessageValidationFailed;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DeclarativeMessageTest extends TestCase
{
    protected DeclarativeWebPushMessage $message;

    protected function setUp(): void
    {
        parent::setUp();

        $this->message = new DeclarativeWebPushMessage;
        $this->message->title('Message title');
        $this->message->navigate('https://example.com');
    }

    #[Test]
    public function title_can_be_set(): void
    {
        $this->message->title('New message title');

        $this->assertEquals('New message title', $this->message->toArray()['notification']['title']);
    }

    #[Test]
    public function title_must_be_set(): void
    {
        $this->message = new DeclarativeWebPushMessage;

        $this->expectException(MessageValidationFailed::class);

        $this->expectExceptionMessage('title');

        $this->message->toArray();
    }

    #[Test]
    public function navigate_can_be_set(): void
    {
        $this->message->navigate('https://new-example.com');

        $this->assertEquals('https://new-example.com', $this->message->toArray()['notification']['navigate']);
    }

    #[Test]
    public function navigate_must_be_set(): void
    {
        $this->message = new DeclarativeWebPushMessage;

        $this->message->title('Message title');

        $this->expectException(MessageValidationFailed::class);

        $this->expectExceptionMessage('navigate');

        $this->message->toArray();
    }

    #[Test]
    public function action_can_be_set(): void
    {
        $this->message->action('Some Action', 'some_action', 'https://example.com/action');

        $this->assertEquals(
            [['title' => 'Some Action', 'action' => 'some_action', 'navigate' => 'https://example.com/action']], $this->message->toArray()['notification']['actions']
        );
    }

    #[Test]
    public function action_can_be_set_with_icon(): void
    {
        $this->message->action('Some Action', 'some_action', 'https://example.com/action', '/icon.png');

        $this->assertEquals(
            [['title' => 'Some Action', 'action' => 'some_action', 'navigate' => 'https://example.com/action', 'icon' => '/icon.png']], $this->message->toArray()['notification']['actions']
        );
    }

    #[Test]
    public function badge_can_be_set(): void
    {
        $this->message->badge('/badge.jpg');

        $this->assertEquals('/badge.jpg', $this->message->toArray()['notification']['badge']);
    }

    #[Test]
    public function body_can_be_set(): void
    {
        $this->message->body('Message body');

        $this->assertEquals('Message body', $this->message->toArray()['notification']['body']);
    }

    #[Test]
    public function direction_can_be_set(): void
    {
        $this->message->dir('rtl');

        $this->assertEquals('rtl', $this->message->toArray()['notification']['dir']);
    }

    #[Test]
    public function icon_can_be_set(): void
    {
        $this->message->icon('/icon.jpg');

        $this->assertEquals('/icon.jpg', $this->message->toArray()['notification']['icon']);
    }

    #[Test]
    public function image_can_be_set(): void
    {
        $this->message->image('/image.jpg');

        $this->assertEquals('/image.jpg', $this->message->toArray()['notification']['image']);
    }

    #[Test]
    public function lang_can_be_set(): void
    {
        $this->message->lang('en');

        $this->assertEquals('en', $this->message->toArray()['notification']['lang']);
    }

    #[Test]
    public function mutable_can_be_set(): void
    {
        $this->message->mutable();

        $this->assertTrue($this->message->toArray()['mutable']);
    }

    #[Test]
    public function renotify_can_be_set(): void
    {
        $this->message->renotify();

        $this->assertTrue($this->message->toArray()['notification']['renotify']);
    }

    #[Test]
    public function require_interaction_can_be_set(): void
    {
        $this->message->requireInteraction();

        $this->assertTrue($this->message->toArray()['notification']['requireInteraction']);
    }

    #[Test]
    public function silent_can_be_set(): void
    {
        $this->message->silent();

        $this->assertTrue($this->message->toArray()['notification']['silent']);
    }

    #[Test]
    public function tag_can_be_set(): void
    {
        $this->message->tag('tag1');

        $this->assertEquals('tag1', $this->message->toArray()['notification']['tag']);
    }

    #[Test]
    public function timestamp_can_be_set(): void
    {
        $this->message->timestamp(1763059844);

        $this->assertEquals(1763059844, $this->message->toArray()['notification']['timestamp']);
    }

    #[Test]
    public function vibration_pattern_can_be_set(): void
    {
        $this->message->vibrate([1, 2, 3]);

        $this->assertEquals([1, 2, 3], $this->message->toArray()['notification']['vibrate']);
    }

    #[Test]
    public function arbitrary_data_can_be_set(): void
    {
        $this->message->data(['id' => 1]);

        $this->assertEquals(['id' => 1], $this->message->toArray()['notification']['data']);
    }

    #[Test]
    public function payload_is_declarative_message(): void
    {
        $this->assertEquals(8030, $this->message->toArray()['web_push']);
    }

    #[Test]
    public function options_can_be_set(): void
    {
        $this->message->options(['ttl' => 60]);

        $this->assertEquals(['ttl' => 60], $this->message->getOptions());
        $this->assertArrayNotHasKey('options', $this->message->toArray());
    }

    #[Test]
    public function options_contain_json_content_type(): void
    {
        $this->assertEquals(['contentType' => 'application/json'], $this->message->getOptions());
    }
}
