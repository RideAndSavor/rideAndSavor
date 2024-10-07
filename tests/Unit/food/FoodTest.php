<?php

namespace Tests\Unit\food;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\UnitTestCase;

class FoodTest extends UnitTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->category = $this->createCategory();
        $this->subCategory = $this->createSubCategory();
        $this->food = $this->createFood();
    }

    public function test_unauthenticated_user_cannot_access_food_page()
    {
        $response = $this->getJson(route('foods.index'))->assertStatus(401);
    }

    public function test_api_food_invalid_validation_errors(): void
    {
        $food = [
            'name' => '',
            'sub_category_id' => ''
        ];
        $response = $this->actingAs($this->user)->postJson(route('foods.store'), $food)
            ->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'sub_category_id']);
    }

    public function test_api_all_foods(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('foods.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);

        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->food->name, $response->json()['data'][0]['name']);
    }

    public function test_api_food_store_successful(): void
    {
        $food = [
            'name' => $this->food->name,
            // 'quantity' => $this->food->quantity,
            'sub_category_id' => $this->subCategory->id
        ];

        $response = $this->actingAs($this->user)->postJson(route('foods.store'), $food)
            ->assertCreated();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']);

        $this->assertEquals($food['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('food', $food);
    }

    public function test_api_food_update_successful(): void
    {
        $food = [
            'name' => 'Update Food',
            // 'quantity' => $this->food->quantity,
            'sub_category_id' => $this->food->sub_category_id
        ];
        
        $response = $this->actingAs($this->user)->putJson(route('foods.update', $this->food->id), $food)
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);

        $this->assertEquals($food['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('food', $food);
    }

    public function test_api_food_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('foods.destroy', $this->food->id))
            ->assertNoContent();
        $this->assertDatabaseMissing('food', [$this->food->id]);
    }
}
