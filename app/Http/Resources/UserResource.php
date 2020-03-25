<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'type' => 'users',
            'id' => $this->id,
            'attributes' => [
                'created_at' => $this->when(isset($this->created_at), function () {
                    return $this->created_at->toDateTimeString();
                }),
                'updated_at' => $this->when(isset($this->updated_at), function () {
                    return $this->updated_at->toDateTimeString();
                }),
                'email_address' => $this->when(isset($this->email), function () {
                    return $this->email;
                }),
                'first_name' => $this->when(isset($this->first_name), function () {
                    return $this->first_name;
                }),
                'job' => $this->when(isset($this->job), function () {
                    return $this->job;
                }),
                'last_name' => $this->when(isset($this->last_name), function () {
                    return $this->last_name;
                }),
                'phone' => $this->when(isset($this->phone), function () {
                    return $this->phone;
                }),
                'role' => $this->when(isset($this->role), function () {
                    return $this->role;
                }),
            ],
            'links' => [
                'self' => route('users.show', [
                    'user' => $this,
                ]),
            ],
            'relationships' => [
                'events' => [
                    'data' => $this->events()->exists() ?
                    $this->events->map(function ($event, $key) {
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
