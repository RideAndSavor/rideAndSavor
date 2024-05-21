<?php

namespace Tests\Unit\salary;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\UnitTestCase;

class SalaryTest extends UnitTestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->salary = $this->createSalary();
    }

    public function test_unauthenticated_user_cannot_access_salary_page()
    {
        $response = $this->getJson(route('salary.index'))->assertStatus(401);
    }

    public function test_api_salary_invalid_validation_returns_error(): void
    {
        $salary = [
            'name' => '',
        ];
        $response = $this->actingAs($this->user)->postJson(route('salary.store'), $salary);
        $response->assertJsonValidationErrors(['name']);
        $response->assertStatus(422);
    }

    public function test_api_returns_all_salaries_list(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('salary.index'))
            ->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data'][0]['name']);
        $this->assertEquals(1, count($response->json()['data']));
        $this->assertEquals($this->salary->name, $response->json()['data'][0]['name']);
    }

    public function test_api_salary_store_successful(): void
    {
        $salary = [
            'name' => $this->salary->name,
        ];
        $response = $this->actingAs($this->user)->postJson(route('salary.store'), $salary)
            ->assertStatus(201);

        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);
        $this->assertDatabaseHas('salaries', $salary);
        $this->assertEquals($salary['name'], $response->json()['data']['name']);
    }

    public function test_api_salary_update_successful(): void
    {
        $salary = [
            'name' => '8000',
        ];
        $response = $this->actingAs($this->user)->putJson(route('salary.update', $this->salary->id), $salary)
            ->assertStatus(200);
        $response->assertExactJson($response->json());
        $this->assertDatabaseHas('salaries', $salary);
    }

    public function test_api_salary_delete_successful(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('salary.destroy', $this->salary->id));
        $response->assertStatus(204);
        $this->assertDatabaseMissing('salaries', [$this->salary->id]);
    }
}
