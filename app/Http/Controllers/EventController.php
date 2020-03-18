<?php

namespace App\Http\Controllers;

use App\Event;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Traits\UploadTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use UploadTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event::all();

        return new EventCollection($events);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventStoreRequest $request)
    {
        $event = Event::create([
            'additionel_information' => $request->additionel_information,
            'end_date' => Carbon::parse($request->end_date),
            'event_program' => $request->event_program,
            'is_active' => false,
            'picture' => $this->uploadOne($request->picture, '/uploads/images/', 'public', $request->title),
            'publish_at' => Carbon::parse($request->publish_at),
            'start_date' => Carbon::parse($request->start_date),
            'start_time' => Carbon::parse($request->start_time),
            'subtitle' => $request->subtitle,
            'title' => $request->title,
        ]);

        $event->user()->associate($request->user())->save();

        return new EventResource($event);
    }

    /**
     * Display the specified resource.
     *
     * @param  App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        return new EventResource($event);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\EventUpdateRequest  $request
     * @param  App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(EventUpdateRequest $request, Event $event)
    {
        $eventAttributes = $request->validated();


        if (isset($eventAttributes['end_date'])) {
            $eventAttributes['end_date'] = Carbon::parse($eventAttributes['end_date']);
        }

        if (isset($eventAttributes['picture'])) {
            $name = $eventAttributes['title'] ?? $event->title;
            $eventAttributes['picture'] = $this->uploadOne($eventAttributes['picture'], '/uploads/images/', 'public', $name);
        }

        if (isset($eventAttributes['publish_at'])) {
            $eventAttributes['publish_at'] = Carbon::parse($eventAttributes['publish_at']);
        }

        if (isset($eventAttributes['start_date'])) {
            $eventAttributes['start_date'] = Carbon::parse($eventAttributes['start_date']);
        }

        if (isset($eventAttributes['start_time'])) {
            $eventAttributes['start_time'] = Carbon::parse($eventAttributes['start_time']);
        }

        $event->fill($eventAttributes);

        $event->save();

        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
