<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * The access token of user model.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * Create a new controller instance.
     *
     * @param  App\User  $resource
     * @param  string  $accessToken
     * @return void
     */
    public function __construct($resource, $accessToken = null)
    {
        parent::__construct($resource);

        $this->accessToken = $accessToken;
    }

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
                'access_token' => $this->when(isset($this->accessToken), function () {
                    return $this->accessToken;
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
                'messages_not_read' => $this->when(isset($this->messages_count), function () {
                    return $this->messages_count;
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
