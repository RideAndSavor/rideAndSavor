<?php

return [
    "ONE" => 1, "TWO" => 2, "THREE" => 3, "FOUR" => 4, "FIVE" => 5, "SIX" => 6,
    "SEVEN" => 7, "EIGHT" => 8, "NINE" => 9, "TEN" => 10, "ELEVEN" => 11, "TWELVE" => 12,
    "THIRTEEN" => 13, "FOURTEEN" => 14, "FIFTEEN" => 15, "SIXTEEN" => 16, "SEVENTEEN" => 17,
    "EIGHTEEN" => 18, "NINETEEN" => 19, "TWENTY" => 20, "TWENTY_ONE" => 21, "TWENTY_TWO" => 22,
    "TWENTY_THREE" => 23, "TWENTY_FOUR" => 24, "TWENTY_FIVE" => 25, "TWENTY_SIX" => 26,
    "TWENTY_SEVEN" => 27, "TWENTY_EIGHT" => 28, "TWENTY_NINE" => 29, "THIRTY" => 30,
    "THIRTY_ONE" => 31, "THIRTY_TWO" => 32, "THIRTY_THREE" => 33, "THIRTY_FOUR" => 34,
    "THIRTY_FIVE" => 35, "USER" => "user", "ADMIN" => "admin", "OWNER" => "shop_owner",
    "RIDER" => "rider", "DRIVER" => "driver",

    "ADMIN_ROLE_NO" => 2,
    "USER_ROLE_NO" => 1,
    "DRIVER_ROLE_NO" => 5,
    "SHOP_OWNER_ROLE_NO" => 3,
    "RIDER_ROLE_NO" => 4,

    'NO_CONTENT' => '204',
    'OK' => '200',
    'CREATED' => '201',
    'CLIENT_ERROR' => '401',
    'SEVER_NOT_FOUND' => '404',
    'Unprocessable Entity' => '422',
    'INTERNAL_SEVER_ERROR' => '500',

    "YOU_DO_NOT_HAVE_ADMIN_ACCESS" => "You do not have admin access",
    "YOU_DO_NOT_HAVE_ACCESS" => "You do not have access",

    "IMAGE_MODEL" => "App\Models\Images",
    "IMAGES_TABLE" => "images",
    'FOOD_IMAGE' => 'food_image',

    'ISE' => 'Internal Server Error',

     // Inventory messages
     'INVENTORY_NOT_FOUND' => 'Inventory item not found.',
     'INVENTORY_DELETED_SUCCESSFULLY' => 'Inventory item deleted successfully.',
     'INVENTORY_CREATED_SUCCESSFULLY' => 'Inventory item created successfully.',
     'INVENTORY_UPDATED_SUCCESSFULLY' => 'Inventory item updated successfully.',
     'INVENTORY_UPDATED_STOCK' => 'Inventory stock updated successfully.',
     'INVENTORY_OUT_OF_STOCK' => 'The item is out of stock.',

    "TAXI_DRIVER_NOT_FOUND" => "Taxi Driver not found",
    "TAXI_DRIVER_DELETED_SUCCESSFULLY" => "Taxi Driver Deleted Successfully",
    "TAXI_DRIVER_UPDATED_SUCCESSFULLY" => "Taxi Driver Updated Successfully",

    "BRAND_NOT_FOUND" => "Brand not found",
    "BRAND_DELETED_SUCCESSFULLY" => "Brand Deleted Successfully",
    "BRAND_UPDATED_SUCCESSFULLY" => "Brand Updated Successfully",
    "BRAND_CREATED_SUCCESSFULLY" => "Brand Created Successfully",

    "SHOP_NOT_FOUND" => "Shop not found",
    "SHOP_DELETED_SUCCESSFULLY" => "Shop Deleted Successfully",
    "SHOP_UPDATED_SUCCESSFULLY" => "Shop Updated Successfully",
    "SHOP_CREATED_SUCCESSFULLY" => "Shop Created Successfully",

    'DINF' => 'Discount Item Not Found',
    'DIDSF' => 'Discount Item Deleted Successfully',

    "CNF" => 'Country Not Found',
    'FTDC' => "Failed to delete Country",
    'CDF' => "Country Deleted Successfully",

    "SNF" =>  'State Not Found',
    'FTDS' => 'Fail To Deleted State',
    'SDF' => "State Deleted Successfully",

    'CITY_NOT_FOUND' => "City Not Found",
    'FAIL_TO_DELETED_CITY' => 'Failed To Deleted City',
    'CITY_DELETED_SUCCESSFULLY' => "State Deleted Successfully",

    'TNF' => 'Township Not Found',
    'FTDT' => 'Failed To Deleted Township',
    'TDS' => "Township Deleted Successfully",

    'WARD_NOT_FOUND' => 'Ward Not Found',
    'FAIL_TO_DELETED_WARD' => 'Failed To Deleted Ward',
    'WARD_DELETED_SUCCESSFULLY' => 'Ward Deleted Successfully',

    "STREET_NOT_FOUND" => 'Street Not Found',
    'FAIL_TO_DELETED_STREET' => 'Falied To Deleted Street',
    'STREET_DELETED_SUCCESSFULLY' => 'Street Deleted Successfully',

    'USER_EMAIL_ALREADY_EXIT' => 'User Email is Already Exist',
    'INVALID_USERNAME_ADN_PASSWORD' => 'Invalid UserName And Password',
    'LOGIN_SUCCESSFULLY' => 'Login successfully',
    'NO_AUTHENTICATED_USER' => 'No authenticated user',
    'LOGGED_OUT_SUCCESSFULLY' => 'Logged out successfully',

    'FAILED_TO_CREATE_PAYMENT_PROVIDER' => 'Failed to create the payment provider',
    'PAYMENT_PROVIDER_NOT_FOUND' => 'Payment Provider not found',
    'PAYMENT_PROVIDER_DELETED_SUCCESSFULLY' => 'PaymentProvider deleted successfully',

    'FAILED_TO_CREATE_ADDRESS' => 'Failed to create the address',
    'ADDRESS_NOT_FOUND' => 'Address not found',
    'ADDRESS_DELETED_SUCCESSFULLY' => 'Address deleted successfully',
    'YOUR_STREET_CAN_NOT_CHANGE' => 'Your Street can not change you can create a new address',

    'TOPPINGS_NOT_FOUND' => 'Toppings Not Found',
    'FAIL_TO_DELETED_TOPPING' => 'Fail To Deleted Topping',
    'TOPPING_DELETED_SUCCESSFULLY' => 'Topping Deleted Successfully',

    'PNF' => 'Price Not Found',
    'FTDP' => "Failed to delete Price",
    'PDS' => "Price Deleted Successfully",

    'DPNF' => 'Delivery Price Not Found',
    'DPDS' => 'Delivery Price Deleted Successfully',

    'ONF' => 'Order Not Found',
    'ODS' => 'Order Deleted Successfully',

    'FRNF' => 'Food Restaurant Not Found',
    'FRDS' => 'Food Restaurant Deleted Successfully',

    'ODNF' => 'Order Detail Not Found',
    'ODDS' => 'Order Detail Deleted Successfully',

    "TASTENF" => 'Taste Not Found',
    "FDTASTE" => 'Failed to delete Taste',
    "TASTEDS" => 'Taste Delete Successfully',

    "SZNF" => 'Size Not Found',
    'FTDSZ' => "Failed to delete Size",
    'SZDS' => "Size Deleted Successfully",

    'CATEGORY_NOT_FOUND' => 'Category Not Found',
    'FAIL_TO_DELETED_CATEGORY' => "Fail To Deleted Category",
    'CATEGORY_DELETED_SUCCESSFULLY' => 'Category Deleted Successfully',

    'SUBCATEGORY_NOT_FOUND' => 'SubCategory Not Found',
    'FAIL_TO_DELETED_SUBCATEGORY' => 'Fail To Deleted SubCategory',
    'SUBCATEGORY_DELETED_SUCCESSFULLY' => 'SubCategory Deleted Successfully',

    'FOOD_NOT_FOUND' => 'Food Not Found',
    'FAIL_TO_DELTEDE_FOOD' => 'Fail To Deleted Food',
    'FOOD_DELETED_SUCCESSFULLY' => 'Food Deleted Successfully',

    'FAILED_TO_DELETED_SALARY' => 'Fail To Deleted Salary',
    'SALARY_DELETED_SUCCESSFULLY' => 'Salary Deleted Successfully',

    'FAIL_TO_DELETED_STATUS' => 'Fail To Deleted Status',
    'STATUS_DELTED_SUCCESSFULLY' => 'Status Deleted Successfully',

    'FAIL_TO_DELETED_ROLE' => 'Fail To Deleted Role',
    'ROLE_DELETED_SUCCESSFULLY' => 'Role Deleted Successfully',

    'PERCENTAGE_NOT_FOUND' => 'Percentage Not Found',
    'FAILE_TO_DELETED_PERCENTAGE' => 'Fail To Deleted Percentage',
    'PERCENTAGE_DELETED_SUCCESSFULLY' => 'Percentage Deleted Successfully',

    'FAIL_TO_CREATE_FOODTOPPING' => 'Failed to create food with toppings',

    'User address Not Found Found'=>"USER_ADDRESS_NOT_FOUND",

    'FAIL_TO_UPDATE_FOODTOPPING' => 'Failed to update food with toppings',
    'FOOD AND TOPPINGS SUCCESSFULLY DELETED' => 'Food and associated toppings successfully deleted',
    'FAIL TO DELETE FOOD AND INREDIENTS' => 'Failed to delete food and toppings',

    'FOOD_RESTAURANT_AND_TOPPING_CREATE_SUCCESSFULLY' => 'Food , restaurant and topping successfully create',
    'FAIL TO CREATE' => 'Fail to create',
    'FOOD_RESTAURANT_AND_TOPPING_UPDATE_SUCCESSFULLY' => 'Food , restaurant and topping successfully update',
    'FAIL TO UPDATE' => 'Fail to update',
    'DELETE_SUCCESSFULLY' => 'Food and its related data deleted successfully',
    'DELETE_FAIL' => 'Data deletion failed!',
    'IMAGE_DATA_NOT_FOUND' => 'Image Data Not Found'

];
