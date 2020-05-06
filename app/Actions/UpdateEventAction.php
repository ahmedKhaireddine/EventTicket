<?php

namespace App\Actions;

use App\Event;
use App\Traits\UploadTrait;
use Carbon\Carbon;

class UpdateEventAction
{
    use UploadTrait;

    /**
     * @param  \App\User  $user
     * @param  array  $attributes
     * @return \App\Event
     */
    public function execute(Event $event, array $attributes): Event
    {
        if (isset($attributes['end_date'])) {
            $attributes['end_date'] = Carbon::parse($attributes['end_date']);
        }

        if (isset($attributes['picture'])) {
            $attributes['picture'] = $this->uploadOne($attributes['picture'], '/uploads/images/', 'public', $event->id);
        }

        if (isset($attributes['start_date'])) {
            $attributes['start_date'] = Carbon::parse($attributes['start_date']);
        }

        if (isset($attributes['start_time'])) {
            $attributes['start_time'] = Carbon::parse($attributes['start_time']);
        }

        $event->fill($attributes);

        $event->save();

        return $event;
    }
}