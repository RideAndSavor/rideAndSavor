<?php

namespace Tests\Unit\category;

use Illuminate\Foundation\Testing\RefreshDatabase;
use SebastianBergmann\Type\VoidType;
use Tests\UnitTestCase;

class CategoryTest extends UnitTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->category = $this->createCategory();
    }

    public function test_unauthenticated_user_cannot_access_categories_page()
    {
        $response = $this->getJson(route('category.index'))->assertStatus(401);
    }

    public function test_api_category_invalid_validation_errors(): void
    {
        $category = [
            'name' => ''
        ];
        $response = $this->actingAs($this->user)->postJson(route('category.store'), $category)
            ->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_api_all_categories(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('category.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);

        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->category->name, $response->json()['data'][0]['name']);
    }

    public function test_api_category_store_successful(): void
    {
        $category = [
            'name' => $this->category->name
        ];
        $response = $this->actingAs($this->user)->postJson(route('category.store'), $category)
            ->assertCreated();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']);

        $this->assertEquals($category['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('categories', $category);
    }

    public function test_api_category_update_successful(): void
    {
        $category = [
            'name' => 'Update Category'
        ];
        $response = $this->actingAs($this->user)->putJson(route('category.update', $this->category->id), $category)
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);

        $this->assertEquals($category['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('categories', $category);
    }

    public function test_api_category_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('category.destroy', $this->category->id))
            ->assertNoContent();
        $this->assertDatabaseMissing('categories', [$this->category->id]);
    }
}
