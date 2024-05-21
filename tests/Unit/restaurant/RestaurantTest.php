<?php

namespace Tests\Unit\restaurant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\UnitTestCase;

class RestaurantTest extends UnitTestCase
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
        $this->address = $this->createAddress();
        $this->restaurant = $this->createRestaurant();
    }

    public function test_unauthenticated_user_cannot_access_restaurants_page()
    {
        $response = $this->getJson(route('restaurant.index'))->assertStatus(401);
    }

    public function test_api_restaurant_invalid_validation_errors(): void
    {
        $restaurant = [
            'address_id' => '',
            'name' => '',
            'open_time' => '',
            'close_time' => '',
            'phone_number' => '',
        ];
        $response = $this->actingAs($this->user)->postJson(route('restaurant.store'), $restaurant)
            ->assertStatus(422);
        $response->assertJsonValidationErrors(['address_id', 'name', 'open_time', 'close_time', 'phone_number']);
    }

    public function test_api_all_restaurants(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('restaurant.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);

        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->restaurant->name, $response->json()['data'][0]['name']);
    }

    public function test_api_restaurant_store_successful(): void
    {
        $restaurant = [
            'address_id' => $this->address->id,
            'name' => $this->restaurant->name,
            'open_time' => $this->restaurant->open_time,
            'close_time' => $this->restaurant->close_time,
            'phone_number' => $this->restaurant->phone_number,
        ];

        $response = $this->actingAs($this->user)->postJson(route('restaurant.store'), $restaurant)
            ->assertCreated();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']);

        $this->assertEquals($restaurant['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('restaurants', $restaurant);
    }

    public function test_api_restaurant_update_successful(): void
    {
        $restaurant = [
            'address_id' => $this->address->id,
            'name' => 'Update Restaurant',
            'open_time' => $this->restaurant->open_time,
            'close_time' => $this->restaurant->close_time,
            'phone_number' => $this->restaurant->phone_number,
        ];
        $response = $this->actingAs($this->user)->putJson(route('restaurant.update', $this->restaurant->id), $restaurant)
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);

        $this->assertEquals($restaurant['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('restaurants', $restaurant);
    }

    public function test_api_restaurant_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('restaurnat.destroy', $this->restaurant->id))
            ->assertNoContent();
        $this->assertDatabaseMissing('restaurants', [$this->restaurant->id]);
    }
}
