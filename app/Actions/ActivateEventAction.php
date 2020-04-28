<?php

namespace App\Actions;

use App\Event;
use App\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ActivateEventAction
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
     * @return string
     */
    public function execute(Event $event): string
    {
        $this->markAsActivated($event);

        $this->sendMessage($this->user->id, $event->id);

        return trans('Activation successfully.');
    }

    /**
     * @param  \App\Event  $event
     * @return void
     */
    private function markAsActivated(Event $event): void
    {
        $event->is_active = true;

        $event->save();
    }

    /**
     * @param  int  $from
     * @param  int  $to
     * @return \App\Message
     */
    private function sendMessage(int $from, int $to): Message
    {
        return Message::create([
            'content' => trans('Your event is validated, you can publish it.'),
            'create_at' => Carbon::now(),
            'from_id' => $from,
            'to_id' => $to
        ]);
    }
}