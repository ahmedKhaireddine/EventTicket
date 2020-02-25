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
                'date' => $this->formatted_date,
                'is_active' => $this->is_active,
                'picture' => $this->picture,
                'publish_at' => $this->formatted_publish_at,
                'start_time' => $this->when(isset($this->formatted_start_time), $this->formatted_start_time),
                'subtitle' => $this->subtitle,
                'title' => $this->title,
            ],
            'links' => [
                'self' => route('events.show', [
                    'event' => $this,
                ])
            ]
        ];
    }
}
