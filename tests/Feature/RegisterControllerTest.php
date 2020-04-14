<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'role' => 'user'
        ]);

        $this->userSuperAdmin = factory(User::class)->create([
            'role' => 'admin',
            'is_super_admin' => true
        ]);
    }

    public function test_can_register_user_admin_when_user_is_not_connected_return_Http_code_401()
    {
        // Arrange
        $data = factory(User::class)->make()->toArray();

        // Action
        $response = $this->json('POST', route('admin.create'), $data);

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_register_user_admin_when_user_is_connected_but_his_not_super_admin_return_Http_code_403()
    {
        // Arrange
        $data = factory(User::class)->make()->toArray();

        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('admin.create'), $data);

        // Assert
        $response->assertStatus(403);
    }

    public function test_can_register_user_admin_when_we_dont_provided_all_data_return_Http_code_422()
    {
        // Arrange
        $data = [
            'first_name' => 'Martin',
            'last_name' => 'Legrand',
        ];

        // Action
        $response = $this->actingAs($this->userSuperAdmin, 'api')->json('POST', route('admin.create'), $data);

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_register_user_admin_when_we_provided_all_data_return_Http_code_201()
    {
        // Arrange
        $data = [
            'email' => 'robert_legrand@yahoo.fr',
            'first_name' => 'Robert',
            'job' => 'Organisateur',
            'last_name' => 'Legrand',
            'password' => '00000000',
            'phone' => '0123456789',
            'password_confirmation' => '00000000',
        ];

        // Action
        $response = $this->actingAs($this->userSuperAdmin, 'api')->json('POST', route('admin.create'), $data);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'id' => 3,
                    'attributes' => [
                        'email_address' => 'robert_legrand@yahoo.fr',
                        'first_name' => 'Robert',
                        'job' => 'Organisateur',
                        'last_name' => 'Legrand',
                        'phone' => '0123456789',
                        'role' => 'admin',
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/users/3',
                    ],
                    'relationships' => [
                        'events' => [
                            'data' => null
                        ]
                    ]
                ]
            ]);
    }
}
