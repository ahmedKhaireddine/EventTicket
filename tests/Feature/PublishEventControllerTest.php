<?php

namespace Tests\Feature;

use App\Event;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PublishEventControllerTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'role' => 'user'
        ]);

        $this->userAdmin = factory(User::class)->create([
            'role' => 'admin'
        ]);
    }

    public function test_can_publish_event_when_user_is_not_connected_return_Http_code_401()
    {
        // Assert
        $event = factory(Event::class)->create();

        // Action
        $response = $this->json('GET', route('events.publish', $event->id));

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_publish_event_when_user_is_connected_and_his_role_admin_return_Http_code_403()
    {
        // Assert
        $event = factory(Event::class)->create();

        // Action
        $response = $this->actingAs($this->userAdmin, 'api')->json('GET', route('events.publish', $event->id));

        // Assert
        $response->assertForbidden();
    }

    public function test_can_publish_event_when_we_dont_provided_event_return_exception()
    {
        // Exception
        $this->expectException(UrlGenerationException::class);

        // Action
        $response = $this->actingAs($this->user, 'api')->json('GET', route('events.publish'));
    }

    public function test_can_publish_event_when_we_provided_params_ot_type_string_return_Http_code_404()
    {
        // Arrange
        $param = '1string';

        // Action
        $response = $this->actingAs($this->user, 'api')->json('GET', route('events.publish', $param));

        // Assert
        $response->assertNotFound();
    }

    public function test_can_publish_event_when_event_is_not_active_return_Http_code_500()
    {
        // Assert
        $event = factory(Event::class)->create();

        // Action
        $response = $this->actingAs($this->user, 'api')->json('GET', route('events.publish', $event->id));

        // Assert
        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Your event is not activated by the platform, please make the validation request.'
            ]);
    }

    public function test_can_publish_event_when_event_is_not_active_and_locale_fr_return_Http_code_500()
    {
        // Assert
        App::setLocale('fr');
        $event = factory(Event::class)->create();

        // Action
        $response = $this->actingAs($this->user, 'api')->json('GET', route('events.publish', $event->id));

        // Assert
        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Votre événement n\'est pas activé par la plateforme, veuillez faire la demande de validation.'
            ]);
    }

    public function test_can_publish_event_when_event_provided_and_his_active_return_Http_code_200()
    {
        // Arrange
        $event = factory(Event::class)->create([
            'is_active' => true,
            'publish_at' => null
        ]);

        // Action
        $response = $this->actingAs($this->user, 'api')->json('GET', route('events.publish', $event->id));

        // Assert
        $response->assertOk()
            ->assertJson([
                'message' => 'Successful publication.'
            ]);
    }

    public function test_can_publish_event_when_event_provided_and_his_active_and_locale_fr_return_Http_code_200()
    {
        // Arrange
        App::setLocale('fr');
        $event = factory(Event::class)->create([
            'is_active' => true,
            'publish_at' => null
        ]);

        // Action
        $response = $this->actingAs($this->user, 'api')->json('GET', route('events.publish', $event->id));

        // Assert
        $response->assertOk()
            ->assertJson([
                'message' => 'Publication réussie.'
            ]);
    }
}
