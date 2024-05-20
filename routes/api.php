<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\WardController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\StreetController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\StatusControlller;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountItemController;
use App\Http\Controllers\TownshipController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\PercentageController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\PaymentProviderController;
use App\Http\Controllers\RestaurantAddressController;
use App\Models\DiscountItem;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::resource('paymentmodes', PaymentProviderController::class);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('country', CountryController::class);
    Route::resource('state', StateController::class);
    Route::resource('city', CityController::class);
    Route::resource('township', TownshipController::class);
    Route::resource('ward', WardController::class);
    Route::resource('street', StreetController::class);
    Route::resource('address', AddressController::class);
    Route::resource('restaurant', RestaurantController::class);
    Route::resource('ingredients', IngredientController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('subcategory', SubCategoryController::class);
    Route::resource('foods', FoodController::class);
    Route::resource('salary', SalaryController::class);
    Route::resource('status', StatusControlller::class);
    Route::resource('role', RoleController::class);
    Route::resource('percentage', PercentageController::class);
    Route::resource('size', SizeController::class);
    Route::resource('price', PriceController::class);

    Route::resource('restaurant', RestaurantController::class);
    Route::resource('foods', FoodController::class);
    Route::resource('salary', SalaryController::class);
    Route::resource('status', StatusControlller::class);
    Route::resource('restaurantaddress', RestaurantAddressController::class);
    Route::resource('discontItem', DiscountItemController::class);
});
