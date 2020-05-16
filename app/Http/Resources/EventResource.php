<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'type' => 'events',
            'id' => $this->id,
            'attributes' => [
                'created_at' => $this->created_at->toDateTimeString(),
                'updated_at' => $this->updated_at->toDateTimeString(),
                $this->mergeWhen($this->address()->exists(), function () {
                    return [
                        'address' => $this->address->toArray()
                    ];
                }),
                'end_date' => $this->when(isset($this->formatted_end_date), function () {
                    return $this->formatted_end_date;
                }),
                $this->mergeWhen($this->tickets()->exists(), function () {
                    return [
                        'event_tickets' => [
                            'total_number_of_tickets' => $this->total_tickets_number,
                            'number_of_tickets_remaining' => $this->total_tickets_remaining,
                            'format_price_to_display' => $this->formatted_price,
                            'tickets' => $this->tickets->map(function ($ticket) {
                                return new TicketResource($ticket);
                            })
                        ],
                    ];
                }),
                $this->mergeWhen($this->translations()->exists(), function () {
                    return [
                        'event_translations' => $this->translations_needed->map(function ($translation) {
                            return $translation->makeHidden([
                                'created_at', 'deleted_at', 'event_id', 'id', 'updated_at'
                            ])->toArray();
                        }),
                    ];
                }),
                'is_active' => $this->is_active,
                'picture' => $this->picture,
                'publish_at' => $this->when(isset($this->formatted_publish_at), function () {
                    return $this->formatted_publish_at;
                }),
                'start_date' => $this->formatted_start_date,
                'start_time' => $this->when(isset($this->formatted_start_time), function () {
                    return $this->formatted_start_time;
                }),
            ],
            'links' => [
                'self' => route('events.show', [
                    'event' => $this,
                ])
            ],
            'relationships' => [
                'address' => [
                    'data' => $this->address()->exists() ? [
                        'type' => 'addresses',
                        'id' => $this->address->id
                    ] : null
                ],
                'tickets' => [
                    'data' => $this->tickets()->exists() ?
                    $this->tickets->map(function ($ticket) {
                        return [
                            'type' => 'tickets',
                            'id' => $ticket->id
                        ];
                    }) : null
                ],
                'translations' => [
                    'data' => $this->translations()->exists() ?
                    $this->translations->map(function ($translation) {
                        return [
                            'type' => 'translations',
                            'id' => $translation->id
                        ];
                    }) : null
                ],
                'user' => [
                    'data' => $this->user()->exists() ? [
                        'type' => 'users',
                        'id'   => $this->user->id,
                    ] : null
                ]
            ]
        ];
    }
}
