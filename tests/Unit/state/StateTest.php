<?php

namespace Tests\Unit\state;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\UnitTestCase;

class StateTest extends UnitTestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->country = $this->createCountry();
        $this->state = $this->createState();
    }

    public function test_unauthenticated_user_cannot_access_state_page()
    {
        $response = $this->getJson(route('state.index'))->assertStatus(401);
    }

    public function test_api_state_invalid_validation_return_error(): void
    {
        $country = [
            'country_id' => '',
            'name' => '',
        ];
        $response = $this->actingAs($this->user)->postJson(route('state.store'), $country);
        $response->assertJsonValidationErrors(['country_id', 'name']);
        $response->assertStatus(422);
    }

    public function test_api_return_all_states_list(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('state.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);
        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->state->name, $response->json()['data'][0]['name']);
    }

    public function test_api_state_store_successful(): void
    {
        $state = [
            'name' => $this->state->name,
            'country_id' => $this->country->id,
        ];
        $response = $this->actingAs($this->user)->postJson(route('state.store'), $state)
            ->assertStatus(201);

        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);
        $this->assertDatabaseHas('states', $state);
        $this->assertEquals($state['name'], $response->json()['data']['name']);
    }

    public function test_api_state_update_successful(): void
    {
        $state = [
            'country_id' => $this->country->id,
            'name' => 'Update State'
        ];
        $response = $this->actingAs($this->user)->putJson(route('state.update', $this->state->id), $state)
            ->assertOk();
        $response->assertExactJson($response->json());
        $this->assertDatabaseHas('states', $state);
    }

    public function test_api_state_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('state.destroy', $this->state->id))
            ->assertNoContent();
        $this->assertDatabaseMissing('states', [$this->state->id, $this->state->name]);
    }
}
