<?php

namespace Tests;

use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected User $user;
    protected Country $country;

    protected function createAdmin(): User
    {
        return User::factory()->create();
    }

    protected function createCountry(): Country
    {
        return Country::factory()->create();
    }
}
