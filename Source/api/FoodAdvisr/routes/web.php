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

Route::post('api/gethoteldetailsbyid', array('as' => 'gethoteldetailsbyid', 'uses' => 'RestApiGeneralController@GetHotelDetailsById'));
Route::post('api/gethotels', array('as' => 'gethotels', 'uses' => 'RestApiGeneralController@GetHotels'));


Route::get('/', function () {
    return view('login.login');
});
Route::get('/forgot', function () {
    return view('login.forgot');
});
Route::resource('login', 'LoginController');
Route::post('validateuser', array('as' => 'validateuser', 'uses' => 'LoginController@validateuser'));
Route::post('forgot', array('as' => 'forgot', 'uses' => 'LoginController@forgot'));
Route::resource('dashboard', 'DashboardController');
Route::resource('hotel', 'HotelController');
Route::resource('uploadhotel', 'FoodController');
Route::resource('user', 'UserController');
Route::resource('company', 'CompanyController');
Route::resource('items', 'ItemsController');
Route::post('allowprivileges/{role_id}/{module_id}/{privilege_id}', array('as' => 'allowprivileges', 'uses' => 'PrivilegesController@allowprivileges'));
Route::post('denyprivileges/{role_id}/{module_id}/{privilege_id}', array('as' => 'denyprivileges', 'uses' => 'PrivilegesController@denyprivileges'));
Route::resource('privilegesmatrix', 'PrivilegesController@privilegesmatrix');
Route::resource('privileges', 'PrivilegesController');
Route::resource('logs', 'LogsController');