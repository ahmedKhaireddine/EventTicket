<?php

namespace App\Actions;

use App\Event;
use App\Traits\UploadTrait;
use Carbon\Carbon;

class StoreEventAction
{
    use UploadTrait;

    /**
     * @param  \App\User  $user
     * @param  array  $attributes
     * @return \App\Event
     */
    public function execute($user, array $attributes): Event
    {
        $event = Event::create([
            'end_date' => Carbon::parse($attributes['end_date']),
            'is_active' => false,
            'picture' => $this->uploadOne($attributes['picture'], '/uploads/images/', 'public', Event::count() + 1),
            'publish_at' => Carbon::parse($attributes['publish_at']),
            'start_date' => Carbon::parse($attributes['start_date']),
            'start_time' => Carbon::parse($attributes['start_time']),
        ]);

        $event->user()->associate($user)->save();

        return $event;
    }
}