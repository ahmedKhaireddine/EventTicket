<?php

namespace Tests\Feature;

use App\Message;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Tests\TestCase;

class UserControllerTest extends TestCase
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

        $this->anotherUser = factory(User::class)->create([
            'first_name' => 'Alice',
            'last_name' => 'Petit',
            'role' => 'user'
        ]);
    }

    // Index

    public function test_can_get_all_users_when_user_is_not_connected_return_Http_code_401()
    {
        // Action
        $response = $this->json('GET', route('users.index'));

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_get_all_users_when_user_is_not_admin_return_Http_code_200()
    {
        // Action
        $response = $this->actingAs($this->user, 'api')->json('GET', route('users.index'));

        // Assert
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    [
                        'id' => 2,
                        'attributes' => [
                            'first_name' => 'Martin',
                            'last_name' => 'Legrand',
                            'role' => 'admin',
                        ],
                        'links' => [
                            'self' => 'http://localhost/api/users/2',
                        ],
                        'relationships' => [
                            'events' => [
                                'data' => null
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_get_all_users_when_user_is_admin_and_has_messges_return_Http_code_200()
    {
        // Arrange
        $messages = factory(Message::class, 2)->create([
            'from_id' => $this->anotherUser->id,
            'to_id' => $this->userAdmin->id
        ]);

        // Action
        $response = $this->actingAs($this->userAdmin, 'api')->json('GET', route('users.index'));

        // Assert
        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson([
                'data' => [
                    [
                        'id' => 3,
                        'attributes' => [
                            'first_name' => 'Alice',
                            'last_name' => 'Petit',
                            'messages_not_read' => 2,
                            'role' => 'user',
                        ],
                        'links' => [
                            'self' => 'http://localhost/api/users/3',
                        ],
                        'relationships' => [
                            'events' => [
                                'data' => null
                            ]
                        ]
                    ],
                    [
                        'id' => 1,
                        'attributes' => [
                            'first_name' => 'Lea',
                            'last_name' => 'Dubois',
                            'role' => 'user',
                        ],
                        'links' => [
                            'self' => 'http://localhost/api/users/1',
                        ],
                        'relationships' => [
                            'events' => [
                                'data' => null
                            ]
                        ]
                    ]
                ]
            ]);
    }

    // Show

    public function test_can_show_user_when_user_is_not_connected_return_Http_code_401()
    {
        // Action
        $response = $this->json('GET', route('users.show', $this->user->id));

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_show_user_when_user_is_connected_return_Http_code_200()
    {
        // Arrange
        $user = factory(User::class)->create([
            'email' => 'robert_petit@yahoo.fr',
            'first_name' => 'Robert',
            'job' => 'Organisateur',
            'last_name' => 'Petit',
            'password' => '0000',
            'phone' => '0123456789',
            'role' => 'user',
        ]);

        // Action
        $response = $this->actingAs($user, 'api')->json('GET', route('users.show', $user->id));

        // Assert
        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'id' => 4,
                    'attributes' => [
                        'email_address' => 'robert_petit@yahoo.fr',
                        'first_name' => 'Robert',
                        'job' => 'Organisateur',
                        'last_name' => 'Petit',
                        'phone' => '0123456789',
                        'role' => 'user',
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/users/4',
                    ],
                    'relationships' => [
                        'events' => [
                            'data' => null
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_show_user_when_we_dont_provide_id_for_the_route_return_exeption_Missing_required_parameters()
    {
        // Exception
        $this->expectException(UrlGenerationException::class);

        // Action
        $response = $this->actingAs($this->user, 'api')->json('GET', route('users.show'));
    }

    public function test_can_show_user_when_another_user_is_connected_return_Http_code_403()
    {
        // Action
        $response = $this->actingAs($this->anotherUser, 'api')->json('GET', route('users.show', $this->user->id));

        // Assert
        $response->assertForbidden();
    }

    public function test_can_show_user_when_another_user_is_connected_with_a_role_admin_return_Http_code_200()
    {
        // Action
        $response = $this->actingAs($this->userAdmin, 'api')->json('GET', route('users.show', $this->user->id));

        // Assert
        $response->assertOk();
    }

    // Store

    public function test_can_store_user_when_user_is_not_connected_return_Http_code_201()
    {
        // Arrange
        $data = [
            'email' => 'robert_legrand@yahoo.fr',
            'first_name' => 'Robert',
            'job' => 'Organisateur',
            'last_name' => 'Legrand',
            'password' => '0000',
            'phone' => '0123456789',
            'password_confirmation' => '0000',
        ];

        // Action
        $response = $this->json('POST', route('users.store'), $data);

        // Assert
        $response
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'id' => 4,
                    'attributes' => [
                        'email_address' => 'robert_legrand@yahoo.fr',
                        'first_name' => 'Robert',
                        'job' => 'Organisateur',
                        'last_name' => 'Legrand',
                        'phone' => '0123456789',
                        'role' => 'user',
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/users/4',
                    ],
                    'relationships' => [
                        'events' => [
                            'data' => null
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_store_user_when_we_dont_provide_id_for_the_request_return_Http_code_422()
    {
        // Action
        $response = $this->json('POST', route('users.store'));

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_store_user_when_we_dont_provide_all_the_fields_return_Http_code_422()
    {
        // Arrange
        $data = [
            'password' => '0000',
            'phone' => '0123456789',
            'password_confirmation' => '0000',
        ];

        // Action
        $response = $this->json('POST', route('users.store'), $data);

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_store_user_when_we_provide_a_field_email_exists_return_Http_code_422()
    {
        // Arrange
        // Arrange
        $user = factory(User::class)->create([
            'email' => 'robert_legrand@yahoo.fr'
        ]);

        $data = [
            'email' => 'robert_legrand@yahoo.fr',
            'first_name' => 'Robert',
            'job' => 'Organisateur',
            'last_name' => 'Legrand',
            'password' => '0000',
            'phone' => '0123456789',
            'password_confirmation' => '0000',
        ];

        // Action
        $response = $this->json('POST', route('users.store'), $data);

        // Assert
        $response->assertStatus(422);
    }

    // // Update


    public function test_can_update_user_when_user_is_not_connected_return_Http_code_401()
    {
        // Arrange
        $data = factory(User::class)->make()->toArray();

        // Action
        $response = $this->json('PUT', route('users.update', $this->user), $data);
        // $response = $this->actingAs($this->user, 'api')->json('PUT', route('users.update'), $data);

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_update_user_when_user_is_connected_return_Http_code_200()
    {
        // Arrange
        $data = $data = [
            'email' => 'robert_legrand@gmail.fr',
            'first_name' => 'Robert',
            'job' => 'Organisateur/Concert',
            'last_name' => 'Legrands',
            'phone' => '0666666666',
        ];;

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('users.update', $this->user->id), $data);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'id' => 1,
                    'attributes' => [
                        'email_address' => 'robert_legrand@gmail.fr',
                        'first_name' => 'Robert',
                        'job' => 'Organisateur/Concert',
                        'last_name' => 'Legrands',
                        'phone' => '0666666666',
                        'role' => 'user',
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/users/1',
                    ],
                    'relationships' => [
                        'events' => [
                            'data' => null
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_update_user_when_we_dont_provide_id_for_the_route_return_exeption_Missing_required_parameters()
    {
        // Exception
        $this->expectException(UrlGenerationException::class);

        // Arrange
        $data = factory(User::class)->make()->toArray();

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('users.update'), $data);
    }

    public function test_can_update_user_when_another_user_is_connected_return_Http_code_403()
    {
        // Arrange
        $data = factory(User::class)->make()->toArray();

        // Action
        $response = $this->actingAs($this->anotherUser, 'api')->json('PUT', route('users.update', $this->user->id), $data);

        // Assert
        $response->assertForbidden();
    }

    public function test_can_update_user_when_another_user_is_connected_with_a_role_admin_return_Http_code_200()
    {
        // Arrange
        $data = factory(User::class)->make()->toArray();

        // Action
        $response = $this->actingAs($this->userAdmin, 'api')->json('PUT', route('users.update', $this->user->id), $data);

        // Assert
        $response->assertOk();
    }

    public function test_can_update_user_when_email_field_exists_return_Http_code_422()
    {
        // Arrange
        $user = factory(User::class)->create([
            'email' => 'robert_legrand@yahoo.fr'
        ]);

        $data = [
            'email' => 'robert_legrand@yahoo.fr',
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('users.update', $this->user->id), $data);

        // Assert
        $response->assertStatus(422);
    }

    // Destroy

    public function test_can_destroy_user_when_user_is_not_connected_return_Http_code_401()
    {
        // Action
        $response = $this->json('DElETE', route('users.destroy', $this->user->id));

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_destroy_user_when_user_is_connected_return_Http_code_403()
    {
        // Action
        $response = $this->actingAs($this->user, 'api')->json('DElETE', route('users.destroy', $this->user->id));

        // Assert
        $response->assertForbidden();
    }

    public function test_can_destroy_user_when_another_user_is_connected_return_Http_code_403()
    {
        // Action
        $response = $this->actingAs($this->anotherUser, 'api')->json('DElETE', route('users.destroy', $this->user->id));

        // Assert
        $response->assertForbidden();
    }

    public function test_can_destroy_user_when_we_dont_provide_id_for_the_route_return_exeption_Missing_required_parameters()
    {
        // Exception
        $this->expectException(UrlGenerationException::class);

        // Action
        $response = $this->actingAs($this->user, 'api')->json('DElETE', route('users.destroy'));
    }

    public function test_can_destroy_user_when_another_user_is_connected_with_a_role_admin_return_Http_code_204()
    {
        // Arrange
        $user = factory(User::class)->create();

        // Action
        $response = $this->actingAs($this->userAdmin, 'api')->json('DElETE', route('users.destroy', $user->id));

        // Assert
        $response->assertStatus(204);
    }

    // Next step

    // public function test_can_destroy_user_when_user_in_relationship_with_events_return_Http_code_204()
    // {
    //     // Arrange
    //     $this->user->events()->createMany(
    //         factory(Event::class, 3)->make()->toArray()
    //     );

    //     // Action
    //     $response = $this->actingAs($this->userAdmin, 'api')->json('DElETE', route('users.destroy', $this->user->id));

    //     // Assert
    //     $response->assertStatus(204);
    // }
}
