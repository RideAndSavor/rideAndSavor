<?php

namespace Tests\Unit\user;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\UnitTestCase;

class UserTest extends UnitTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
    }

    public function test_api_register_invalid_validation_errors(): void
    {
        $user = [
            'name' => '',
            'email' => '',
            'password' => '',
            'phone_no' => '',
            'role' => '',
            'age' => '',
            'gender' => '',
        ];

        $response = $this->postJson(route('register'), $user)->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password', 'phone_no', 'role', 'gender', 'age']);
    }

    public function test_api_register_store_successful(): void
    {
        $user = [
            'name' => $this->user->name,
            'email' => "test123@gmail.com",
            'password' => $this->user->password,
            'phone_no' => $this->user->phone_no,
            'role' => "admin",
            'age' => $this->user->age,
            'gender' => $this->user->gender,
        ];
        $response = $this->postJson(route('register'), $user)->assertCreated();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['data']['name']);

        $this->assertEquals($user['name'], $response->json()['data']['name']);
        $this->assertDatabaseHas('users', ["email" => $user['email']]);
    }

    public function test_api_login_invalid_validation_errors():void{
        $user = [
            'email' => '',
            'password' => ''
        ];
        $response = $this->postJson(route('login'), $user)->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }
    
    public function test_api_login_successful(): void
    {
        $user = [
            'email' => $this->user->email,
            'password' => "password"
        ];
        $response = $this->postJson(route('login'), $user)->assertOk();
        $response->assertExactJson($response->json());
        $response->assertSee($response->json()['email']);

        $this->assertDatabaseHas('users', ['email' => $user['email']]);
    }
}
