<?php

namespace Tests;

use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected User $user;
    protected Country $country;
    protected State $state;

    protected function createAdmin(): User
    {
        return User::factory()->create();
    }

    protected function createCountry(): Country
    {
        return Country::factory()->create();
    }

    protected function createState(): State
    {
        return State::factory()->create([
            'country_id' => $this->country->id
        ]);
    }

    protected function createCity()
    {
        // return City::factory()->create([
        //     'state_id' => $this->country->id
        // ]);
    }
}
