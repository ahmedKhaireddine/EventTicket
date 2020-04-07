<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;
use App\Http\Resources\TicketResource;
use App\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\TicketStoreRequest  $request
     * @return \App\Http\Resources\TicketResource
     */
    public function store(TicketStoreRequest $request)
    {
        $attributes = $request->validated();

        $ticket = Ticket::create([
            'description' => $attributes['ticket']['description'],
            'event_id' => $attributes['event_id'],
            'price' => $attributes['ticket']['price'],
            'tickets_number' => $attributes['ticket']['number'],
            'tickets_remain' => $attributes['ticket']['number'],
            'type' => $attributes['ticket']['type'],
        ]);

        return new TicketResource($ticket);
    }

    /**
     * Display the specified resource.
     *
     * @param  App\Ticket $ticket
     * @return App\Http\Resources\TicketResource
     */
    public function show(Ticket $ticket)
    {
        return new TicketResource($ticket);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\TicketUpdateRequest  $request
     * @param  App\Ticket  $ticket
     * @return App\Http\Resources\TicketResource
     */
    public function update(TicketUpdateRequest $request, Ticket $ticket)
    {
        $attributes = $request->validated();

        if ($attributes['ticket_id'] != $ticket->id) {
            abort(500, 'The ticket identifier passed in the request parameter does not match with ticket to retrieve.');
        }

        if (isset($attributes['ticket']['number'])) {
            $attributes['ticket']['tickets_number'] = $attributes['ticket']['number'];
            $attributes['ticket']['tickets_remain'] = $attributes['ticket']['number'];
            unset($attributes['ticket']['number']);
        }

        $ticket->fill($attributes['ticket']);

        $ticket->save();

        return new TicketResource($ticket);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ticket $ticket)
    {
        abort(405);
    }
}
