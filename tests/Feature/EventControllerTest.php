<?php

namespace Tests\Feature;

use App\Address;
use App\Event;
use App\EventTranslation;
use App\Ticket;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->fakeEventId = Event::all()->count() + 1;

        $this->user = factory(User::class)->create();

        $this->userNotVerified = factory(User::class)->create([
            'email_verified_at' => null
        ]);
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

    public function test_can_get_all_list_of_event_with_a_complete_json_response_and_locale_en_return_http_code_200_with_english_translation()
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
            'end_date' => '2021-12-21',
            'is_active' => false,
            'picture' => 'http://lorempixel.com/640/480/',
            'start_date' => '2021-12-20',
            'start_time' => '12:00',
        ]);

        $frenshTranslation = factory(EventTranslation::class)->create([
            'additionel_information' => 'Excusez-moi, je ne me sens pas bien, est-ce que je peux aller à l’infirmerie ?',
            'event_id' => $event->id,
            'event_program' => ['Je suis désolé, j’ai oublié mes devoirs à la maison.', 'J’étais absent la semaine dernière.'],
            'locale' => 'fr',
            'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
            'title' => 'LES ELUCUBRATIONS',
        ]);

        $englishTranslation = factory(EventTranslation::class)->create([
            'additionel_information' => 'Excuse me, I don\'t feel well, can I go to the infirmary?',
            'event_id' => $event->id,
            'event_program' => ['I\'m sorry, I forgot my homework.', 'I was away last week.'],
            'locale' => 'en',
            'subtitle' => 'CARCASSONNE FESTIVAL 2020',
            'title' => 'THE ELUCUBRATIONS'
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
                            'event_translations' => [
                                [
                                    'additionel_information' => 'Excuse me, I don\'t feel well, can I go to the infirmary?',
                                    'event_program' => [
                                        'I\'m sorry, I forgot my homework.',
                                        'I was away last week.'
                                    ],
                                    'locale' => 'en',
                                    'subtitle' => 'CARCASSONNE FESTIVAL 2020',
                                    'title' => 'THE ELUCUBRATIONS'
                                ]
                            ],
                            'is_active' => false,
                            'picture' => 'http://lorempixel.com/640/480/',
                            'start_date' => '20/12/2021',
                            'start_time' => '12:00',
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

    public function test_can_show_event_when_locale_en_return_Http_code_200_with_english_translation()
    {
        // Arrange
        $event = factory(Event::class)->create([
            'start_date' => '2021-12-20',
            'picture' => 'http://lorempixel.com/640/480/',
        ]);

        $frenshTranslation = factory(EventTranslation::class)->create([
            'additionel_information' => 'Excusez-moi, je ne me sens pas bien, est-ce que je peux aller à l’infirmerie ?',
            'event_id' => $event->id,
            'event_program' => ['Je suis désolé, j’ai oublié mes devoirs à la maison.', 'J’étais absent la semaine dernière.'],
            'locale' => 'fr',
            'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
            'title' => 'LES ELUCUBRATIONS',
        ]);

        $englishTranslation = factory(EventTranslation::class)->create([
            'additionel_information' => 'Excuse me, I don\'t feel well, can I go to the infirmary?',
            'event_id' => $event->id,
            'event_program' => ['I\'m sorry, I forgot my homework.', 'I was away last week.'],
            'locale' => 'en',
            'subtitle' => 'CARCASSONNE FESTIVAL 2020',
            'title' => 'THE ELUCUBRATIONS'
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
                        'event_translations' => [
                            [
                                'additionel_information' => 'Excuse me, I don\'t feel well, can I go to the infirmary?',
                                'event_program' => [
                                    'I\'m sorry, I forgot my homework.',
                                    'I was away last week.'
                                ],
                                'locale' => 'en',
                                'subtitle' => 'CARCASSONNE FESTIVAL 2020',
                                'title' => 'THE ELUCUBRATIONS'
                            ],
                        ],
                        'is_active' => false,
                        'picture' => 'http://lorempixel.com/640/480/',
                        'start_date' => '20/12/2021',
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
    public function test_can_show_event_when_locale_fr_return_Http_code_200_with_frensh_translation()
    {
        // Arrange
        App::setLocale('fr');

        $event = factory(Event::class)->create([
            'start_date' => '2021-12-20',
            'picture' => 'http://lorempixel.com/640/480/',
        ]);

        $frenshTranslation = factory(EventTranslation::class)->create([
            'additionel_information' => 'Excusez-moi, je ne me sens pas bien, est-ce que je peux aller à l’infirmerie ?',
            'event_id' => $event->id,
            'event_program' => ['Je suis désolé, j’ai oublié mes devoirs à la maison.', 'J’étais absent la semaine dernière.'],
            'locale' => 'fr',
            'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
            'title' => 'LES ELUCUBRATIONS',
        ]);

        $englishTranslation = factory(EventTranslation::class)->create([
            'additionel_information' => 'Excuse me, I don\'t feel well, can I go to the infirmary?',
            'event_id' => $event->id,
            'event_program' => ['I\'m sorry, I forgot my homework.', 'I was away last week.'],
            'locale' => 'en',
            'subtitle' => 'CARCASSONNE FESTIVAL 2020',
            'title' => 'THE ELUCUBRATIONS'
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
                        'event_translations' => [
                            [
                                'additionel_information' => 'Excusez-moi, je ne me sens pas bien, est-ce que je peux aller à l’infirmerie ?',
                                'event_program' => [
                                    'Je suis désolé, j’ai oublié mes devoirs à la maison.',
                                    'J’étais absent la semaine dernière.'
                                ],
                                'locale' => 'fr',
                                'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
                                'title' => 'LES ELUCUBRATIONS'
                            ],
                        ],
                        'is_active' => false,
                        'picture' => 'http://lorempixel.com/640/480/',
                        'start_date' => '20/12/2021',
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

    public function test_can_show_event_when_user_is_connected_return_Http_code_200_with_all_translations()
    {
        // Arrange
        $event = factory(Event::class)->create([
            'start_date' => '2021-12-20',
            'picture' => 'http://lorempixel.com/640/480/',
        ]);

        $frenshTranslation = factory(EventTranslation::class)->create([
            'additionel_information' => 'Excusez-moi, je ne me sens pas bien, est-ce que je peux aller à l’infirmerie ?',
            'event_id' => $event->id,
            'event_program' => ['Je suis désolé, j’ai oublié mes devoirs à la maison.', 'J’étais absent la semaine dernière.'],
            'locale' => 'fr',
            'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
            'title' => 'LES ELUCUBRATIONS',
        ]);

        $englishTranslation = factory(EventTranslation::class)->create([
            'additionel_information' => 'Excuse me, I don\'t feel well, can I go to the infirmary?',
            'event_id' => $event->id,
            'event_program' => ['I\'m sorry, I forgot my homework.', 'I was away last week.'],
            'locale' => 'en',
            'subtitle' => 'CARCASSONNE FESTIVAL 2020',
            'title' => 'THE ELUCUBRATIONS'
        ]);


        // Action
        $response = $this->actingAs($this->user, 'api')->json('GET', route('events.show', $event->id));

        // Assert
        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'type' => 'events',
                    'id' => 1,
                    'attributes' => [
                        'event_translations' => [
                            [
                                'additionel_information' => 'Excuse me, I don\'t feel well, can I go to the infirmary?',
                                'event_program' => [
                                    'I\'m sorry, I forgot my homework.',
                                    'I was away last week.'
                                ],
                                'locale' => 'en',
                                'subtitle' => 'CARCASSONNE FESTIVAL 2020',
                                'title' => 'THE ELUCUBRATIONS'
                            ],
                            [
                                'additionel_information' => 'Excusez-moi, je ne me sens pas bien, est-ce que je peux aller à l’infirmerie ?',
                                'event_program' => [
                                    'Je suis désolé, j’ai oublié mes devoirs à la maison.',
                                    'J’étais absent la semaine dernière.'
                                ],
                                'locale' => 'fr',
                                'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
                                'title' => 'LES ELUCUBRATIONS'
                            ],
                        ],
                        'is_active' => false,
                        'picture' => 'http://lorempixel.com/640/480/',
                        'start_date' => '20/12/2021',
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

    public function test_can_store_event_when_user_is_connected_but_his_email_is_not_verified_Http_code_403()
    {
        // Arrange
        $data = factory(Event::class)->make()->toArray();

        // Action
        $response = $this->actingAs($this->userNotVerified, 'api')->json('POST', route('events.store'), $data);

        // Assert
        $response->assertForbidden()
            ->assertSeeText('Your email address is not verified.');
    }

    public function test_can_store_event_when_user_is_connected_return_Http_code_200()
    {
        // Arrange
        $data = [
            'attributes' => [
                'event' => [
                    'end_date' => '2021-12-21',
                    'picture' => UploadedFile::fake()->image('avatar.jpg'),
                    'start_date' => '2021-12-20',
                    'start_time' => '12:00',
                ],
                'event_translate_data' => [
                    'additionel_information' => 'Excusez-moi, je ne me sens pas bien, est-ce que je peux aller à l’infirmerie ?',
                    'event_program' => ['Je suis désolé, j’ai oublié mes devoirs à la maison.', 'J’étais absent la semaine dernière.'],
                    'locale' => 'fr',
                    'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
                    'title' => 'LES ELUCUBRATIONS',
                ]
            ]
        ];

        $time = time();

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
                        'end_date' => '21/12/2021',
                        'event_translations' => [
                            [
                                'additionel_information' => 'Excuse me, I don\'t feel well, can I go to the infirmary?',
                                'event_program' => [
                                    'I\'m sorry, I forgot my homework.',
                                    'I was away last week.'
                                ],
                                'locale' => 'en',
                                'subtitle' => 'CARCASSONNE FESTIVAL 2020',
                                'title' => 'THE ELUCUBRATIONS'
                            ],
                            [
                                'additionel_information' => 'Excusez-moi, je ne me sens pas bien, est-ce que je peux aller à l’infirmerie ?',
                                'event_program' => [
                                    'Je suis désolé, j’ai oublié mes devoirs à la maison.',
                                    'J’étais absent la semaine dernière.'
                                ],
                                'locale' => 'fr',
                                'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
                                'title' => 'LES ELUCUBRATIONS'
                            ],
                        ],
                        'is_active' => false,
                        'picture' => 'http://localhost/api/public/uploads/images/event_1_'. $time .'.jpg',
                        'start_date' => '20/12/2021',
                        'start_time' => '12:00',
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

    // // Update

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

    public function test_can_update_event_when_user_is_connected_but_his_email_is_not_verified_Http_code_403()
    {
        // Arrange
        $event = factory(Event::class)->create();

        $data = [
            'attributes' => [
                'event' => [
                    'end_date' => '2021-12-21',
                    'picture' => UploadedFile::fake()->image('avatar.jpg'),
                    'publish_at' => '2021-12-20',
                    'start_date' => '2021-12-20',
                    'start_time' => '12:00',
                ],
                'event_translate_data' => [
                    'additionel_information' => 'Excusez-moi, je ne me sens pas bien, est-ce que je peux aller à l’infirmerie ?',
                    'event_program' => ['Je suis désolé, j’ai oublié mes devoirs à la maison.', 'J’étais absent la semaine dernière.'],
                    'locale' => 'fr',
                    'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
                    'title' => 'LES ELUCUBRATIONS',
                ]
            ]
        ];

        // Action
        $response = $this->actingAs($this->userNotVerified, 'api')->json('PUT', route('events.update', $event->id), $data);

        // Assert
        $response->assertForbidden()
            ->assertSeeText('Your email address is not verified.');
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
            'event' => [
                'picture' => UploadedFile::fake()->image('avatar.jpg'),
            ]
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
            'attributes' => [
                'event' => [
                    'end_date' => '2021-12-21',
                    'picture' => UploadedFile::fake()->image('avatar.jpg'),
                    'publish_at' => '2021-12-20',
                    'start_date' => '2021-12-20',
                    'start_time' => '12:00',
                ],
                'event_translate_data' => [
                    'additionel_information' => 'Excusez-moi, je ne me sens pas bien, est-ce que je peux aller à l’infirmerie ?',
                    'event_program' => ['Je suis désolé, j’ai oublié mes devoirs à la maison.', 'J’étais absent la semaine dernière.'],
                    'locale' => 'fr',
                    'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
                    'title' => 'LES ELUCUBRATIONS',
                ]
            ]
        ];

        $time = time();

        $event = factory(Event::class)->create();
        $event->user()->associate($this->user)->save();

        $frenshTranslation = factory(EventTranslation::class)->create([
            'locale' => 'fr',
            'event_id' => $event->id
        ]);

        $englishTranslation = factory(EventTranslation::class)->create([
            'locale' => 'en',
            'event_id' => $event->id
        ]);

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
                        'end_date' => '21/12/2021',
                        'event_translations' => [
                            [
                                'additionel_information' => 'Excuse me, I don\'t feel well, can I go to the infirmary?',
                                'event_program' => [
                                    'I\'m sorry, I forgot my homework.',
                                    'I was away last week.'
                                ],
                                'locale' => 'en',
                                'subtitle' => 'CARCASSONNE FESTIVAL 2020',
                                'title' => 'THE ELUCUBRATIONS'
                            ],
                            [
                                'additionel_information' => 'Excusez-moi, je ne me sens pas bien, est-ce que je peux aller à l’infirmerie ?',
                                'event_program' => [
                                    'Je suis désolé, j’ai oublié mes devoirs à la maison.',
                                    'J’étais absent la semaine dernière.'
                                ],
                                'locale' => 'fr',
                                'subtitle' => 'FESTIVAL DE CARCASSONNE 2020',
                                'title' => 'LES ELUCUBRATIONS'
                            ],
                        ],
                        'is_active' => false,
                        'picture' => 'http://localhost/api/public/uploads/images/event_1_'. $time .'.jpg',
                        'publish_at' => '20/12/2021',
                        'start_date' => '20/12/2021',
                        'start_time' => '12:00',
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
