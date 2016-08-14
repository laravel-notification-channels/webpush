<?php

namespace NotificationChannels\WebPush\Test;

use NotificationChannels\WebPush\WebPushMessage;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    /** @var \NotificationChannels\WebPush\WebPushMessage */
    protected $message;

    public function setUp()
    {
        parent::setUp();

        $this->message = new WebPushMessage;
    }

    /** @test */
    public function it_can_accept_a_body_when_constructing_a_message()
    {
       $message = new WebPushMessage('Message body');

       $this->assertEquals('Message body', $message->toArray()['body']);
    }

    /** @test */
    public function it_provides_a_create_method()
    {
        $message = WebPushMessage::create('Message body');

        $this->assertEquals('Message body', $message->toArray()['body']);
    }

    /** @test */
    public function it_can_set_the_title()
    {
        $this->message->title('Message title');

        $this->assertEquals('Message title', $this->message->toArray()['title']);
    }

    /** @test */
    public function it_can_set_the_body()
    {
        $this->message->body('Message body');

        $this->assertEquals('Message body', $this->message->toArray()['body']);
    }

    /** @test */
    public function it_can_set_the_icon()
    {
        $this->message->icon('Icon');

        $this->assertEquals('Icon', $this->message->toArray()['icon']);
    }

    /** @test */
    public function it_can_set_an_action()
    {
        $this->message->action('Title', 'Action');

        $this->assertEquals([['title' => 'Title', 'action' => 'Action']], $this->message->toArray()['actions']);
    }

    /** @test */
    public function it_can_set_the_id()
    {
        $this->message->id(1);

        $this->assertEquals(1, $this->message->toArray()['id']);
    }
}
