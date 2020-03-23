<?php

namespace Tests\Feature;

use App\Address;
use App\Event;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();

        $this->event = factory(Event::class)->create();
    }

    // Store

    public function test_can_store_address_when_user_is_not_connected_return_Http_code_401()
    {
        // Arrange
        $data = factory(Address::class)->make()->toArray();

        // Action
        $response = $this->json('POST', route('addresses.store'), $data);

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_store_address_when_user_is_connected_address_is_not_in_france_return_Http_code_201()
    {
        // Arrange
        $data = [
            'address' => [
                'additionel_information' => 'Suite 063',
                'city' => 'Lake Vedaland',
                'country' => 'Benin',
                'postal_code' => '29294',
                'street_address' => '15654 Caitlyn Mall',
                'venue' => 'Maggio LLC'
            ],
            'event_id' => $this->event->id
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('addresses.store'), $data);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'addresses',
                    'id' => 1,
                    'attributes' => [
                        'additionel_information' => 'Suite 063',
                        'city' => 'Lake Vedaland',
                        'country' => 'Benin',
                        'full_address' => '15654 Caitlyn Mall, 29294 Lake Vedaland',
                        'postal_code' => '29294',
                        'street_address' => '15654 Caitlyn Mall',
                        'venue' => 'Maggio LLC'
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/addresses/1'
                    ],
                    'relationships' => [
                        'events' => [
                            'data' => [
                                [
                                    'type' => 'events',
                                    'id' => $this->event->id
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_store_address_when_address_exists_in_france_return_Http_code_201()
    {
        // Arrange
        $data = [
            'address' => [
                'additionel_information' => 'Hall C',
                'city' => 'Paris',
                'country' => 'France',
                'postal_code' => '75013',
                'street_address' => '52 rue de tolbiac',
                'venue' => 'Maggio LLC'
            ],
            'event_id' => $this->event->id
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('addresses.store'), $data);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'addresses',
                    'id' => 1,
                    'attributes' => [
                        'additionel_information' => 'Hall C',
                        'city' => 'Paris',
                        'country' => 'France',
                        'full_address' => '52 rue de tolbiac, 75013 Paris',
                        'postal_code' => '75013',
                        'street_address' => '52 rue de tolbiac',
                        'venue' => 'Maggio LLC'
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/addresses/1'
                    ],
                    'relationships' => [
                        'events' => [
                            'data' => [
                                [
                                    'type' => 'events',
                                    'id' => $this->event->id
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_store_address_when_address_is_not_exists_in_france_return_Http_code_500()
    {
        // Arrange
        $data = [
            'address' => [
                'additionel_information' => 'Hall C',
                'city' => 'Paris',
                'country' => 'France',
                'postal_code' => '75012',
                'street_address' => '1000 rue tolbiac',
                'venue' => 'Maggio LLC'
            ],
            'event_id' => $this->event->id
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('addresses.store'), $data);

        // Assert
        $response->assertStatus(500)
            ->assertSeeText('The address does not exist in France.');
    }

    public function test_can_store_address_without_giving_data_return_Http_code_422()
    {
        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('addresses.store'));

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_store_address_when_event_already_has_a_relationship_with_another_address_return_Http_code_422()
    {
        // Arrange
        $address = factory(Address::class)->create();

        $this->event->address()->associate($address)->save();

        $data = [
            'address' => [
                'additionel_information' => 'Suite 063',
                'city' => 'Lake Vedaland',
                'country' => 'Benin',
                'postal_code' => '29294',
                'street_address' => '15654 Caitlyn Mall',
                'venue' => 'Maggio LLC'
            ],
            'event_id' => $this->event->id
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('POST', route('addresses.store'), $data);

        // Assert
        $response->assertStatus(422)
            ->assertSeeText('This event already has a relationship with another address.');
    }

    // Update

    public function test_can_update_address_when_user_is_not_connected_return_Http_code_401()
    {
        // Arrange
        $address = factory(Address::class)->create();
        $data = factory(Address::class)->make()->toArray();

        // Action
        $response = $this->json('PUT', route('addresses.update', $address->id), $data);

        // Assert
        $response->assertStatus(401);
    }

    public function test_can_update_address_when_we_do_not_provide_the_id_parameters_to_the_url_return_exeption()
    {
        // Exception
        $this->expectException(UrlGenerationException::class);

        // Arrange
        $data = factory(Address::class)->make()->toArray();

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('addresses.update'), $data);
    }

    public function test_can_update_address_when_address_id_and_event_id_not_provided_return_Http_code_422()
    {
        // Arrange
        $address = factory(Address::class)->create();

        $this->event->address()->associate($address)->save();

        $data = [
            'address' => factory(Address::class)
                ->make()
                ->makeHidden(['full_address'])
                ->toArray(),
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('addresses.update', $address->id), $data);

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_update_address_when_address_is_not_in_relationship_with_event_return_Http_code_422()
    {
        // Arrange
        $address = factory(Address::class)->create();
        $anotherAddress = factory(Address::class)->create();

        $this->event->address()->associate($address)->save();

        $data = [
            'address' => factory(Address::class)
                ->make()
                ->makeHidden(['full_address'])
                ->toArray(),
            'address_id' => $anotherAddress->id,
            'event_id' => $this->event->id,
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('addresses.update', $address->id), $data);

        // Assert
        $response->assertStatus(422)
            ->assertSeeText('The address is not related to the event provided.');
    }

    public function test_can_update_address_when_address_id_and_the_parameter_provided_in_the_url_are_not_the_same_return_Http_code_500()
    {
        // Arrange
        $address = factory(Address::class)->create();
        $anotherAddress = factory(Address::class)->create();

        $this->event->address()->associate($address)->save();

        $data = [
            'address' => factory(Address::class)
                ->make()
                ->makeHidden(['full_address'])
                ->toArray(),
            'address_id' => $address->id,
            'event_id' => $this->event->id
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('addresses.update', $anotherAddress->id), $data);

        // Assert
        $response->assertStatus(500)
            ->assertSeeText('The address identifier passed in the request parameter does not match with address to retrieve.');
    }

    public function test_can_update_address_when_we_provided_all_attributes_return_Http_code_200()
    {
        // Arrange
        $address = factory(Address::class)->create();

        $this->event->address()->associate($address)->save();

        $data = [
            'address' => [
                'additionel_information' => 'Suite 063',
                'city' => 'Lake Vedaland',
                'country' => 'Benin',
                'postal_code' => '29294',
                'street_address' => '15654 Caitlyn Mall',
                'venue' => 'Maggio LLC'
            ],
            'address_id' => $address->id,
            'event_id' => $this->event->id
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('addresses.update', $address->id), $data);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'addresses',
                    'id' => 1,
                    'attributes' => [
                        'additionel_information' => 'Suite 063',
                        'city' => 'Lake Vedaland',
                        'country' => 'Benin',
                        'full_address' => '15654 Caitlyn Mall, 29294 Lake Vedaland',
                        'postal_code' => '29294',
                        'street_address' => '15654 Caitlyn Mall',
                        'venue' => 'Maggio LLC'
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/addresses/1'
                    ],
                    'relationships' => [
                        'events' => [
                            'data' => [
                                [
                                    'type' => 'events',
                                    'id' => $this->event->id
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_update_address_when_address_exists_in_france_return_Http_code_200()
    {
        // Arrange
        $address = factory(Address::class)->create();

        $this->event->address()->associate($address)->save();

        $data = [
            'address' => [
                'additionel_information' => 'Hall C',
                'city' => 'Paris',
                'country' => 'France',
                'postal_code' => '75013',
                'street_address' => '52 rue de tolbiac',
                'venue' => 'Maggio LLC'
            ],
            'address_id' => $address->id,
            'event_id' => $this->event->id
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('addresses.update', $address->id), $data);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'addresses',
                    'id' => 1,
                    'attributes' => [
                        'additionel_information' => 'Hall C',
                        'city' => 'Paris',
                        'country' => 'France',
                        'full_address' => '52 rue de tolbiac, 75013 Paris',
                        'postal_code' => '75013',
                        'street_address' => '52 rue de tolbiac',
                        'venue' => 'Maggio LLC'
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/addresses/1'
                    ],
                    'relationships' => [
                        'events' => [
                            'data' => [
                                [
                                    'type' => 'events',
                                    'id' => $this->event->id
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_update_address_when_address_is_not_exists_in_france_return_Http_code_500()
    {
        // Arrange
        $address = factory(Address::class)->create();

        $this->event->address()->associate($address)->save();

        $data = [
            'address' => [
                'additionel_information' => 'Hall C',
                'city' => 'Paris',
                'country' => 'France',
                'postal_code' => '75011',
                'street_address' => '1000 rue tolbiac',
                'venue' => 'Maggio LLC'
            ],
            'address_id' => $address->id,
            'event_id' => $this->event->id
        ];

        // Action
        $response = $this->actingAs($this->user, 'api')->json('PUT', route('addresses.update', $address->id), $data);

        // Assert
        $response->assertStatus(500)
            ->assertSeeText('The address does not exist in France.');
    }
}
