<?php

namespace Tests\Feature;

use App\Event;
use App\Ticket;
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
            'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
            'event_id' => $this->event->id,
            'price' => 2000,
            'tickets_number' => 50000,
            'tickets_remain' => 50000,
            'type' => 'Block C',
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

    public function test_can_show_ticket_when_user_is_connected_and_ticket_exists_return_Http_code_200()
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
                        'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                        'price' => 2000,
                        'tickets_number' => 50000,
                        'tickets_remain' => 50000,
                        'type' => 'Block C',
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
            'event_id' => $this->event->id,
            'ticket' => [
                'number' => 50000,
                'type' => 'Block B',
                'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                'price' => 2000,
            ],
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
            'event_id' => $this->event->id,
            'ticket' => [
                'number' => 50000,
                'type' => 'Block B',
                'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                'price' => 2000,
            ],
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
            'event_id' => $this->event->id,
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
            'ticket' => [
                'number' => 50000,
                'type' => 'Block B',
                'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                'price' => 2000,
            ],
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
            'event_id' => $this->event->id,
            'ticket' => [
                'number' => 50000,
                'type' => 'Block B',
                'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                'price' => 2000,
            ],
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
                        'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                        'price' => 2000,
                        'tickets_number' => 50000,
                        'tickets_remain' => 50000,
                        'type' => 'Block B',
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
            'event_id' => $this->event->id,
            'ticket' => [
                'number' => 50000,
                'type' => 'Block C',
                'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                'price' => 2000,
            ],
            'ticket_id' => $this->ticket->id
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
            'event_id' => $this->event->id,
            'ticket' => [
                'number' => 60000,
                'type' => 'Block U',
                'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                'price' => 3000,
            ],
            'ticket_id' => $this->ticket->id
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
            'event_id' => $this->event->id,
            'ticket' => [
                'number' => 50000,
                'type' => 'Block C',
                'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                'price' => 2000,
            ],
            'ticket_id' => $this->ticket->id
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
            'ticket' => [
                'number' => 60000,
                'type' => 'Block D',
                'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                'price' => 3000,
            ],
            'ticket_id' => $this->ticket->id
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('tickets.update', $this->ticket->id), $data);

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_update_ticket_when_we_dont_provided_ticket_id_return_Http_code_422()
    {
        // Arrange
        $data = [
            'event_id' => $this->event->id,
            'ticket' => [
                'number' => 60000,
                'type' => 'Block D',
                'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                'price' => 3000,
            ],
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
            'event_id' => $anotherEvent->id,
            'ticket' => [
                'number' => 60000,
                'type' => 'Block D',
                'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                'price' => 3000,
            ],
            'ticket_id' => $this->ticket->id
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('tickets.update', $this->ticket->id), $data);

        // Assert
        $response->assertStatus(422)
            ->assertSeeText('The ticket is not related to the event provided.');
    }

    public function test_can_update_ticket_when_we_dont_provided_ticket_id_return_Http_code_500()
    {
        // Arrange
        $anotherTicket = factory(Ticket::class)->create([]);

        $data = [
            'event_id' => $this->event->id,
            'ticket' => [
                'number' => 60000,
                'type' => 'Block D',
                'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                'price' => 3000,
            ],
            'ticket_id' => $this->ticket->id
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('tickets.update', $anotherTicket), $data);

        // Assert
        $response->assertStatus(500)
            ->assertSeeText('The ticket identifier passed in the request parameter does not match with ticket to retrieve.');
    }

    public function test_can_update_ticket_when_we_provided_all_params_return_Http_code_200()
    {
        // Arrange
        $data = [
            'event_id' => $this->event->id,
            'ticket' => [
                'number' => 60000,
                'type' => 'Block U',
                'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                'price' => 3000,
            ],
            'ticket_id' => $this->ticket->id
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
                        'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                        'price' => 3000,
                        'tickets_number' => 60000,
                        'tickets_remain' => 60000,
                        'type' => 'Block U',
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

    public function test_can_update_ticket_when_we_dont_provided_all_ticket_data_return_Http_code_200()
    {
        // Arrange
        $data = [
            'event_id' => $this->event->id,
            'ticket' => [
                'price' => 4000,
            ],
            'ticket_id' => $this->ticket->id
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
                        'description' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression.',
                        'price' => 4000,
                        'tickets_number' => 50000,
                        'tickets_remain' => 50000,
                        'type' => 'Block C',
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
