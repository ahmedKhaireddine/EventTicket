<?php

namespace Tests\Feature;

use App\Event;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->fakeEventId = Event::all()->count() + 1;

        $this->user = factory(User::class)->create();
    }

    // Index

    public function test_can_get_all_list_of_event_return_Http_code_200()
    {
        // Arrange
        $events = factory(Event::class, 4)->create();

        // Action
        $response = $this->json('GET', route('events.index'));

        // Assert
        $response
            ->assertOk()
            ->assertJsonCount(4, 'data');
    }

    public function test_can_get_all_list_of_event_with_a_complete_json_response_return_http_code_200()
    {
        // Arrange
        $event = factory(Event::class)->create([
            'additionel_information' => 'Des informations utile.',
            'date' => Carbon::parse('20-12-2021 12:00'),
            'is_active' => false,
            'picture' => 'http://lorempixel.com/640/480/',
            'publish_at' => Carbon::parse('20-12-2021'),
            'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
            'title' => 'LES ELUCUBRATIONS',
        ]);

        // Action
        $response = $this->json('GET', route('events.index'));

        // Assert
        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    [
                        'type' => 'events',
                        'id' => 1,
                        'attributes' => [
                            'additionel_information' => 'Des informations utile.',
                            'date' => '20/12/2021',
                            'is_active' => false,
                            'picture' => 'http://lorempixel.com/640/480/',
                            'publish_at' => '20/12/2021',
                            'start_time' => '12:00',
                            'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
                            'title' => 'LES ELUCUBRATIONS',
                        ],
                        'links' => [
                            'self' => 'http://localhost/api/events/1',
                        ]
                    ]
                ]
            ]);
    }

    // Show

    public function test_can_show_event_we_dont_provide_id_for_the_route_return_exeption_Missing_required_parameters()
    {
        // Exception
        $this->expectException(UrlGenerationException::class);

        // Action
        $response = $this->json('GET', route('events.show'));
    }

    public function test_can_show_event_when_event_dont_exists_return_Http_code_404()
    {
        // Action
        $response = $this->json('GET', route('events.show', $this->fakeEventId));

        // Assert
        $response->assertNotFound();
    }

    public function test_can_show_event_return_Http_code_200()
    {
        // Arrange
        $event = factory(Event::class)->create([
            'additionel_information' => 'Des informations utile.',
            'date' => Carbon::parse('20-12-2021 12:00'),
            'is_active' => false,
            'picture' => 'http://lorempixel.com/640/480/',
            'publish_at' => Carbon::parse('20-12-2021'),
            'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
            'title' => 'LES ELUCUBRATIONS',
        ]);

        // Action
        $response = $this->json('GET', route('events.show', $event->id));

        // Assert
        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'type' => 'events',
                    'id' => 1,
                    'attributes' => [
                        'additionel_information' => 'Des informations utile.',
                        'date' => '20/12/2021',
                        'is_active' => false,
                        'picture' => 'http://lorempixel.com/640/480/',
                        'publish_at' => '20/12/2021',
                        'start_time' => '12:00',
                        'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
                        'title' => 'LES ELUCUBRATIONS',
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/events/1',
                    ]
                ]
            ]);
    }

    // Store

    public function test_can_store_event_when_user_is_not_connected_Http_code_401()
    {
        // Arrange
        $data = factory(Event::class)->make()->toArray();

        // Action
        $response = $this->json('POST', route('events.store'), $data);

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_store_event_when_user_is_connected_return_Http_code_200()
    {
        // Arrange
        $data = [
            'additionel_information' => 'Des informations utile.',
            'date' => Carbon::parse('20-12-2021 12:00'),
            'picture' => UploadedFile::fake()->image('avatar.jpg'),
            'publish_at' => Carbon::parse('20-12-2021'),
            'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
            'title' => 'ELUCUBRATIONS',
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('events.store'), $data);

        // Assert
        $response
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'events',
                    'id' => 1,
                    'attributes' => [
                        'additionel_information' => 'Des informations utile.',
                        'date' => '20/12/2021',
                        'is_active' => false,
                        'picture' => 'http://localhost/api/public/uploads/images/ELUCUBRATIONS_'. time() .'.jpg',
                        'publish_at' => '20/12/2021',
                        'start_time' => '12:00',
                        'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
                        'title' => 'ELUCUBRATIONS',
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/events/1',
                    ]
                ]
            ]);
    }

    public function test_can_store_event_without_giving_data_return_Httap_code_422()
    {
        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('events.store'));

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_store_event_when_event_title_exists_return_Httap_code_422()
    {
        // Arrange
        $event = factory(Event::class)->create([
            'title' => 'ELUCUBRATIONS',
        ]);

        $data = [
            'additionel_information' => 'Des informations utile.',
            'date' => Carbon::parse('20-12-2021 12:00'),
            'picture' => UploadedFile::fake()->image('avatar.jpg'),
            'publish_at' => Carbon::parse('20-12-2021'),
            'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
            'title' => 'ELUCUBRATIONS',
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('events.store'), $data);

        // Assert
        $response->assertStatus(422);
    }
}
