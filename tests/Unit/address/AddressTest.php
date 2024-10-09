<?php

namespace Tests\Unit\address;

use App\Enums\RoleType;
use Tests\UnitTestCase;
// use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressTest extends UnitTestCase
{
    // use RefreshDatabase;
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        // dd($this->user->role);
        $this->country = $this->createCountry();
        $this->state = $this->createState();
        $this->city = $this->createCity();
        $this->township = $this->createTownship();
        $this->ward = $this->createWard();
        $this->street = $this->creatStreet();
        $this->address = $this->createAddress();
    }

    public function test_unauthenticated_user_cannot_access_addresses_page()
    {
        $response = $this->getJson(route('address.index'))->assertStatus(401);
    }

    public function test_api_address_invalid_validation_errors(): void
    {
        $address = [
            'street_id' => '',
            'block_no' => '',
            'floor' => '',
        ];
        $response = $this->actingAs($this->user)->postJson(route('address.store'), $address)
            ->assertStatus(422);
        $response->assertJsonValidationErrors(['street_id', 'block_no', 'floor']);
    }

    public function test_api_return_all_addresses_successful(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('address.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['street_id']);

        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->address->street_id, $response->json()['data'][0]['street_id']);
    }

    public function test_api_address_store_successful(): void
    {
        $address = [
            'street_id' => $this->street->id,
            'block_no' => $this->address->block_no,
            'floor' => $this->address->floor,
            'latitude' => $this->address->latitude,
            'longitude' => $this->address->longitude,
        ];
        $response = $this->actingAs($this->user)->postJson(route('address.store'), $address)
            ->assertCreated();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['street_id']);

        $this->assertEquals($address['street_id'], $response->json()['data']['street_id']);
        $this->assertDatabaseHas('addresses', $address);
    }

    public function test_api_address_update_successful(): void
    {
        $address = [
            'street_id' => $this->street->id,
            'block_no' => 'update block',
            'floor' => $this->address->floor,
            'latitude' => $this->address->latitude,
            'longitude' => $this->address->longitude,
        ];
        $response = $this->actingAs($this->user)->putJson(route('address.update', $this->address->id), $address)
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['street_id']);

        $this->assertEquals($address['block_no'], $response->json()['data']['block_no']);
        $this->assertDatabaseHas('addresses', $address);
    }

    public function test_api_address_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('address.destroy', $this->address->id))
            ->assertNoContent();
        $this->assertDatabaseMissing('addresses', [$this->address->id]);
    }
}
