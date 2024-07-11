<?php

namespace NotificationChannels\WebPush\Test;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Event;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Mockery;
use NotificationChannels\WebPush\Events\NotificationFailed;
use NotificationChannels\WebPush\Events\NotificationSent;
use NotificationChannels\WebPush\ReportHandler;
use NotificationChannels\WebPush\WebPushChannel;

class ChannelTest extends TestCase
{
    /** @test */
    public function notification_can_be_sent()
    {
        Event::fake();

        /** @var mixed $webpush */
        $webpush = Mockery::mock(WebPush::class);
        $channel = new WebPushChannel($webpush, $this->app->make(ReportHandler::class));
        $message = ($notification = new TestNotification)->toWebPush(null, null);

        $webpush->shouldReceive('queueNotification')
            ->once()
            ->withArgs(function (Subscription $subscription, string $payload, array $options = [], array $auth = []) use ($message) {
                $this->assertInstanceOf(Subscription::class, $subscription);
                $this->assertEquals('endpoint', $subscription->getEndpoint());
                $this->assertEquals('aesgcm', $subscription->getContentEncoding());
                $this->assertSame($message->getOptions(), $options);
                $this->assertSame(json_encode($message->toArray()), $payload);

                return true;
            })
            ->andReturn(true);

        $webpush->shouldReceive('flush')
            ->once()
            ->andReturn((function () {
                yield new MessageSentReport(new Request('POST', 'endpoint'), null, true);
            })());

        $this->testUser->updatePushSubscription('endpoint', 'keys', 'aesgcm');

        $channel->send($this->testUser, $notification);

        Event::assertDispatched(NotificationSent::class);
    }

    /** @test */
    public function subscriptions_with_invalid_endpoint_are_deleted()
    {
        Event::fake();

        /** @var mixed $webpush */
        $webpush = Mockery::mock(WebPush::class);
        $channel = new WebPushChannel($webpush, $this->app->make(ReportHandler::class));

        $webpush->shouldReceive('queueNotification')
            ->times(3);

        $webpush->shouldReceive('flush')
            ->once()
            ->andReturn((function () {
                yield new MessageSentReport(new Request('POST', 'valid_endpoint'), new Response(200), true);
                yield new MessageSentReport(new Request('POST', 'invalid_endpoint2'), new Response(404), false);
                yield new MessageSentReport(new Request('POST', 'invalid_endpoint1'), new Response(410), false);
            })());

        $this->testUser->updatePushSubscription('valid_endpoint');
        $this->testUser->updatePushSubscription('invalid_endpoint1');
        $this->testUser->updatePushSubscription('invalid_endpoint2');

        $channel->send($this->testUser, new TestNotification);

        $this->assertTrue($this->testUser->pushSubscriptions()->where('endpoint', 'valid_endpoint')->exists());
        $this->assertFalse($this->testUser->pushSubscriptions()->where('endpoint', 'invalid_endpoint1')->exists());
        $this->assertFalse($this->testUser->pushSubscriptions()->where('endpoint', 'invalid_endpoint2')->exists());

        Event::assertDispatched(NotificationSent::class);
        Event::assertDispatched(NotificationFailed::class);
        Event::assertDispatched(NotificationFailed::class);
    }
}
