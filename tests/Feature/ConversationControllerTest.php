<?php

namespace Tests\Feature;

use App\Message;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ConversationControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'first_name' => 'Lea',
            'last_name' => 'Dubois',
            'role' => 'user'
        ]);

        $this->userAdmin = factory(User::class)->create([
            'first_name' => 'Martin',
            'last_name' => 'Legrand',
            'role' => 'admin'
        ]);

        $this->userAnother = factory(User::class)->create([
            'role' => 'user'
        ]);
    }

    // Index

    public function test_can_get_conversation_when_user_is_not_connected_return_Http_code_401()
    {
        // Action
        $response = $this->json('GET', route('conversations.index'));

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_get_conversation_when_user_is_connected_and_do_not_pass_user_id_parameter_return_Http_code_422()
    {
        // Action
        $response = $this->actingAs($this->user, 'api')->json('GET', route('conversations.index'));

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_get_conversation_when_user_is_connected_and_speak_with_himself_return_Http_code_403()
    {
        // Arrange
        $param = "?user_id={$this->user->id}";

        // Action
        $response = $this->actingAs($this->user, 'api')->json('GET', route('conversations.index').$param);

        // Assert
        $response->assertStatus(403);
    }

    public function test_can_get_conversation_when_user_is_connected_return_Http_code_200()
    {
        // Arrange
        $param = "?user_id={$this->user->id}";

        $messageUser = factory(Message::class)->create([
            'content' => 'Salut !',
            'from_id' => $this->user->id,
            'to_id' => $this->userAdmin->id
        ]);

        $messageUserAdmin = factory(Message::class)->create([
            'content' => 'Salut ! comment vas-tu ?',
            'from_id' => $this->userAdmin->id,
            'to_id' => $this->user->id
        ]);

        // Action
        $response = $this->actingAs($this->userAdmin, 'api')->json('GET', route('conversations.index').$param);

        // Assert
        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson([
                'data' => [
                    [
                        'type' => 'conversations',
                        'id' => 2,
                        'attributes' => [
                            'content' => 'Salut ! comment vas-tu ?',
                            'form_user' => [
                                'id' => $this->userAdmin->id,
                                'full_name' => 'Martin Legrand'
                            ],
                            'to_user' => [
                                'id' => $this->user->id,
                                'full_name' => 'Lea Dubois'
                            ]
                        ],
                        'relationships' => [
                            'user' => [
                                'data' => [
                                    'type' => 'users',
                                    'id'   => $this->userAdmin->id,
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'conversations',
                        'id' => 1,
                        'attributes' => [
                            'content' => 'Salut !',
                            'form_user' => [
                                'id' => $this->user->id,
                                'full_name' => 'Lea Dubois'
                            ],
                            'read_at' => '2020-03-30 00:00:00',
                            'to_user' => [
                                'id' => $this->userAdmin->id,
                                'full_name' => 'Martin Legrand'
                            ]
                        ],
                        'relationships' => [
                            'user' => [
                                'data' => [
                                    'type' => 'users',
                                    'id'   => $this->user->id,
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    // Store

    public function test_can_store_message_when_user_is_not_connected_return_Http_code_401()
    {
        // Arrange
        $data = [
            'content' => 'Salut !',
            'to_id' => $this->userAdmin->id,
        ];

        // Action
        $response = $this->json('POST', route('conversations.store'), $data);

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_store_message_when_user_is_connected_and_speaking_with_a_user_is_not_admin_return_Http_code_422()
    {
        // Arrange
        $data = [
            'content' => 'Salut !',
            'to_id' => $this->userAnother->id,
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('conversations.store'), $data);

        // Assert
        $response->assertStatus(422)
            ->assertSeeText('You cannot speak with this user.');
    }

    public function test_can_store_message_when_user_is_connected_and_has_user_role_return_Http_code_201()
    {
        // Arrange
        $data = [
            'content' => 'Salut !',
            'to_id' => $this->userAdmin->id,
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('conversations.store'), $data);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'conversations',
                    'id' => 1,
                    'attributes' => [
                        'content' => 'Salut !',
                        'form_user' => [
                            'id' => $this->user->id,
                            'full_name' => 'Lea Dubois'
                        ],
                        'to_user' => [
                            'id' => $this->userAdmin->id,
                            'full_name' => 'Martin Legrand'
                        ]
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'type' => 'users',
                                'id'   => $this->user->id,
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_store_message_when_user_is_connected_and_has_admin_role_return_Http_code_201()
    {
        // Arrange
        $data = [
            'content' => 'Salut comment vas-tu ?',
            'to_id' => $this->user->id,
        ];

        // Action
        $response = $this->actingAs($this->userAdmin, 'api')->json('POST', route('conversations.store'), $data);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'conversations',
                    'id' => 1,
                    'attributes' => [
                        'content' => 'Salut comment vas-tu ?',
                        'form_user' => [
                            'id' => $this->userAdmin->id,
                            'full_name' => 'Martin Legrand'
                        ],
                        'to_user' => [
                            'id' => $this->user->id,
                            'full_name' => 'Lea Dubois'
                        ]
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'type' => 'users',
                                'id'   => $this->userAdmin->id,
                            ]
                        ]
                    ]
                ]
            ]);
    }
}
