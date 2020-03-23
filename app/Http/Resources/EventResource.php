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
                'additionel_information' => $this->additionel_information,
                $this->mergeWhen($this->address()->exists(), function () {
                    return [
                        'address' => $this->address->toArray()
                    ];
                }),
                'end_date' => $this->when(isset($this->formatted_end_date), function () {
                    return $this->formatted_end_date;
                }),
                'event_program' => $this->when(isset($this->event_program), function () {
                    return $this->event_program;
                }),
                'is_active' => $this->is_active,
                'picture' => $this->picture,
                'publish_at' => $this->formatted_publish_at,
                'start_date' => $this->formatted_start_date,
                'start_time' => $this->when(isset($this->formatted_start_time), function () {
                    return $this->formatted_start_time;
                }),
                'subtitle' => $this->subtitle,
                'title' => $this->title,
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
