<?php

namespace App\Http\Controllers;

use App\Actions\StoreTicketAction;
use App\Actions\UpdateTicketAction;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;
use App\Http\Resources\TicketResource;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        try {
            DB::beginTransaction();

            $data = $request->validated();

            $ticket = (new StoreTicketAction)->execute($data['attributes']);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }

        return new TicketResource($ticket);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Ticket $ticket
     * @return \App\Http\Resources\TicketResource
     */
    public function show(Ticket $ticket)
    {
        return new TicketResource($ticket);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\TicketUpdateRequest  $request
     * @param  \App\Ticket  $ticket
     * @return \App\Http\Resources\TicketResource
     */
    public function update(TicketUpdateRequest $request, Ticket $ticket)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            (new UpdateTicketAction)->execute($ticket, $data['attributes']);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }

        return new TicketResource($ticket);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ticket $ticket)
    {
        abort(405);
    }
}
