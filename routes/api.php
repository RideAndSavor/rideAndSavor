<?php

use App\Models\DiscountItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderItAgain;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WardController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\FavoriteCuisine;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\TasteController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StreetController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\TravelController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\FoodsInRestaurant;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StatusControlller;
use App\Http\Controllers\ToppingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\PopularRestaurants;
use App\Http\Controllers\TownshipController;

use App\Http\Controllers\CartItemsController;

use App\Http\Controllers\InventoryController;

use App\Http\Controllers\ElectronicController;
use App\Http\Controllers\NearbyTaxiController;
use App\Http\Controllers\PercentageController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\TaxiDriverController;
use App\Http\Controllers\CartSessionController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\AcceptDriverController;

use App\Http\Controllers\DeliverPriceController;
use App\Http\Controllers\DiscountItemController;
use App\Http\Controllers\ProductOrderController;
use App\Http\Controllers\DeliveryPriceController;
use App\Http\Controllers\FoodRestaurantController;
use App\Http\Controllers\RestaurantFoodController;
use App\Http\Controllers\PaymentProviderController;
use App\Http\Controllers\RestaurantAddressController;
use App\Http\Controllers\FeatureRestaurantsController;
use App\Http\Controllers\BiddingPriceByDriverController;
use App\Http\Controllers\CalculateDeliveryFeesController;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use App\Http\Controllers\InvoiceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

// Social Login
// Route::get('login/google', [SocialLoginController::class, 'redirectToGoogle']);
// Route::post('/social/login/callback-url', [SocialLoginController::class, 'handleCallback']);

Route::post('/request', [SocialLoginController::class, 'redirectToGoogle']);
Route::post('/social/login/callback-url', [SocialLoginController::class, 'handleGoogleCallback']);

