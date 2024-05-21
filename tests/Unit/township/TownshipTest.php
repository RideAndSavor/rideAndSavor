<?php

namespace Tests\Unit\township;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\UnitTestCase;

class TownshipTest extends UnitTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createAdmin();
        $this->country = $this->createCountry();
        $this->state = $this->createState();
        $this->city = $this->createCity();
        $this->township = $this->createTownship();
    }

    public function test_unauthenticated_user_cannot_access_township_page()
    {
        $response = $this->getJson(route('township.index'))->assertStatus(401);
    }

    public function test_api_township_invalid_validation_error(): void
    {
        $township = [
            'city_id' => '',
            'name' => ''
        ];
        $response = $this->actingAs($this->user)->postJson(route('township.store'), $township)
            ->assertStatus(422);
        $response->assertJsonValidationErrors(['city_id', 'name']);
    }

    public function test_api_return_all_townships_list(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('township.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);
        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->township->name, $response->json()['data'][0]['name']);
    }

    public function test_api_township_store_successful(): void
    {
        $township = [
            'city_id' => $this->city->id,
            'name' => $this->township->name
        ];
        $response = $this->actingAs($this->user)->postJson(route('township.store'), $township);
        $response->assertStatus(201);
        $response->assertExactJson($response->json());
        $this->assertDatabaseHas('townships', $township);
        $this->assertEquals($this->township->name, $response->json()['data']['name']);
    }

    public function test_api_township_update_successful():void{
        $township = [
            'city_id' => $this->city->id,
            'name' => 'Update township',
        ];

        $response = $this->actingAs($this->user)->putJson(route('township.update',$this->township),$township)
        ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);

        $this->assertEquals($township['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('townships', $township);
    }

    public function test_api_township_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('township.destroy', $this->township->id))
            ->assertNoContent();
        $this->assertDatabaseMissing('townships', [$this->township->id, $this->township->name]);
    }
}
