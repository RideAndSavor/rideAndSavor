<?php

namespace Tests\Unit\size;

use Tests\UnitTestCase;

class SizeTest extends UnitTestCase
{
    // protected function setUp(): void
    // {
    //     parent::setUp();

    //     $this->user = $this->createAdmin();
    //     $this->size = $this->createSize();
    // }

    // public function test_unauthenticated_user_cannot_access_size_page()
    // {
    //     $response = $this->getJson(route('size.index'))->assertStatus(401);
    // }

    // public function test_api_size_invalid_validation_returns_error(): void
    // {
    //     $size = [
    //         'name' => '',
    //     ];
    //     $response = $this->actingAs($this->user)->postJson(route('size.store'), $size);
    //     $response->assertJsonValidationErrors(['name']);
    //     $response->assertStatus(422);
    // }

    // public function test_api_returns_all_sizes_list(): void
    // {
    //     $response = $this->actingAs($this->user)->getJson(route('size.index'))
    //         ->assertOk();
    //     $response->assertExactJson($response->json());
    //     $response->assertSee($response->json()['data'][0]['name']);
    //     $this->assertEquals(1, count($response->json()['data']));
    //     $this->assertEquals($this->size->name, $response->json()['data'][0]['name']);
    // }

    // public function test_api_size_store_successful(): void
    // {
    //     $size = [
    //         'name' => $this->size->name,
    //     ];
    //     $response = $this->actingAs($this->user)->postJson(route('size.store'), $size)
    //         ->assertStatus(201);

    //     $response->assertExactJson($response->json());
    //     $response->assertSee($response->json()['data']['name']);
    //     $this->assertDatabaseHas('sizes', $size);
    //     $this->assertEquals($size['name'], $response->json()['data']['name']);
    // }

    // public function test_api_size_update_successful(): void
    // {
    //     $size = [
    //         'name' => 'Update Size'
    //     ];
    //     $response = $this->actingAs($this->user)->putJson(
    //         route('size.update', $this->size->id),
    //         $size
    //     )->assertStatus(200);
    //     $response->assertExactJson($response->json());
    //     $this->assertDatabaseHas('sizes', $size);
    // }

    // public function test_api_size_delete_successful(): void
    // {
    //     $response = $this->actingAs($this->user)->deleteJson(route('size.destroy', $this->size->id));
    //     $response->assertStatus(204);
    //     $this->assertDatabaseMissing('sizes', [$this->size->id]);
    // }
}
