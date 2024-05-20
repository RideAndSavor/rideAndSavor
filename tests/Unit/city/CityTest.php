<?php

namespace Tests\Unit\city;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\UnitTestCase;

class CityTest extends UnitTestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->country = $this->createCountry();
        $this->state = $this->createState();
        $this->city = $this->createCity();
    }

    public function test_unauthenticated_user_cannot_access_cities_page()
    {
        $response = $this->getJson(route('city.index'))->assertStatus(401);
    }

    public function test_api_city_invalid_validation_returns_error(): void
    {
        $city = [
            'state_id' => '',
            'name' => '',
        ];
        $response = $this->actingAs($this->user)->postJson(route('city.store'), $city);
        $response->assertJsonValidationErrors(['state_id', 'name']);
        $response->assertStatus(422);
    }

    public function test_api_returns_all_cities_list(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('city.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);
        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->city->name, $response->json()['data'][0]['name']);
    }

    public function test_api_city_store_successful(): void
    {
        $city = [
            'state_id' => $this->state->id,
            'name' => $this->city->name,
        ];
        $response = $this->actingAs($this->user)->postJson(route('city.store'), $city)
            ->assertStatus(201);

        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);
        $this->assertDatabaseHas('cities', $city);
        $this->assertEquals($city['name'], $response->json()['data']['name']);
    }

    public function test_api_city_update_successful(): void
    {
        $city = [
            'state_id' => $this->state->id,
            'name' => 'Update City',
        ];
        $response = $this->actingAs($this->user)->putJson(route('city.update', $this->city->id), $city)
            ->assertOk();
        $response->assertExactJson($response->json());
        $this->assertDatabaseHas('cities', $city);
    }

    public function test_api_city_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('city.destroy', $this->city->id));
        $response->assertStatus(204);
        $this->assertDatabaseMissing('cities', [$this->city->id]);
    }
}
