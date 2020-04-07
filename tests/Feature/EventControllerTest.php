<?php

namespace Tests\Feature;

use App\Address;
use App\Event;
use App\Ticket;
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
    use DatabaseMigrations, RefreshDatabase;

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
        $address = factory(Address::class)->create([
            'additionel_information' => 'Suite 584',
            'city' => 'Port Bobbyfurt',
            'country' => 'Bahamas',
            'postal_code' => '95929-9607',
            'street_address' => '2316 Clemmie Throughway',
            'venue' => 'Maggio LLC'
        ]);

        $event = factory(Event::class)->create([
            'additionel_information' => 'Des informations utile.',
            'end_date' => '2021-12-21',
            'event_program' => ['Des informations utile.', 'Des informations utile.'],
            'is_active' => false,
            'picture' => 'http://lorempixel.com/640/480/',
            'publish_at' => '2021-12-20',
            'start_date' => '2021-12-20',
            'start_time' => '12:00',
            'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
            'title' => 'LES ELUCUBRATIONS',
        ]);

        $ticket = factory(Ticket::class)->create([
            'description' => 'Des informations utile.',
            'event_id' => $event->id,
            'price' => 2000,
            'tickets_number' => 3000,
            'tickets_remain' => 3000,
            'type' => 'Block A'
        ]);

        $ticketTwo = factory(Ticket::class)->create([
            'description' => 'Des informations utile.',
            'event_id' => $event->id,
            'price' => 1500,
            'tickets_number' => 1000,
            'tickets_remain' => 1000,
            'type' => 'Block C'
        ]);

        $event->address()->associate($address)->save();

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
                            'address' => [
                                'additionel_information' => 'Suite 584',
                                'city' => 'Port Bobbyfurt',
                                'country' => 'Bahamas',
                                'postal_code' => '95929-9607',
                                'street_address' => '2316 Clemmie Throughway',
                                'venue' => 'Maggio LLC',
                                'full_address' => '2316 Clemmie Throughway, 95929-9607 Port Bobbyfurt',
                            ],
                            'end_date' => '21/12/2021',
                            'event_program' => ['Des informations utile.', 'Des informations utile.'],
                            'event_tickets' => [
                                'total_number_of_tickets' => 4000,
                                'number_of_tickets_remaining' => 4000,
                                'format_price_to_display' => 'A Partir de 15.00 €',
                                'tickets' => [
                                    [
                                        'description' => 'Des informations utile.',
                                        'price' => 2000,
                                        'tickets_number' => 3000,
                                        'tickets_remain' => 3000,
                                        'type' => 'Block A'
                                    ],
                                    [
                                        'description' => 'Des informations utile.',
                                        'price' => 1500,
                                        'tickets_number' => 1000,
                                        'tickets_remain' => 1000,
                                        'type' => 'Block C'
                                    ]
                                ]
                            ],
                            'is_active' => false,
                            'picture' => 'http://lorempixel.com/640/480/',
                            'publish_at' => '20/12/2021',
                            'start_date' => '20/12/2021',
                            'start_time' => '12:00',
                            'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
                            'title' => 'LES ELUCUBRATIONS',
                        ],
                        'links' => [
                            'self' => 'http://localhost/api/events/1',
                        ],
                        'relationships' => [
                            'address' => [
                                'data' => [
                                    'type' => 'addresses',
                                    'id' => $address->id
                                ]
                            ],
                            'tickets' => [
                                'data' => [
                                    [
                                        'type' => 'tickets',
                                        'id' => $ticket->id
                                    ],
                                    [
                                        'type' => 'tickets',
                                        'id' => $ticketTwo->id
                                    ]
                                ]
                            ],
                            'user' => [
                                'data' => null
                            ]
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
            'start_date' => '2021-12-20',
            'is_active' => false,
            'picture' => 'http://lorempixel.com/640/480/',
            'publish_at' => '2021-12-20',
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
                        'is_active' => false,
                        'picture' => 'http://lorempixel.com/640/480/',
                        'publish_at' => '20/12/2021',
                        'start_date' => '20/12/2021',
                        'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
                        'title' => 'LES ELUCUBRATIONS',
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/events/1',
                    ],
                    'relationships' => [
                        'address' => [
                            'data' => null
                        ],
                        'user' => [
                            'data' => null
                        ]
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
            'end_date' => '2021-12-21',
            'event_program' => ['Des informations utile.', 'Des informations utile.'],
            'picture' => UploadedFile::fake()->image('avatar.jpg'),
            'publish_at' => '2021-12-20',
            'start_date' => '2021-12-20',
            'start_time' => '12:00',
            'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
            'title' => 'LES ELUCUBRATIONS',
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
                        'end_date' => '21/12/2021',
                        'event_program' => ['Des informations utile.', 'Des informations utile.'],
                        'is_active' => false,
                        'picture' => 'http://localhost/api/public/uploads/images/LES_ELUCUBRATIONS_'. time() .'.jpg',
                        'publish_at' => '20/12/2021',
                        'start_date' => '20/12/2021',
                        'start_time' => '12:00',
                        'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
                        'title' => 'LES ELUCUBRATIONS',
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/events/1',
                    ],
                    'relationships' => [
                        'address' => [
                            'data' => null
                        ],
                        'user' => [
                            'data' => [
                                'type' => 'users',
                                'id' => $this->user->id
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_store_event_without_giving_data_return_Http_code_422()
    {
        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('events.store'));

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_store_event_when_event_title_exists_return_Http_code_422()
    {
        // Arrange
        $event = factory(Event::class)->create([
            'title' => 'ELUCUBRATIONS',
        ]);

        $data = [
            'additionel_information' => 'Des informations utile.',
            'start_date' => '2021-12-20',
            'picture' => UploadedFile::fake()->image('avatar.jpg'),
            'publish_at' => '2021-12-20',
            'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
            'title' => 'ELUCUBRATIONS',
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('events.store'), $data);

        // Assert
        $response->assertStatus(422);
    }

    // Update

    public function test_can_update_event_when_user_is_not_connected_Http_code_401()
    {
        // Arrange
        $data = factory(Event::class)->make()->toArray();
        $event = factory(Event::class)->create();

        // Action
        $response = $this->json('PUT', route('events.update', $event->id), $data);

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_update_event_when_event_dont_exists_return_Http_code_404()
    {
        // Arrange
        $data = factory(Event::class)->make()->toArray();

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('events.show', $this->fakeEventId), $data);

        // Assert
        $response->assertNotFound();
    }

    public function test_can_update_event_when_event_title_exists_return_Http_code_422()
    {
        // Arrange
        $event = factory(Event::class)->create([
            'title' => 'ELUCUBRATIONS',
        ]);

        $secondEvent =  factory(Event::class)->create();

        $data = [
            'title' => 'ELUCUBRATIONS',
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('events.update', $secondEvent->id), $data);

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_update_event_without_giving_data_return_Http_code_200()
    {
        // Arrange
        $event = factory(Event::class)->create();

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('events.update', $event->id));

        // Assert
        $response->assertStatus(200);
    }

    public function test_can_update_event_when_data_contains_a_picture_return_Http_code_200()
    {
        // Arrange
        $data = [
            'picture' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $event = factory(Event::class)->create();

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('events.update', $event->id), $data);

        // Assert
        $response->assertStatus(200);
    }

    public function test_can_update_event_when_data_contains_a_date_and_publish_at_return_Http_code_200()
    {
        // Arrange
        $data = [
            'end_date' => '2020-12-21',
            'publish_at' => '2020-12-20',
            'start_date' => '2020-12-20',
            'start_time' => '12:00',
        ];

        $event = factory(Event::class)->create();

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('events.update', $event->id), $data);

        // Assert
        $response->assertStatus(200);
    }

    public function test_can_update_event_return_Http_code_200_with_json()
    {
        // Arrange
        $data = [
            'additionel_information' => 'Des informations utile.',
            'end_date' => '2021-12-21',
            'event_program' => ['Des informations utile.', 'Des informations utile.'],
            'picture' => UploadedFile::fake()->image('avatar.jpg'),
            'publish_at' => '2021-12-20',
            'start_date' => '2021-12-20',
            'start_time' => '12:00',
            'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
            'title' => 'LES ELUCUBRATIONS',
        ];

        $event = factory(Event::class)->create();
        $event->user()->associate($this->user)->save();

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('events.update', $event->id), $data);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'events',
                    'id' => 1,
                    'attributes' => [
                        'additionel_information' => 'Des informations utile.',
                        'end_date' => '21/12/2021',
                        'event_program' => ['Des informations utile.', 'Des informations utile.'],
                        'is_active' => false,
                        'picture' => 'http://localhost/api/public/uploads/images/LES_ELUCUBRATIONS_'. time() .'.jpg',
                        'publish_at' => '20/12/2021',
                        'start_date' => '20/12/2021',
                        'start_time' => '12:00',
                        'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
                        'title' => 'LES ELUCUBRATIONS',
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/events/1',
                    ],
                    'relationships' => [
                        'address' => [
                            'data' => null
                        ],
                        'user' => [
                            'data' => [
                                'type' => 'users',
                                'id' => $this->user->id
                            ]
                        ]
                    ]
                ]
            ]);
    }
}
