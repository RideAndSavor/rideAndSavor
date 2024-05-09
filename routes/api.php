<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\TownshipController;
use Illuminate\Database\Events\StatementPrepared;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('country',CountryController::class);
Route::resource('state',StateController::class);
Route::resource('city',CityController::class);
Route::resource('township',TownshipController::class);
