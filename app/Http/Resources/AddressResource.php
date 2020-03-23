<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'addresses',
            'id' => $this->id,
            'attributes' => [
                'additionel_information' => $this->when(isset($this->additionel_information), function () {
                    return $this->additionel_information;
                }),
                'city' => $this->city,
                'country' => $this->country,
                'full_address' => $this->full_address,
                'postal_code' => $this->postal_code,
                'street_address' => $this->street_address,
                'venue' => $this->when(isset($this->venue), function () {
                    return $this->venue;
                }),
            ],
            'links' => [
                'self' => route('addresses.show', [
                    'address' => $this
                ])
            ],
            'relationships' => [
                'events' => [
                    'data' => $this->events()->exists() ?
                    $this->events->map(function ($event) {
                        return [
                            'type' => 'events',
                            'id' => $event->id
                        ];
                    }) : null
                ]
            ]
        ];
    }
}
