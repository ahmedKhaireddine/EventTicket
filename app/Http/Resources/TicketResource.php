<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'type' => 'tickets',
            'id' => $this->id,
            'attributes' => [
                'created_at' => $this->created_at->toDateTimeString(),
                'updated_at' => $this->updated_at->toDateTimeString(),
                'description' => $this->description,
                'price' => $this->price,
                'tickets_number' => $this->tickets_number,
                'tickets_remain' => $this->tickets_remain,
                'type' => $this->type,
            ],
            'links' => [
                'self' => route('tickets.show', [
                    'ticket' => $this
                ])
            ],
            'relationships' => [
                'event' => [
                    'data' => $this->event()->exists() ? [
                        'type' => 'events',
                        'id' => $this->event->id
                    ] : null
                ]
            ]
        ];
    }
}
