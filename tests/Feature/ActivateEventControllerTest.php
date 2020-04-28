<?php

namespace Tests\Feature;

use App\Event;
use App\Message;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class ActivateEventControllerTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'role' => 'user',
        ]);

        $this->userAdmin = factory(User::class)->create([
            'role' => 'admin',
        ]);
    }

    public function test_can_active_event_when_user_is_not_connected_return_Http_code_401()
    {
        // Assert
        $event = factory(Event::class)->create();

        // Action
        $response = $this->json('GET', route('events.active', $event->id));

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_active_event_when_user_is_connected_and_his_role_user_return_Http_code_403()
    {
        // Assert
        $event = factory(Event::class)->create();

        // Action
        $response = $this->actingAs($this->user, 'api')->json('GET', route('events.active', $event->id));

        // Assert
        $response->assertForbidden();
    }

    public function test_can_active_event_when_we_dont_provided_event_return_exception()
    {
        // Exception
        $this->expectException(UrlGenerationException::class);

        // Action
        $response = $this->actingAs($this->userAdmin, 'api')->json('GET', route('events.active'));
    }

    public function test_can_active_event_when_user_is_connected_and_his_role_admin_return_Http_code_200()
    {
        // Assert
        $event = factory(Event::class)->create();

        // Action
        $response = $this->actingAs($this->userAdmin, 'api')->json('GET', route('events.active', $event->id));

        // Assert
        $response->assertOk()
            ->assertJson([
                'message' => 'Activation successfully.'
            ]);
    }

    public function test_can_active_event_when_locale_fr_return_Http_code_200()
    {
        // Assert
        App::setLocale('fr');
        $event = factory(Event::class)->create();
        // Action
        $response = $this->actingAs($this->userAdmin, 'api')->json('GET', route('events.active', $event->id));

        // Assert
        $response->assertOk()
            ->assertJson([
                'message' => 'Activation réussie.'
            ]);
    }

    public function test_id_user_admin_send_messages_at_time_of_event_activation_return_true()
    {
        // Assert
        $event = factory(Event::class)->create();

        // Action
        $this->actingAs($this->userAdmin, 'api')->json('GET', route('events.active', $event->id));
        $message = $this->userAdmin->messages->last();

        // Assert
        $this->assertEquals('Your event is validated, you can publish it.', $message->content);
    }

    public function test_id_user_admin_send_messages_at_time_of_event_activation_when_locale_fr_return_true()
    {
        // Assert
        App::setLocale('fr');
        $event = factory(Event::class)->create();

        // Action
        $this->actingAs($this->userAdmin, 'api')->json('GET', route('events.active', $event->id));
        $message = $this->userAdmin->messages->last();

        // Assert
        $this->assertEquals('Votre événement est validé, vous pouvez le publier.', $message->content);
    }
}
