<?php

namespace NotificationChannels\WebPush;

use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;
use Illuminate\Notifications\Notification;

class WebPushChannel
{
    /** @var \Minishlink\WebPush\WebPush */
    protected $webPush;

    /**
     * @param  \Minishlink\WebPush\WebPush $webPush
     * @return void
     */
    public function __construct(WebPush $webPush)
    {
        $this->webPush = $webPush;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $subscriptions = $notifiable->routeNotificationFor('WebPush');

        if (! $subscriptions || $subscriptions->isEmpty()) {
            return;
        }

        $payload = json_encode($notification->toWebPush($notifiable, $notification)->toArray());

        $subscriptions->each(function ($sub) use ($payload) {
            $this->webPush->sendNotification(
                $sub->endpoint,
                $payload,
                $sub->public_key,
                $sub->auth_token
            );
        });

        $response = $this->webPush->flush();

        $this->logErrorsInDebug($response, $subscriptions, $payload);

        $this->deleteInvalidSubscriptions($response, $subscriptions);
    }

    /**
     * @param  array|bool $response
     * @param  \Illuminate\Database\Eloquent\Collection $subscriptions
     * @return void
     */
    protected function deleteInvalidSubscriptions($response, $subscriptions)
    {
        if (! is_array($response)) {
            return;
        }

        foreach ($response as $index => $value) {
            if (! $value['success'] && isset($subscriptions[$index])) {
                $subscriptions[$index]->delete();
            }
        }
    }

    protected function logErrorsInDebug($response, $subscriptions, $payload)
    {
		if(config('webpush.enable_logging')){
			if(is_array($response)){
				foreach($response as $index => $push){
					if(!$push['success']){
						Log::error("[WebPush] Error pushing: {$push['message']}\nEndpoint: {$push['endpoint']}\nPayload: {$payload}");
					}else{
						Log::info("[WebPush] Push successful\nEndpoint: {$subscriptions[$index]->endpoint}");
					}
				}
			}elseif(is_bool($response) && $response === true){
				Log::info('[WebPush] All messages successfully pushed');
			}elseif(is_bool($response) && $response === false){
				Log::info('[WebPush] No notifications in the queue');
			}

		}
    }
}
