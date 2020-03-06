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
            'date' => Carbon::parse($request->date),
            'is_active' => false,
            'picture' => $this->uploadOne($request->picture, '/uploads/images/', 'public', $request->title),
            'publish_at' => Carbon::parse($request->publish_at),
            'subtitle' => $request->subtitle,
            'title' => $request->title,
        ]);

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

        if (isset($eventAttributes['picture'])) {
            $name = $eventAttributes['title'] ?? $event->title;
            $eventAttributes['picture'] = $this->uploadOne($eventAttributes['picture'], '/uploads/images/', 'public', $name);
        }

        if (isset($eventAttributes['date'])) {
            $eventAttributes['date'] = Carbon::parse($eventAttributes['date']);
        }

        if (isset($eventAttributes['publish_at'])) {
            $eventAttributes['publish_at'] = Carbon::parse($eventAttributes['publish_at']);
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
