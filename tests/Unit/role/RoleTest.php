<?php

namespace Tests\Unit\role;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\UnitTestCase;

class RoleTest extends UnitTestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->role = $this->createRole();
    }

    public function test_unauthenticated_user_cannot_access_role_page()
    {
        $response = $this->getJson(route('role.index'))->assertStatus(401);
    }

    public function test_api_role_invalid_validation_returns_error(): void
    {
        $role = [
            'name' => '',
        ];
        $response = $this->actingAs($this->user)->postJson(route('role.store'), $role);
        $response->assertJsonValidationErrors(['name']);
        $response->assertStatus(422);
    }

    public function test_api_returns_all_roles_list(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('role.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);
        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->role->name, $response->json()['data'][0]['name']);
    }

    public function test_api_role_store_successful(): void
    {
        $role = [
            'name' => $this->role->name,
        ];
        $response = $this->actingAs($this->user)->postJson(route('role.store'), $role)
            ->assertStatus(201);

        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);
        $this->assertDatabaseHas('roles', $role);
        $this->assertEquals($role['name'], $response->json()['data']['name']);
    }

    public function test_api_role_update_successful(): void
    {
        $role = [
            'name' => 'Update Role',
        ];
        $response = $this->actingAs($this->user)->putJson(route('role.update', $this->role->id), $role)
            ->assertStatus(200);
        $response->assertExactJson($response->json());
        $this->assertDatabaseHas('roles', $role);
    }

    public function test_api_role_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('role.destroy', $this->role->id));
        $response->assertStatus(204);
        $this->assertDatabaseMissing('roles', [$this->role->id]);
    }
}
