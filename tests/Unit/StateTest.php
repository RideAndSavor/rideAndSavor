<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StateTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->country = $this->createCountry();
        $this->state = $this->createState();
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
}
