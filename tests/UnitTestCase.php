<?php

namespace Tests;

use App\Enums\RoleType;
use App\Models\Address;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Food;
use App\Models\Ingredient;
use App\Models\Price;
use App\Models\Restaurant;
use App\Models\Role;
use App\Models\Salary;
use App\Models\Size;
use App\Models\State;
use App\Models\Street;
use App\Models\SubCategory;
use App\Models\Township;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class UnitTestCase extends BaseTestCase
{
    use CreatesApplication, LazilyRefreshDatabase;

    protected User $user;
    protected Country $country;
    protected State $state;
    protected City $city;
    protected Township $township;
    protected Ward $ward;
    protected Street $street;
    protected Address $address;
    protected Category $category;
    protected SubCategory $subCategory;
    protected Restaurant $restaurant;
    protected Ingredient $ingredient;
    protected Food $food;
    protected Price $price;
    protected Role $role;
    protected Salary $salary;
    protected Size $size;

    protected function createAdmin(): User
    {
        return User::factory()->create([
            'role' => RoleType::Admin->value
        ]);
    }

    protected function createDriver(): User
    {
        return User::factory()->create([
            'role' => RoleType::Driver->value
        ]);
    }

    protected function createRider(): User
    {
        return User::factory()->create([
            'role' => RoleType::Rider->value
        ]);
    }

    protected function createOwner(): User
    {
        return User::factory()->create([
            'role' => RoleType::Owner->value
        ]);
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

    protected function createCity(): City
    {
        return City::factory()->create([
            'state_id' => $this->country->id
        ]);
    }

    protected function createTownship(): Township
    {
        return Township::factory()->create([
            'city_id' => $this->city->id
        ]);
    }

    protected function createWard(): Ward
    {
        return Ward::factory()->create([
            'township_id' => $this->township->id
        ]);
    }

    protected function creatStreet(): Street
    {
        return Street::factory()->create([
            'ward_id' => $this->ward->id
        ]);
    }

    protected function createAddress(): Address
    {
        return Address::factory()->create([
            'street_id' => $this->ward->id
        ]);
    }

    protected function createCategory(): Category
    {
        return Category::factory()->create();
    }

    protected function createSubCategory(): SubCategory
    {
        return SubCategory::factory()->create([
            'category_id' => $this->category->id
        ]);
    }

    protected function createRestaurant(): Restaurant
    {
        return Restaurant::factory()->create([
            'address_id' => $this->address->id
        ]);
    }

    protected function createIngredient(): Ingredient
    {
        return Ingredient::factory()->create();
    }

    protected function createFood(): Food
    {
        return Food::factory()->create([
            'sub_category_id' => $this->subCategory->id
        ]);
    }

    protected function createPrice(): Price
    {
        return Price::factory()->create();
    }

    protected function createRole(): Role
    {
        return Role::factory()->create();
    }

    protected function createSalary(): Salary
    {
        return Salary::factory()->create();
    }

    // protected function createSize(): Size
    // {
    //     return Size::factory()->create();
    // }
    
}