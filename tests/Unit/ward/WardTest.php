<?php

namespace Tests\Unit\ward;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\UnitTestCase;

class WardTest extends UnitTestCase
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

    public function test_unauthenticated_user_cannot_access_ward_page()
    {
        $response = $this->getJson(route('ward.index'))->assertStatus(401);
    }

    public function test_api_ward_invalid_validation_return_error()
    {
        $ward = [
            'township_id' => '',
            'name' => '',
        ];
        $response = $this->actingAs($this->user)->postJson(route('ward.store'), $ward);
        $response->assertJsonValidationErrors(['township_id', 'name']);
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
            ->assertCreated();
        $response->assertExactJson($response->json());
        $response->assertSee($this->ward->name, $response->json()['data']['name']);

        $this->assertEquals($ward['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('wards', $ward);
    }

    public function test_api_ward_update_successful()
    {
        $ward = [
            'township_id' => $this->township->id,
            'name' => 'Update Ward'
        ];
        $response = $this->actingAs($this->user)->putJson(route('ward.update', $this->ward->id), $ward)
            ->assertOk();
        $response->assertExactJson($response->json());
        $this->assertDatabaseHas('wards', $ward);
        $this->assertEquals($ward['name'], $response->json()['data']['name']);
    }

    public function test_api_ward_delete_successful()
    {
        $response = $this->actingAs($this->user)->deleteJson(route('ward.destroy', $this->ward->id))
            ->assertNoContent();
        $this->assertDatabaseMissing('wards', [$this->ward->id, $this->ward->name]);
    }
}