Route::post('signup', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::get('/users/{id}', [AuthController::class, 'show']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::resource('paymentmodes', PaymentProviderController::class);

// Broadcasting routes
Broadcast::routes(['middleware' => ['auth:api']]);
Route::middleware(['auth:api'])->group(function () {



    // Route::post('/user/change-role-to-driver', [UserController::class, 'changeRoleToDriver']);
    Route::post('/taxi-drivers/update-location', [TaxiDriverController::class, 'updateLocation'])
        ->middleware('throttle:60,1'); // Limit to 60 requests per minute
    Route::post('/taxi-drivers/nearby', [TaxiDriverController::class, 'getNearbyDrivers']);

    Route::apiResource('travels', TravelController::class);
    Route::apiResource('biddings', BiddingPriceByDriverController::class);
    Route::apiResource('accept_drivers', AcceptDriverController::class);
    Route::get('/driver/{driverId}/notifications', [TaxiDriverController::class, 'getDriverNotifications']);

    Route::get('/driver/history/{driverId}', [AcceptDriverController::class, 'getDriverHistory']);
    Route::get('/driver/acceptedUserNoti/{driverId}', [AcceptDriverController::class, 'getNotiForDriver']);
    Route::post('/driver_accept_to_user', [AcceptDriverController::class, 'updateDriverStatus']);
    Route::get('/user/{travel_id}', [AcceptDriverController::class, 'showDriverComplete']);
    Route::post('/travel/{travelId}/complete', [AcceptDriverController::class, 'completeTravel']);


    //user get bidding prices
    Route::get('user/bidding-prices/{travel_id}', [BiddingPriceByDriverController::class, 'getBiddingPricesByTravelId']);

    /* pp */
    Route::resource('/taxi-drivers', TaxiDriverController::class);
    Route::get('/taxi-driver/user/{user_id}', [TaxiDriverController::class, 'getByUserId']);

    Route::resource('/brands', BrandController::class);
    Route::resource('/shops', ShopController::class);
    Route::post('/cart/add', [CartSessionController::class, 'addToCart']);
    Route::delete('/cart/remove/{id}', [CartSessionController::class, 'removeCartItem']);
    Route::post('/cart/update', [CartSessionController::class, 'updateCartItem']);
    Route::get('/cartview', [CartSessionController::class, 'getCartItems']);
    Route::get('user-orders', [ProductOrderController::class, 'getUserPendingOrders']);
    Route::post('/checkout', [ProductOrderController::class, 'checkout']);
    Route::post('/process-payment', [ProductOrderController::class, 'processPayment']);
    Route::post('/success-payment', [ProductOrderController::class, 'successPayment']);
    Route::get('/order-notifications', [ProductOrderController::class, 'getOrderNotifications']);
    Route::post('/order/{id}/confirm', [ProductOrderController::class, 'confirmOrder']);

    Route::get('/invoice/{orderId}', [InvoiceController::class, 'generateInvoiceForEachOrder']);
    Route::get('/invoices/monthly/{year}/{month}', [InvoiceController::class, 'generateMonthlyInvoices']);

    Route::post('/order/{id}/reject', [ProductOrderController::class, 'rejectOrder']);

    Route::get('/state/{country_id}', [StateController::class, 'getStatesByCountry']);
    Route::get('/city/{state_id}', [CityController::class, 'getCitiesByState']);
    Route::get('/township/{city_id}', [TownshipController::class, 'getTownshipsByCity']);
    Route::get('/ward/{township_id}', [WardController::class, 'getWardsByTownship']);
    Route::get('/street/{ward_id}', [StreetController::class, 'getStreetsByWard']);

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
    Route::resource('taste', TasteController::class);
    Route::resource('electronic', ElectronicController::class);
    Route::resource('products', ProductController::class);

    Route::get('restaurant_types', [RestaurantController::class, 'restaurantTypes']);
    Route::resource('foods', FoodController::class);
    Route::get('/popular-foods', [FoodController::class, 'getPopularFoods']);
    Route::get('/featureRestaurant', [RestaurantController::class, 'featureRestaurants']);

    Route::resource('salary', SalaryController::class);
    Route::resource('status', StatusControlller::class);
    Route::resource('restaurantaddress', RestaurantAddressController::class);
    Route::resource('discountItem', DiscountItemController::class);

    Route::post('/shop-discount', [DiscountController::class, 'storeForShop']);


    // Route::post('restaurants/{restaurant}/foods-with-ingredients', [RestaurantFoodController::class, 'storeFoodWithIngredients']);
    // Route::get('restaurants/{restaurant}/foods-with-ingredients_all', [RestaurantFoodController::class, 'showAllFoodIngredients']);
    // Route::get('restaurants/{restaurant}/foods-with-ingredients/{food}', [RestaurantFoodController::class, 'showFoodIngredient']);
    // Route::put('restaurants/{restaurant}/foods-with-ingredients/{food}', [RestaurantFoodController::class, 'updateFoodIngredient']);
    // Route::delete('restaurants/{restaurant}/foods-with-ingredients/{food}', [RestaurantFoodController::class, 'destroyFoodIngredient']);

    // Restaurant Info
    Route::resource('restaurant', RestaurantController::class);
    Route::resource('restaurant_food_topping', RestaurantFoodController::class);


    //Restaurant_Food
    Route::controller(RestaurantFoodController::class)->group(function () {
        Route::post('restaurants/{restaurant}/foods-with-toppings', 'storeFoodWithToppings');
        Route::get('restaurants/{restaurant}/foods-with-toppings_all', 'showAllFoodToppings');
        Route::get('restaurants/{restaurant}/foods-with-toppings/{food}', 'showFoodTopping');
        Route::put('restaurants/{restaurant}/foods-with-toppings/{food}', 'updateFoodTopping');
        Route::delete('restaurants/{restaurant}/foods-with-toppings/{food}', 'destroyFoodTopping');
    });

    Route::apiResource('inventory', InventoryController::class);
    Route::put('inventory/{id}/update-stock', [InventoryController::class, 'updateStock']);


    Route::apiResource('carts', CartController::class)->except(['show', 'destroy']);
    Route::apiResource('cart-items', CartItemsController::class)->only('destroy');


    Route::resource('orders', OrderController::class);
    Route::resource('delivery_price', DeliveryPriceController::class);
    Route::resource('orderDetail', OrderDetailController::class);

    Route::get('/order-it-again', OrderItAgain::class)->name('order-it-again');
    Route::get('/favorite-cuisine', FavoriteCuisine::class)->name('food.favorite.cuisine');
    Route::get('/popular-restaurants', PopularRestaurants::class)->name('restaurant.popular-restaurants');
    Route::get('/feature-restaurants', FeatureRestaurantsController::class)->name('restaurant.feature-restaurants');
    Route::get('/foods-in-restaurant', FoodsInRestaurant::class)->name('food.in-restaurant');


    //Calculate_Delivery_Fees
    Route::post('/calculate-delivery-fee', [CalculateDeliveryFeesController::class, 'calculateDeliveryFee']);

    Route::get('/search', [SearchController::class, 'search']);

    Route::get('/discounted-foods', [DiscountController::class, 'getDiscountFoods']);

    //recent_order
    Route::get('users/{userId}/recent-orders', [OrderController::class, 'getRecentOrder']);
});


//stripe
Route::middleware(['auth:api'])->group(function (){
    Route::get('/stripe/connect', [StripeController::class, 'redirectToStripe']);
    Route::get('/stripe/callback', [StripeController::class, 'handleStripeCallback'])->withoutMiddleware('auth:api');
    Route::get('shop/{shop_id}/stripe', [StripeController::class, 'getShopStripeAccount']);

});


//tzm
Route::post('/validate-users', [UserController::class, 'validateUsers']);
Route::middleware(['auth:api', 'admin'])->group(function () {

    Route::post('/user/change-user-role', [UserController::class, 'changeUserRole']);
});

Route::middleware(['auth:api', 'user'])->group(function () {

    Route::post('/user/rider_request_taxi', [TripController::class, 'RiderRequestTaxi']);

    Route::get('/user/trip_differentprice', [TripController::class, 'tripDifferentPrice']);

    Route::get('/user/driver_detail/{driver_id}', [TripController::class, 'driverDeatil']);
});


Route::middleware(['auth:api', 'driver'])->group(function () {

    Route::post('/driver/setting-price', [TripController::class, 'storeDriverSettingPrice']);
});






//end tzm
