<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * The user model implementation.
     *
     * @var App\User
     */
    protected $toUser;

    /**
     * Create a new controller instance.
     *
     * @param  App\User  $user
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->toUser = User::find($resource->to_id, ['id', 'first_name', 'last_name']);
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
            'type' => 'conversations',
            'id' => $this->id,
            'attributes' => [
                'create_at' => $this->when(isset($this->create_at), function () {
                    return $this->create_at->toDateTimeString();
                }),
                'content' => $this->content,
                'form_user' => $this->when(isset($this->user), function () {
                    return [
                        'id' => $this->user->id,
                        'full_name' => "{$this->user->first_name} {$this->user->last_name}"
                    ];
                }),
                'read_at' => $this->when(isset($this->read_at), function () {
                    return $this->read_at->toDateTimeString();
                }),
                'to_user' => $this->when(isset($this->toUser), function () {
                    return [
                        'id' => $this->toUser->id,
                        'full_name' => "{$this->toUser->first_name} {$this->toUser->last_name}"
                    ];
                }),
            ],
            'relationships' => [
                'user' => [
                    'data' => $this->user()->exists() ? [
                        'type' => 'users',
                        'id'   => $this->user->id,
                    ] : null
                ]
            ]
        ];
    }

    /**
     * Customize the response for a request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\JsonResponse  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        if ($request->isMethod('post')) {
            $response->setStatusCode(201);
        }
    }
}
