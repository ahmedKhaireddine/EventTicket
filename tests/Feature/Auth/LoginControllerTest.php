<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        \Artisan::call('passport:install',['-vvv' => true]);

        $this->user = factory(User::class)->create([
                'email' => 'robert_legrand@yahoo.fr',
                'first_name' => 'Robert',
                'job' => 'Organisateur',
                'last_name' => 'Legrand',
                'password' => 'userpass',
                'phone' => '0123456789',
                'role' => 'user'
        ]);
    }

    // login

    public function test_can_login_when_the_email_or_password_is_not_valid_return_Http_code_401()
    {
        // Arrange
        $user = [
            'email' => 'robert-legrand@yahoo.com',
            'password' => 'userpass0'
        ];

        // Action
        $response = $this->json('POST', route('login'), $user);

        // Assert
        $response->assertStatus(401)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => 'Invalid credentials.'
            ]);
    }

    public function test_can_login_when_we_dont_provide_email_and_password_return_Http_code_422()
    {
        // Action
        $response = $this->json('POST', route('login'));

        // Assert
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.']
                ]
            ]);
    }

    public function test_can_login_when_we_provided_email_and_password_return_Http_code_200()
    {
        // Arrange
        $user = [
            'email' => 'robert_legrand@yahoo.fr',
            'password' => 'userpass'
        ];

        // Action
        $response = $this->json('POST', route('login'), $user);

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'success' => [
                    'token'
                ]
            ]);
    }

    // signout

    public function test_can_logout_when_user_is_not_connected_return_Http_code_401()
    {
        // Action
        $response = $this->json('POST', route('signout'));

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_logout_when_user_is_connected_return_Http_code_204()
    {
        // Arrange
        $user = [
            'email' => 'robert_legrand@yahoo.fr',
            'password' => 'userpass'
        ];

        Auth::attempt($user);
        $token = Auth::user()->createToken('fake_user')->accessToken;
        $headers = ['Authorization' => "Bearer {$token}"];

        // Action
        $response = $this->json('POST', route('signout'), [], $headers);

        // Assert
        $response->assertStatus(204);
    }

    // Get user connected

    public function test_get_user_when_user_is_not_connected_return_Http_code_401()
    {
        // Action
        $response = $this->json('GET', route('user'));

        // Assert
        $response->assertStatus(401);
    }

    public function test_get_user_when_user_is_connected_return_Http_code_200()
    {
        // Arrange
        $user = [
            'email' => 'robert_legrand@yahoo.fr',
            'password' => 'userpass'
        ];

        Auth::attempt($user);
        $token = Auth::user()->createToken('fake_user')->accessToken;
        $headers = ['Authorization' => "Bearer {$token}"];

        // Action
        $response = $this->json('GET', route('user'), [], $headers);

        // Assert
        $response->assertOk()
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'id' => 1,
                    'attributes' => [
                        'email_address' => 'robert_legrand@yahoo.fr',
                        'first_name' => 'Robert',
                        'job' => 'Organisateur',
                        'last_name' => 'Legrand',
                        'phone' => '0123456789',
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
}
