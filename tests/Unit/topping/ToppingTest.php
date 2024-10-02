<?php

namespace Tests\Unit\topping;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\UnitTestCase;

class ToppingTest extends UnitTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->topping = $this->createTopping();
    }

    public function test_unauthenticated_user_cannot_access_topping_page()
    {
        $response = $this->getJson(route('toppings.index'))->assertStatus(401);
    }

    public function test_api_topping_invalid_validation_errors(): void
    {
        $topping = [
            'name' => '',
        ];
        $response = $this->actingAs($this->user)->postJson(route('toppings.store'), $topping)
            ->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_api_all_toppings(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('toppings.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);

        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->topping->name, $response->json()['data'][0]['name']);
    }

    public function test_api_topping_store_successful(): void
    {
        $topping = [
            'name' => $this->topping->name,
        ];

        $response = $this->actingAs($this->user)->postJson(route('toppings.store'), $topping)
            ->assertCreated();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']);

        $this->assertEquals($topping['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('toppings', $topping);
    }

    public function test_api_topping_update_successful(): void
    {
        $topping = [
            'name' => 'Update topping',
        ];
        $response = $this->actingAs($this->user)->putJson(route('toppings.update', $this->topping->id), $topping)
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);

        $this->assertEquals($topping['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('toppings', $topping);
    }

    public function test_api_topping_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('toppings.destroy', $this->topping->id))
            ->assertNoContent();
        $this->assertDatabaseMissing('toppings', [$this->topping->id]);
    }
}
