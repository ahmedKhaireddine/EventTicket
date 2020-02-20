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
                'created_at' => $this->created_at->toDateTimeString(),
                'updated_at' => $this->updated_at->toDateTimeString(),
                'email_address' => $this->email,
                'first_name' => $this->first_name,
                'job' => $this->job,
                'last_name' => $this->last_name,
                'phone' => $this->phone,
                'role' => $this->role,
            ],
            'links' => [
                'self' => route('users.show', [
                    'user' => $this,
                ]),
            ],
        ];
    }
}
