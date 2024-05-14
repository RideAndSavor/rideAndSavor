<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->category = $this->createCategory();
        $this->subCategory = $this->createSubCategory();
    }

    public function test_unauthenticated_user_cannot_access_sub_categories_page()
    {
        $response = $this->get('/subcategory');
        $response->assertRedirect(route('login'));
    }

    public function test_api_subCategory_invalid_validation_errors(): void
    {
        $subCategory = [
            'name' => '',
            'category_id' => ''
        ];
        $response = $this->actingAs($this->user)->postJson(route('subcategory.store'), $subCategory)
            ->assertStatus(422);
        $response->assertJsonValidationErrors(['name','category_id']);
    }

    public function test_api_all_subCategories(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('subcategory.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);

        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->subCategory->name, $response->json()['data'][0]['name']);
    }

    public function test_api_subCategory_store_successful(): void
    {
        $subCategory = [
            'name' => $this->subCategory->name
        ];
        $response = $this->actingAs($this->user)->postJson(route('subcategory.store'), $subCategory)
            ->assertCreated();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']);

        $this->assertEquals($subCategory['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('sub_categories', $subCategory);
    }

    public function test_api_subCategory_update_successful(): void
    {
        $subCategory = [
            'name' => 'Update SubCategory'
        ];
        $response = $this->actingAs($this->user)->putJson(route('subcategory.update', $this->subCategory->id), $subCategory)
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);

        $this->assertEquals($subCategory['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('sub_categories', $subCategory);
    }

    public function test_api_subCategory_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('subcategory.destroy', $this->subCategory->id))
            ->assertNoContent();
        $this->assertDatabaseMissing('sub_categories', [$this->subCategory->id]);
    }
}
