<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WardTest extends TestCase
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
        $this->ward = $this->createWard();
    }

    public function test_api_return_all_wards()
    {
        $response = $this->actingAs($this->user)->getJson(route('ward.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);

        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->ward->name, $response->json()['data'][0]['name']);
    }

    public function test_api_ward_store_successful()
    {
        $ward = [
            'township_id' => $this->township->id,
            'name' => $this->ward->name,
        ];
        $response = $this->actingAs($this->user)->postJson(route('ward.store'), $ward)
            ;
        $response->assertExactJson($response->json());
        dd($response->json());
        $response->assertSee($this->ward->name, $response->json()['data']['name']);

        $this->assertEquals($ward, $response->json()['data']);
        $this->assertDatabaseHas('wards', $ward);
    }

    public function test_api_ward_update_successful()
    {
        $ward = [
            'township_id' => $this->township->id,
            'name' => 'Update Ward'
        ];
        $response = $this->actingAs($this->user)->putJson(route('ward.update', $this->ward->id), $ward)
            ;
        $response->assertExactJson($response->json());
        $this->assertDatabaseHas('wards', $ward);
        $this->assertEquals($ward, $response->json()['data']);
    }
}
