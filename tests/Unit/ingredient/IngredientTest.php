<?php

namespace Tests\Unit\ingredient;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngredientTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->ingredient = $this->createIngredient();
    }

    public function test_unauthenticated_user_cannot_access_ingredient_page()
    {
        $response = $this->getJson(route('ingredients.index'))->assertStatus(401);
    }

    public function test_api_ingredient_invalid_validation_errors(): void
    {
        $ingredient = [
            'name' => '',
        ];
        $response = $this->actingAs($this->user)->postJson(route('ingredients.store'), $ingredient)
            ->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_api_all_ingredients(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('ingredients.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);

        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->ingredient->name, $response->json()['data'][0]['name']);
    }

    public function test_api_ingredient_store_successful(): void
    {
        $ingredient = [
            'name' => $this->ingredient->name,
        ];

        $response = $this->actingAs($this->user)->postJson(route('ingredients.store'), $ingredient)
            ->assertCreated();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']);

        $this->assertEquals($ingredient['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('ingredients', $ingredient);
    }

    public function test_api_ingredient_update_successful(): void
    {
        $ingredient = [
            'name' => 'Update ingredient',
        ];
        $response = $this->actingAs($this->user)->putJson(route('ingredients.update', $this->ingredient->id), $ingredient)
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);

        $this->assertEquals($ingredient['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('ingredients', $ingredient);
    }

    public function test_api_ingredient_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('ingredients.destroy', $this->ingredient->id))
            ->assertNoContent();
        $this->assertDatabaseMissing('ingredients', [$this->ingredient->id]);
    }
}
