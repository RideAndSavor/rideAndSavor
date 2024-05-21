<?php

namespace Tests\Unit\country;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\UnitTestCase;

class CountryTest extends UnitTestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createOwner();
        $this->country = $this->createCountry();
    }

    public function test_unauthenticated_user_cannot_access_country_page()
    {
        $response = $this->getJson(route('country.index'))->assertStatus(401);
    }

    public function test_api_country_invalid_validation_returns_error(): void
    {
        $country = [
            'name' => '',
        ];
        $response = $this->actingAs($this->user)->postJson(route('country.store'), $country);
        $response->assertJsonValidationErrors(['name']);
        $response->assertStatus(422);
    }

    public function test_api_returns_all_countries_list(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('country.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);
        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->country->name, $response->json()['data'][0]['name']);
    }

    public function test_api_country_store_successful(): void
    {
        $country = [
            'name' => '::name::',
        ];
        $response = $this->actingAs($this->user)->postJson(route('country.store'), $country)
            ->assertStatus(201);

        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);
        $this->assertDatabaseHas('countries', $country);
        $this->assertEquals($country['name'], $response->json()['data']['name']);
    }

    public function test_api_country_update_successful(): void
    {
        $response = $this->actingAs($this->user)->putJson(
            route('country.update', $this->country->id),
            ['name' => '::Burmese::']
        )->assertStatus(200);
        $response->assertExactJson($response->json());
        $this->assertDatabaseHas(
            'countries',
            ['name' => '::Burmese::']
        );
    }

    public function test_api_country_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('country.destroy', $this->country->id));
        $response->assertStatus(204);
        $this->assertDatabaseMissing('countries', ['id' => $this->country->id]);
    }
}
