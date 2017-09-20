<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Establishment;
Route::get('/', function () {
	$establishment_count = Establishment::all()->count();
    return view('welcome', compact('establishment_count'));
});
Route::post('store','FoodController@store');

Route::post('api/gethoteldetailsbyid', array('as' => 'gethoteldetailsbyid', 'uses' => 'RestApiGeneralController@GetHotelDetailsById'));
Route::post('api/gethotels', array('as' => 'gethotels', 'uses' => 'RestApiGeneralController@GetHotels'));