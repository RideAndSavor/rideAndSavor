<?php

use App\Models\DiscountItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StreetController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\StatusControlller;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\TownshipController;
use App\Http\Controllers\PercentageController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\TaxiDriverController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\DeliverPriceController;
use App\Http\Controllers\DiscountItemController;
use App\Http\Controllers\DeliveryPriceController;
use App\Http\Controllers\FoodRestaurantController;
use App\Http\Controllers\RestaurantFoodController;
use App\Http\Controllers\PaymentProviderController;
use App\Http\Controllers\RestaurantAddressController;
use App\Http\Controllers\CalculateDeliveryFeesController;
use App\Http\Controllers\ToppingController;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

Route::post('signup', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::resource('paymentmodes', PaymentProviderController::class);


// Route::middleware(['auth:sanctum','admin','shop_owner'])->group(function () {
Route::middleware(['auth:sanctum'])->group(function () {
    
    Route::post('/user/change-role-to-driver', [UserController::class, 'changeRoleToDriver']); 
    Route::post('/taxi-drivers/update-location', [TaxiDriverController::class, 'updateLocation'])
      ->middleware('throttle:60,1'); // Limit to 60 requests per minute
    Route::post('/taxi-drivers/nearby', [TaxiDriverController::class, 'getNearbyDrivers']);

    Route::resource('country', CountryController::class);
    Route::resource('state', StateController::class);
    Route::resource('city', CityController::class);
    Route::resource('township', TownshipController::class);
    Route::resource('ward', WardController::class);
    Route::resource('street', StreetController::class);
    Route::resource('address', AddressController::class);
    Route::resource('topping', ToppingController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('subcategory', SubCategoryController::class);
    Route::resource('foods', FoodController::class);
    Route::resource('salary', SalaryController::class);
    Route::resource('status', StatusControlller::class);
    Route::resource('role', RoleController::class);
    Route::resource('percentage', PercentageController::class);
    Route::resource('size', SizeController::class);
    Route::resource('price', PriceController::class);

    Route::get('restaurant_types', [RestaurantController::class, 'restaurantTypes']);
    Route::resource('foods', FoodController::class);
    Route::get('/popular-foods', [FoodController::class, 'getPopularFoods']);
    Route::get('/featureRestaurant', [RestaurantController::class, 'featureRestaurants']);

    Route::resource('salary', SalaryController::class);
    Route::resource('status', StatusControlller::class);
    Route::resource('restaurantaddress', RestaurantAddressController::class);
    Route::resource('discountItem', DiscountItemController::class);

    // Route::post('restaurants/{restaurant}/foods-with-ingredients', [RestaurantFoodController::class, 'storeFoodWithIngredients']);
    // Route::get('restaurants/{restaurant}/foods-with-ingredients_all', [RestaurantFoodController::class, 'showAllFoodIngredients']);
    // Route::get('restaurants/{restaurant}/foods-with-ingredients/{food}', [RestaurantFoodController::class, 'showFoodIngredient']);
    // Route::put('restaurants/{restaurant}/foods-with-ingredients/{food}', [RestaurantFoodController::class, 'updateFoodIngredient']);
    // Route::delete('restaurants/{restaurant}/foods-with-ingredients/{food}', [RestaurantFoodController::class, 'destroyFoodIngredient']);

    // Restaurant Info
    Route::resource('restaurant', RestaurantController::class);


    //Restaurant_Food
    Route::controller(RestaurantFoodController::class)->group(function () {
        Route::post('restaurants/{restaurant}/foods-with-toppings', 'storeFoodWithToppings');
        Route::get('restaurants/{restaurant}/foods-with-toppings_all', 'showAllFoodToppings');
        Route::get('restaurants/{restaurant}/foods-with-toppings/{food}', 'showFoodTopping');
        Route::put('restaurants/{restaurant}/foods-with-toppings/{food}', 'updateFoodTopping');
        Route::delete('restaurants/{restaurant}/foods-with-toppings/{food}', 'destroyFoodTopping');
    });

    Route::resource('order', OrderController::class);
    Route::resource('delivery_price', DeliveryPriceController::class);
    Route::resource('orderDetail', OrderDetailController::class);


    //Calculate_Delivery_Fees
    Route::post('/calculate-delivery-fee', [CalculateDeliveryFeesController::class, 'calculateDeliveryFee']);

    Route::get('/search', [SearchController::class, 'search']);

    Route::get('/discounted-foods', [DiscountController::class, 'getDiscountFoods']);

    //recent_order
   Route::get('users/{userId}/recent-orders',[OrderController::class,'getRecentOrder']);
});
