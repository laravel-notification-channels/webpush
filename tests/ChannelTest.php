<?php

namespace NotificationChannels\WebPush\Test;

use Mockery;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\MessageSentReport;
use NotificationChannels\WebPush\ReportHandler;
use NotificationChannels\WebPush\WebPushChannel;

class ChannelTest extends TestCase
{
    /** @var Mockery\Mock */
    protected $webPush;

    /** @var \NotificationChannels\WebPush\WebPushChannel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();

        $this->webPush = Mockery::mock(WebPush::class);

        $this->channel = new WebPushChannel($this->webPush, new ReportHandler);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        $this->webPush->shouldReceive('sendNotification')
            ->once()
            ->withArgs(function (Subscription $subscription, string $payload, bool $flush = false, array $options = []) {
                $this->assertSame($this->getPayload(), $payload);
                $this->assertInstanceOf(Subscription::class, $subscription);
                $this->assertEquals('endpoint', $subscription->getEndpoint());
                $this->assertEquals('key', $subscription->getPublicKey());
                $this->assertEquals('token', $subscription->getAuthToken());
                $this->assertEquals('aesgcm', $subscription->getContentEncoding());

                return true;
            })
            ->andReturn(true);

        $this->webPush->shouldReceive('flush')
            ->once()
            ->andReturn((function () {
                yield new MessageSentReport(new Request('POST', 'endpoint'), null, true);
            })());

        $this->testUser->updatePushSubscription('endpoint', 'key', 'token', 'aesgcm');

        $this->channel->send($this->testUser, new TestNotification);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_send_a_notification_with_options()
    {
        $this->webPush->shouldReceive('sendNotification')
            ->once()
            ->withArgs(function ($subscription, $payload, $flush, array $options = []) {
                $this->assertSame(['ttl' => 60], $options);

                return true;
            })
            ->andReturn(true);

        $this->webPush->shouldReceive('flush')
            ->once()
            ->andReturn((function () {
                yield new MessageSentReport(new Request('POST', 'endpoint'), null, true);
            })());

        $this->testUser->updatePushSubscription('endpoint', 'key', 'token');

        $this->channel->send($this->testUser, new TestNotificationWithOptions);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_will_delete_invalid_subscriptions()
    {
        $this->webPush->shouldReceive('sendNotification')
            ->once()
            // ->with('valid_endpoint', $this->getPayload(), null, null, [])
            ->andReturn(true);

        $this->webPush->shouldReceive('sendNotification')
            ->once()
            // ->with('invalid_endpoint', $this->getPayload(), null, null, [])
            ->andReturn(true);

        $this->webPush->shouldReceive('flush')
            ->once()
            ->andReturn((function () {
                yield new MessageSentReport(new Request('POST', 'valid_endpoint'), new Response(200), true);
                yield new MessageSentReport(new Request('POST', 'invalid_endpoint'), new Response(404), false);
            })());

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
