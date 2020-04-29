<?php

namespace App\Actions;

use App\Event;
use App\Message;
use App\Notifications\PublishNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class PublishEventAction
{
    /**
     * @var \App\User
     **/
    private $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    /**
     * @param  \App\Event  $event
     * @return array
     */
    public function execute(Event $event): array
    {
        if ($event->is_active) {
            $this->markAsPublished($event);

            $this->sendNotification($this->user);

            return [
                'message' => trans('Successful publication.'),
                'code' => 200
            ];
        }

        return [
            'message' => trans('Your event is not activated by the platform, please make the validation request.'),
            'code' => 500
        ];
    }

    /**
     * @param  \App\Event  $event
     * @return void
     */
    private function markAsPublished(Event $event): void
    {
        $event->publish_at = now();

        $event->save();
    }

    /**
     * @param \App\User
     * @return void
     */
    private function sendNotification($user): void
    {
        $user->notify(new PublishNotification());
    }
}