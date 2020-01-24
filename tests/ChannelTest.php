<?php

namespace NotificationChannels\WebPush\Test;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Mockery;
use NotificationChannels\WebPush\ReportHandler;
use NotificationChannels\WebPush\WebPushChannel;

class ChannelTest extends TestCase
{
    /** @var \Mockery\MockInterface */
    protected $webPush;

    /** @var \NotificationChannels\WebPush\WebPushChannel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();

        $this->webPush = Mockery::mock(WebPush::class);
        $this->channel = new WebPushChannel($this->webPush, new ReportHandler);
    }

    /** @test */
    public function notification_can_be_sent()
    {
        $message = ($notification = new TestNotification)->toWebPush(null, null);

        $this->webPush->shouldReceive('sendNotification')
            ->once()
            ->withArgs(function (Subscription $subscription, string $payload, bool $flush, array $options = []) use ($message) {
                $this->assertInstanceOf(Subscription::class, $subscription);
                $this->assertEquals('endpoint', $subscription->getEndpoint());
                $this->assertEquals('key', $subscription->getPublicKey());
                $this->assertEquals('token', $subscription->getAuthToken());
                $this->assertEquals('aesgcm', $subscription->getContentEncoding());
                $this->assertSame($message->getOptions(), $options);
                $this->assertSame(json_encode($message->toArray()), $payload);

                return true;
            })
            ->andReturn(true);

        $this->webPush->shouldReceive('flush')
            ->once()
            ->andReturn((function () {
                yield new MessageSentReport(new Request('POST', 'endpoint'), null, true);
            })());

        $this->testUser->updatePushSubscription('endpoint', 'key', 'token', 'aesgcm');

        $this->channel->send($this->testUser, $notification);
    }

    /** @test */
    public function subscriptions_with_invalid_endpoint_are_deleted()
    {
        $this->webPush->shouldReceive('sendNotification')
            ->times(3);

        $this->webPush->shouldReceive('flush')
            ->once()
            ->andReturn((function () {
                yield new MessageSentReport(new Request('POST', 'valid_endpoint'), new Response(200), true);
                yield new MessageSentReport(new Request('POST', 'invalid_endpoint2'), new Response(404), false);
                yield new MessageSentReport(new Request('POST', 'invalid_endpoint1'), new Response(410), false);
            })());

        $this->testUser->updatePushSubscription('valid_endpoint');
        $this->testUser->updatePushSubscription('invalid_endpoint1');
        $this->testUser->updatePushSubscription('invalid_endpoint2');

        $this->channel->send($this->testUser, new TestNotification);

        $this->assertTrue($this->testUser->pushSubscriptions()->where('endpoint', 'valid_endpoint')->exists());
        $this->assertFalse($this->testUser->pushSubscriptions()->where('endpoint', 'invalid_endpoint1')->exists());
        $this->assertFalse($this->testUser->pushSubscriptions()->where('endpoint', 'invalid_endpoint2')->exists());
    }
}
