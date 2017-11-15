<?php

namespace NotificationChannels\WebPush\Test;

use Mockery;
use Minishlink\WebPush\WebPush;
use NotificationChannels\WebPush\WebPushChannel;

class ChannelTest extends TestCase
{
    /** @var Mockery\Mock */
    protected $webPush;

    /** @var \NotificationChannels\WebPush\WebPushChannel */
    protected $channel;

    public function setUp()
    {
        parent::setUp();

        $this->webPush = Mockery::mock(WebPush::class);

        $this->channel = new WebPushChannel($this->webPush);
    }

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        $this->webPush->shouldReceive('sendNotification')
            ->once()
            ->with('endpoint', $this->getPayload(), 'key', 'token')
            ->andReturn(true);

        $this->webPush->shouldReceive('flush')
            ->once()
            ->andReturn(true);

        $this->testUser->updatePushSubscription('endpoint', 'key', 'token');

        $this->channel->send($this->testUser, new TestNotification);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_will_delete_invalid_subscriptions()
    {
        $this->webPush->shouldReceive('sendNotification')
            ->once()
            ->with('valid_endpoint', $this->getPayload(), null, null)
            ->andReturn(true);

        $this->webPush->shouldReceive('sendNotification')
            ->once()
            ->with('invalid_endpoint', $this->getPayload(), null, null)
            ->andReturn(true);

        $this->webPush->shouldReceive('flush')
            ->once()
            ->andReturn([
                ['success' => true],
                ['success' => false],
            ]);

        $this->testUser->updatePushSubscription('valid_endpoint');
        $this->testUser->updatePushSubscription('invalid_endpoint');

        $this->channel->send($this->testUser, new TestNotification);

        $this->assertFalse($this->testUser->pushSubscriptions()->where('endpoint', 'invalid_endpoint')->exists());

        $this->assertTrue($this->testUser->pushSubscriptions()->where('endpoint', 'valid_endpoint')->exists());
    }

    /**
     * @return string
     */
    protected function getPayload()
    {
        return json_encode([
            'title' => 'Title',
            'actions' => [
                ['title' => 'Title', 'action' => 'Action'],
            ],
            'body' => 'Body',
            'icon' => 'Icon',
            'data' => ['id' => 1],
        ]);
    }
}
