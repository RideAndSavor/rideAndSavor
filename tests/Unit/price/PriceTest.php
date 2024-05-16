<?php

namespace Tests\Unit\price;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\UnitTestCase;

class PriceTest extends UnitTestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->price = $this->createPrice();
    }

    public function test_unauthenticated_user_cannot_access_price_page()
    {
        $response = $this->getJson(route('price.index'))->assertStatus(401);
    }

    public function test_api_price_invalid_validation_returns_error(): void
    {
        $price = [
            'name' => '',
        ];
        $response = $this->actingAs($this->user)->postJson(route('price.store'), $price);
        $response->assertJsonValidationErrors(['name']);
        $response->assertStatus(422);
    }

    public function test_api_returns_all_prices_list(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('price.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);
        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->price->name, $response->json()['data'][0]['name']);
    }

    public function test_api_price_store_successful(): void
    {
        $price = [
            'name' => $this->price->name,
        ];
        $response = $this->actingAs($this->user)->postJson(route('price.store'), $price)
            ->assertStatus(201);

        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);
        $this->assertDatabaseHas('prices', $price);
        $this->assertEquals($price['name'], $response->json()['data']['name']);
    }

    public function test_api_price_update_successful(): void
    {
        $price = [
            'name' => '5000'
        ];
        $response = $this->actingAs($this->user)->putJson(
            route('price.update', $this->price->id),
            $price
        )->assertStatus(200);
        $response->assertExactJson($response->json());
        $this->assertDatabaseHas('prices', $price);
    }

    public function test_api_price_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('price.destroy', $this->price->id));
        $response->assertStatus(204);
        $this->assertDatabaseMissing('prices', [$this->price->id]);
    }
}
