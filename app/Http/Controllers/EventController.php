<?php

namespace App\Http\Controllers;

use App\Actions\StoreEventAction;
use App\Actions\StoreEventTranslationAction;
use App\Actions\UpdateEventAction;
use App\Actions\UpdateEventTranslationAction;
use App\Event;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Resources\EventCollection
     */
    public function index()
    {
        $events = Event::all();

        return new EventCollection($events);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\EventStoreRequest  $request
     * @return \App\Http\Resources\EventResource
     */
    public function store(EventStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            $event = (new StoreEventAction)->execute($request->user(), $data['attributes']['event']);

            (new StoreEventTranslationAction)->execute($event, $data['attributes']['event_translate_data']);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }

        return new EventResource($event);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Event  $event
     * @return \App\Http\Resources\EventResource
     */
    public function show(Event $event)
    {
        return new EventResource($event);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\EventUpdateRequest  $request
     * @param  \App\Event  $event
     * @return \App\Http\Resources\EventResource
     */
    public function update(EventUpdateRequest $request, Event $event)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            if ($request->filled('attributes.event')) {
                (new UpdateEventAction)->execute($event, $data['attributes']['event']);
            }

            if ($request->filled('attributes.event_translate_data')) {
                (new UpdateEventTranslationAction)->execute($event, $data['attributes']['event_translate_data']);
            }

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }

        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        abort(405);
    }
}
