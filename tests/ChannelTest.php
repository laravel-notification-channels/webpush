<?php

namespace NotificationChannels\PusherPushNotifications\Test;

use Illuminate\Notifications\Notification;
use Mockery;
use NotificationChannels\PusherPushNotifications\Channel;
use PHPUnit_Framework_TestCase;
use Pusher;

class ChannelTest extends PHPUnit_Framework_TestCase
{
    /** @var Mockery\Mock */
    protected $pusher;

    /** @var \NotificationChannels\PusherPushNotifications\Channel */
    protected $channel;

    /** @test */
    public function setUp()
    {
        $this->pusher = Mockery::mock(Pusher::class);

        $this->channel = new Channel($this->pusher);
    }

    public function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        /* TODO: revisit this test after L5.3 comes out */

        //$this->pusher->shouldReceive('notify')->once()->andReturn(['response' => 200]);

        //$this->channel->send(new Notifiable(), new Notification());
    }
}
