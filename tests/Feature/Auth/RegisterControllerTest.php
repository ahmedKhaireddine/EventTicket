<?php

namespace Tests\Feature\Auth;

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

    public function test_can_register_user_admin_when_we_dont_provided_data_return_Http_code_422()
    {
        // Action
        $response = $this->actingAs($this->userSuperAdmin, 'api')->json('POST', route('admin.create'));

        // Assert
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['The email field is required.'],
                    'job' => ['The job field is required.'],
                    'first_name' => ['The first name field is required.'],
                    'last_name' => ['The last name field is required.'],
                    'password' => ['The password field is required.'],
                    'phone' => ['The phone field is required.'],
                ]
            ]);
    }

    public function test_can_register_user_admin_when_we_dont_confirmation_password_return_Http_code_422()
    {
        $data = [
            'email' => 'robert_legrand@yahoo.fr',
            'first_name' => 'Robert',
            'job' => 'Organisateur',
            'last_name' => 'Legrand',
            'password' => 'userpass',
            'phone' => '0123456789',
        ];

        // Action
        $response = $this->actingAs($this->userSuperAdmin, 'api')->json('POST', route('admin.create'), $data);

        // Assert
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'password' => ['The password confirmation does not match.']
                ]
            ]);
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
