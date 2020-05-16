<?php

namespace Tests\Feature;

use App\Event;
use App\Ticket;
use App\TicketTranslation;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();

        $this->userNotVerified = factory(User::class)->create([
            'email_verified_at' => null
        ]);

        $this->event = factory(Event::class)->create();

        $this->ticket = factory(Ticket::class)->create([
            'event_id' => $this->event->id,
            'price' => 2000,
            'tickets_number' => 50000,
            'tickets_remain' => 50000,
        ]);

        $frenshTranslation = factory(TicketTranslation::class)->create([
            'description' => 'Cette block vous le trouvez devant la seins.',
            'locale' => 'fr',
            'location' => 'Block A',
            'ticket_id' => $this->ticket->id,
            'type' => 'Gratuit',
        ]);

        $englishTranslation = factory(TicketTranslation::class)->create([
            'description' => 'This block you find it in front of the breast.',
            'locale' => 'en',
            'location' => 'Block A',
            'ticket_id' => $this->ticket->id,
            'type' => 'Free',
        ]);

        $this->fakeTicketId = Ticket::all()->count() + 1;
    }

    // Show

    public function test_can_show_ticket_when_user_is_not_connected_return_Http_code_401()
    {
        // Action
        $response = $this->json('GET', route('tickets.show', $this->ticket));

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_show_ticket_when_user_is_connected_but_his_email_is_not_verified_return_Http_code_403()
    {
        // Action
        $response = $this->actingAs($this->userNotVerified, 'api')->json('GET', route('tickets.show', $this->ticket));

        // Assert
        $response->assertForbidden()
            ->assertSeeText('Your email address is not verified.');
    }

    public function test_can_show_ticket_when_ticket_dont_exists_return_Http_code_404()
    {
        // Action
        $response = $this->actingAs($this->user, 'api')->json('GET', route('tickets.show', $this->fakeTicketId));

        // Assert
        $response->assertNotFound();
    }

    public function test_can_show_ticket_when_user_is_connected_and_ticket_exists_and_locale_en_return_Http_code_200_with_english_translation()
    {
        // Action
        $response = $this->actingAs($this->user, 'api')->json('GET', route('tickets.show', $this->ticket));

        // Assert
        $response->assertOk()
            ->assertJson([
                'data' => [
                    'type' => 'tickets',
                    'id' => 1,
                    'attributes' => [
                        'price' => 2000,
                        'ticket_translations' => [
                            [
                                'description' => 'This block you find it in front of the breast.',
                                'locale' => 'en',
                                'location' => 'Block A',
                                'type' => 'Free',
                            ],
                            [
                                'description' => 'Cette block vous le trouvez devant la seins.',
                                'locale' => 'fr',
                                'location' => 'Block A',
                                'type' => 'Gratuit',
                            ],
                        ],
                        'tickets_number' => 50000,
                        'tickets_remain' => 50000,
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/tickets/1'
                    ],
                    'relationships' => [
                        'event' => [
                            'data' => [
                                'type' => 'events',
                                'id' => $this->event->id
                            ]
                        ]
                    ]
                ]
            ]);
    }

    // Store

    public function test_can_store_ticket_when_user_is_not_connected_return_Http_code_401()
    {
        // Arrange
        $data = [
            'attributes' => [
                'event_id' => $this->event->id,
                'ticket' => [
                    'number' => 60000,
                    'price' => 3000,
                ],
                'ticket_translation_data' => [
                    'description' => 'Cette zone vous le trouver a l\'étage.',
                    'locale' => 'fr',
                    'location' => 'Zone B',
                    'type' => 'Payant',
                ]
            ]
        ];

        // Action
        $response = $this->json('POST', route('tickets.store'), $data);

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_store_ticket_when_user_is_connected_but_his_email_is_not_verified_return_Http_code_403()
    {
        // Arrange
        $data = [
            'attributes' => [
                'event_id' => $this->event->id,
                'ticket' => [
                    'number' => 60000,
                    'price' => 3000,
                ],
                'ticket_translation_data' => [
                    'description' => 'Cette zone vous le trouver a l\'étage.',
                    'locale' => 'fr',
                    'location' => 'Zone B',
                    'type' => 'Payant',
                ]
            ]
        ];

        // Action
        $response = $this->actingAs($this->userNotVerified, 'api')->json('POST', route('tickets.store'), $data);

        // Assert
        $response->assertForbidden()
            ->assertSeeText('Your email address is not verified.');
    }

    public function test_can_store_ticket_when_we_dont_provided_event_id_return_Http_code_422()
    {
        // Arrange
        $data = [
            'attributes' => [
                'ticket' => [
                    'number' => 60000,
                    'price' => 3000,
                ],
                'ticket_translation_data' => [
                    'description' => 'Cette zone vous le trouver a l\'étage.',
                    'locale' => 'fr',
                    'location' => 'Zone B',
                    'type' => 'Payant',
                ]
            ]
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('tickets.store'), $data);

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_store_ticket_when_we_dont_provided_ticket_data_return_Http_code_422()
    {
        // Arrange
        $data = [
            'attributes' => [
                'event_id' => $this->event->id,
                'ticket_translation_data' => [
                    'description' => 'Cette zone vous le trouver a l\'étage.',
                    'locale' => 'fr',
                    'location' => 'Zone B',
                    'type' => 'Payant',
                ]
            ]
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('tickets.store'), $data);

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_store_ticket_when_we_dont_provided_ticket_translation_data_return_Http_code_422()
    {
        // Arrange
        $data = [
            'attributes' => [
                'event_id' => $this->event->id,
                'ticket' => [
                    'number' => 60000,
                    'price' => 3000,
                ],
            ]
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('tickets.store'), $data);

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_store_ticket_when_we_provided_all_ticket_data_return_Http_code_201()
    {
        // Arrange
        $data = [
            'attributes' => [
                'event_id' => $this->event->id,
                'ticket' => [
                    'number' => 60000,
                    'price' => 3000,
                ],
                'ticket_translation_data' => [
                    'description' => 'Vous trouvez cette zone à l\'étage.',
                    'locale' => 'fr',
                    'location' => 'Zone B',
                    'type' => 'Payant',
                ]
            ]
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('tickets.store'), $data);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'tickets',
                    'id' => 2,
                    'attributes' => [
                        'price' => 3000,
                        'ticket_translations' => [
                            [
                                'description' => 'You find this area upstairs.',
                                'locale' => 'en',
                                'location' => 'Zone B',
                                'type' => 'Paying',
                            ],
                            [
                                'description' => 'Vous trouvez cette zone à l\'étage.',
                                'locale' => 'fr',
                                'location' => 'Zone B',
                                'type' => 'Payant',
                            ],
                        ],
                        'tickets_number' => 60000,
                        'tickets_remain' => 60000,
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/tickets/2'
                    ],
                    'relationships' => [
                        'event' => [
                            'data' => [
                                'type' => 'events',
                                'id' => $this->event->id
                            ]
                        ]
                    ]
                ]
            ]);
    }

    // Update

    public function test_can_update_ticket_when_user_is_not_connected_return_Http_code_401()
    {
        // Arrange
        $data = [
            'attributes' => [
                'event_id' => $this->event->id,
                'ticket' => [
                    'number' => 40000,
                    'price' => 3000,
                ],
                'ticket_translation_data' => [
                    'description' => 'Cette zone vous le trouver a l\'étage.',
                    'locale' => 'fr',
                    'location' => 'Zone B',
                    'type' => 'Payant',
                ]
            ]
        ];

        // Action
        $response = $this->json('PUT', route('tickets.update', $this->ticket->id), $data);

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_update_ticket_when_user_is_connected_but_his_email_is_not_verified_return_Http_code_403()
    {
        // Arrange
        $data = [
            'attributes' => [
                'event_id' => $this->event->id,
                'ticket' => [
                    'number' => 40000,
                    'price' => 3000,
                ],
                'ticket_translation_data' => [
                    'description' => 'Cette zone vous le trouver a l\'étage.',
                    'locale' => 'fr',
                    'location' => 'Zone B',
                    'type' => 'Payant',
                ]
            ]
        ];

        // Action
        $response = $this->actingAs($this->userNotVerified, 'api')->json('PUT', route('tickets.update', $this->ticket->id), $data);

        // Assert
        $response->assertForbidden()
            ->assertSeeText('Your email address is not verified.');
    }

    public function test_can_update_ticket_when_ticket_dont_exists_return_Http_code_404()
    {
        // Arrange
        $data = [
            'attributes' => [
                'event_id' => $this->event->id,
                'ticket' => [
                    'number' => 40000,
                    'price' => 3000,
                ],
                'ticket_translation_data' => [
                    'description' => 'Cette zone vous le trouver a l\'étage.',
                    'locale' => 'fr',
                    'location' => 'Zone B',
                    'type' => 'Payant',
                ]
            ]
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('tickets.update', $this->fakeTicketId), $data);

        // Assert
        $response->assertNotFound();
    }

    public function test_can_update_ticket_when_we_dont_provided_event_id_return_Http_code_422()
    {
        // Arrange
        $data = [
            'attributes' => [
                'ticket' => [
                    'number' => 40000,
                    'price' => 3000,
                ],
                'ticket_translation_data' => [
                    'description' => 'Cette zone vous le trouver a l\'étage.',
                    'locale' => 'fr',
                    'location' => 'Zone B',
                    'type' => 'Payant',
                ]
            ]
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('tickets.update', $this->ticket->id), $data);

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_update_ticket_when_we_dont_provided_locale_return_Http_code_422()
    {
        // Arrange
        $data = [
            'attributes' => [
                'ticket' => [
                    'number' => 40000,
                    'price' => 3000,
                ],
                'ticket_translation_data' => [
                    'description' => 'Cette zone vous le trouver a l\'étage.',
                    'location' => 'Zone B',
                    'type' => 'Payant',
                ]
            ]
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('tickets.update', $this->ticket->id), $data);

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_update_ticket_when_when_ticket_is_not_related_to_the_past_event_return_Http_code_422()
    {
        // Arrange
        $anotherEvent = factory(Event::class)->create([]);

        $data = [
            'attributes' => [
                'event_id' => $anotherEvent->id,
                'ticket' => [
                    'number' => 40000,
                    'price' => 3000,
                ],
                'ticket_translation_data' => [
                    'description' => 'Cette zone vous le trouver a l\'étage.',
                    'locale' => 'fr',
                    'location' => 'Zone B',
                    'type' => 'Payant',
                ]
            ]
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('tickets.update', $this->ticket->id), $data);

        // Assert
        $response->assertStatus(422)
            ->assertSeeText('The ticket is not related to the event provided.');
    }

    public function test_can_update_ticket_when_we_provided_all_params_return_Http_code_200()
    {
        // Arrange
        $data = [
            'attributes' => [
                'event_id' => $this->event->id,
                'ticket' => [
                    'number' => 60000,
                    'price' => 3000,
                ],
                'ticket_translation_data' => [
                    'description' => 'Cette zone vous le trouver a l\'étage.',
                    'locale' => 'fr',
                    'location' => 'Zone B',
                    'type' => 'Payant',
                ]
            ]
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('tickets.update', $this->ticket->id), $data);

        // Assert
        $response->assertOk()
            ->assertJson([
                'data' => [
                    'type' => 'tickets',
                    'id' => 1,
                    'attributes' => [
                        'price' => 3000,
                        'ticket_translations' => [
                            [
                              'description' => 'This area you find it upstairs.',
                              'locale' => 'en',
                              'location' => 'Zone B',
                              'type' => 'Paying',
                            ],
                            [
                              'description' => 'Cette zone vous le trouver a l\'étage.',
                              'locale' => 'fr',
                              'location' => 'Zone B',
                              'type' => 'Payant',
                            ],
                        ],
                        'tickets_number' => 60000,
                        'tickets_remain' => 60000,
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/tickets/1'
                    ],
                    'relationships' => [
                        'event' => [
                            'data' => [
                                'type' => 'events',
                                'id' => $this->event->id
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_update_ticket_when_we_dont_provided_ticket_translation_data_return_Http_code_200()
    {
        // Arrange
        $data = [
            'attributes' => [
                'event_id' => $this->event->id,
                'ticket_translation_data' => [
                    'description' => 'Cette zone vous le trouver a l\'étage.',
                    'locale' => 'fr',
                    'location' => 'Zone B',
                    'type' => 'Payant',
                ]
            ]
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('tickets.update', $this->ticket->id), $data);

        // Assert
        $response->assertOk()
            ->assertJson([
                'data' => [
                    'type' => 'tickets',
                    'id' => 1,
                    'attributes' => [
                        'price' => 2000,
                        'ticket_translations' => [
                            [
                              'description' => 'This area you find it upstairs.',
                              'locale' => 'en',
                              'location' => 'Zone B',
                              'type' => 'Paying',
                            ],
                            [
                              'description' => 'Cette zone vous le trouver a l\'étage.',
                              'locale' => 'fr',
                              'location' => 'Zone B',
                              'type' => 'Payant',
                            ],
                        ],
                        'tickets_number' => 50000,
                        'tickets_remain' => 50000,
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/tickets/1'
                    ],
                    'relationships' => [
                        'event' => [
                            'data' => [
                                'type' => 'events',
                                'id' => $this->event->id
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_update_ticket_when_we_dont_provided_ticket_price_return_Http_code_200()
    {
        // Arrange
        $data = [
            'attributes' => [
                'event_id' => $this->event->id,
                'ticket' => [
                    'price' => 4000,
                ],
            ]
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('tickets.update', $this->ticket->id), $data);

        // Assert
        $response->assertOk()
            ->assertJson([
                'data' => [
                    'type' => 'tickets',
                    'id' => 1,
                    'attributes' => [
                        'price' => 4000,
                        'tickets_number' => 50000,
                        'tickets_remain' => 50000,
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/tickets/1'
                    ],
                    'relationships' => [
                        'event' => [
                            'data' => [
                                'type' => 'events',
                                'id' => $this->event->id
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_update_ticket_when_we_dont_provided_ticket_number_return_Http_code_200()
    {
        // Arrange
        $data = [
            'attributes' => [
                'event_id' => $this->event->id,
                'ticket' => [
                    'number' => 30000,
                ],
            ]
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('tickets.update', $this->ticket->id), $data);

        // Assert
        $response->assertOk()
            ->assertJson([
                'data' => [
                    'type' => 'tickets',
                    'id' => 1,
                    'attributes' => [
                        'price' => 2000,
                        'tickets_number' => 30000,
                        'tickets_remain' => 30000,
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/tickets/1'
                    ],
                    'relationships' => [
                        'event' => [
                            'data' => [
                                'type' => 'events',
                                'id' => $this->event->id
                            ]
                        ]
                    ]
                ]
            ]);
    }
}
