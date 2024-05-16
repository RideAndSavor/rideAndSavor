<?php

namespace Tests\Unit\street;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\UnitTestCase;

class StreetTest extends UnitTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->country = $this->createCountry();
        $this->state = $this->createState();
        $this->city = $this->createCity();
        $this->township = $this->createTownship();
        $this->ward = $this->createWard();
        $this->street = $this->creatStreet();
    }

    public function test_unauthenticated_user_cannot_access_street_page()
    {
        $response = $this->getJson(route('street.index'))->assertStatus(401);
    }

    public function test_api_street_invalid_validation_return_error(): void
    {
        $street = [
            'ward_id' => '',
            'name' => '',
        ];
        $response = $this->actingAs($this->user)->postJson(route('street.store'), $street)
            ->assertStatus(422);
        $response->assertJsonValidationErrors(['ward_id', 'name']);
    }

    public function test_api_return_all_streets(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('street.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);

        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->street->name, $response->json()['data'][0]['name']);
    }

    public function test_api_street_store_successful(): void
    {
        $street = [
            'ward_id' => $this->ward->id,
            'name' => $this->ward->name,
        ];
        $response = $this->actingAs($this->user)->postJson(route('street.store'), $street)
            ->assertCreated();
        $response->assertExactJson($response->json());
        $response->assertSee($street['name'], $response->json()['data']['name']);

        $this->assertEquals($street['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('streets', $street);
    }

    public function test_api_street_update_successful(): void
    {
        $street = [
            'ward_id' => $this->ward->id,
            'name' => 'Update Street',
        ];
        $response = $this->actingAs($this->user)->putJson(route('street.update', $this->street->id), $street)
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);

        $this->assertEquals($street['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('streets', $street);
    }

    public function test_api_street_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('street.destroy', $this->street->id))
            ->assertNoContent();
        $this->assertDatabaseMissing('streets', [$this->street->id]);
    }
}
