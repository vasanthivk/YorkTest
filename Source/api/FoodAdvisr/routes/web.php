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
Route::resource('eateries', 'EateriesController');
Route::resource('uploadhotel', 'FoodController');
Route::resource('menu', 'MenuController');
Route::resource('user', 'UserController');
Route::resource('company', 'CompanyController');
Route::resource('category', 'CategoryController');
Route::resource('ingredients', 'IngredientsController');
Route::resource('nutrition', 'NutritionController');
Route::resource('items', 'ItemsController');
Route::resource('recipe', 'RecipeController');
Route::resource('itemnutritions', 'ItesmNutritionController');
Route::post('allowprivileges/{role_id}/{module_id}/{privilege_id}', array('as' => 'allowprivileges', 'uses' => 'PrivilegesController@allowprivileges'));
Route::post('denyprivileges/{role_id}/{module_id}/{privilege_id}', array('as' => 'denyprivileges', 'uses' => 'PrivilegesController@denyprivileges'));
Route::resource('privilegesmatrix', 'PrivilegesController@privilegesmatrix');
Route::resource('privileges', 'PrivilegesController');
Route::resource('logs', 'LogsController');
Route::resource('configuration', 'ConfigurationController');

//Start-----------------------Api Version 1------------------------Start//

Route::post('api/v1_gethotels', array('as' => 'v1_gethotels', 'uses' => 'RestApi_V1_GeneralController@V1_GetHotels'));
Route::post('api/v1_gethoteldetailsbyid', array('as' => 'v1_gethoteldetailsbyid', 'uses' => 'RestApi_V1_GeneralController@V1_GetHotelDetailsById'));
Route::post('api/v1_gettop10hotels', array('as' => 'v1_gettop10hotels', 'uses' => 'RestApi_V1_GeneralController@V1_GetTop10Hotels'));
Route::get('api/v1_getcategories','RestApi_V1_GeneralController@V1_GetCategories');
Route::post('api/v1_gethotelbyitemdetails', array('as' => 'v1_gethotelbyitemdetails', 'uses' => 'RestApi_V1_GeneralController@V1_GetHotelByItemDetails'));

//End------------------------Api Version 1-------------------------End//