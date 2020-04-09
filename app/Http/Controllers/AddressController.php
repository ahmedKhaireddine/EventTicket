<?php

namespace App\Http\Controllers;

use App\Address;
use App\Event;
use App\Http\Requests\AddressStoreRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Traits\CheckFranceAddressTrait;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use CheckFranceAddressTrait;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\AddressStoreRequest  $request
     * @return \App\Http\Resources\AddressResource
     */
    public function store(AddressStoreRequest $request)
    {
        $attributes = $request->validated();

        $event = Event::find($attributes['event_id']);

        if ($attributes['address']['country'] === 'France') {
            if ($this->checkAddress($attributes['address'])) {
                $address = Address::create($attributes['address']);
            } else {
                abort(500, 'The address does not exist in France.');
            }
        } else {
            $address = Address::create($attributes['address']);
        }

        $event->address()->associate($address)->save();

        return new AddressResource($address);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\AddressUpdateRequest  $request
     * @param  \App\Address  $address
     * @return \App\Http\Resources\AddressResource
     */
    public function update(AddressUpdateRequest $request, Address $address)
    {
        $attributes = $request->validated();

        if ($attributes['address_id'] != $address->id) {
            abort(500, 'The address identifier passed in the request parameter does not match with address to retrieve.');
        }

        if ($attributes['address']['country'] === 'France') {
            if ($this->checkAddress($attributes['address'])) {
                $address->fill($attributes['address']);
            } else {
                abort(500, 'The address does not exist in France.');
            }
        } else {
            $address->fill($attributes['address']);
        }

        $address->save();

        return new AddressResource($address);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort(405);
    }
}
