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
	// Session::flush();
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
Route::any('destroyeatryimage/{imagename}', array('as' => 'destroyeatryimage', 'uses' => 'EateriesController@destroyeatryimage'));
Route::any('destroyeateryimageedit/{imagename}', array('as' => 'destroyeateryimageedit', 'uses' => 'EateriesController@destroyeateryimageedit'));
Route::resource('itemgroups', 'ItemGroupsController');
Route::resource('itemcategory', 'ItemCategoriesController');
Route::resource('uploadeatery', 'FoodController');
Route::resource('menu', 'MenuController');
Route::get('searcheateries', 'MenuController@searcheateries');
Route::resource('menusections', 'MenuSectionController');
Route::resource('menusubsections', 'MenuSubSectionController');
Route::resource('dishes', 'DishesController');
Route::resource('uploadmenu', 'UploadMenuController');
Route::get('generateExcel', 'UploadMenuController@generateExcel');
Route::resource('user', 'UserController');
Route::resource('appcustomers', 'AppCustomersController');
Route::resource('groups', 'GroupsController');
Route::resource('brands', 'BrandsController');
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

Route::post('api/v1/geteateries', array('as' => 'v1_geteateries', 'uses' => 'RestApi_V1_GeneralController@V1_GetEateries'));
Route::post('api/v1/geteaterydetailsbyid', array('as' => 'v1_geteaterydetailsbyid', 'uses' => 'RestApi_V1_GeneralController@V1_GetEateryDetailsById'));
Route::get('api/v1/getcategories','RestApi_V1_GeneralController@V1_GetCategories');
Route::post('api/v1/gethotelbyitemdetails', array('as' => 'v1_gethotelbyitemdetails', 'uses' => 'RestApi_V1_GeneralController@V1_GetHotelByItemDetails'));
Route::post('api/v1/addclickbeforeassociated', array('as' => 'v1_addclickbeforeassociated', 'uses' => 'RestApi_V1_GeneralController@V1_AddClickBeforeAssociated'));
Route::post('api/v1/addclickafterassociated', array('as' => 'v1_addclickafterassociated', 'uses' => 'RestApi_V1_GeneralController@V1_AddClickAfterAssociated'));
Route::post('api/v1/getclicksbeforeassociated', array('as' => 'v1_getclicksbeforeassociated', 'uses' => 'RestApi_V1_GeneralController@V1_GetClicksBeforeAssociated'));
Route::post('api/v1/getclicksafterassociated', array('as' => 'v1_getclicksafterassociated', 'uses' => 'RestApi_V1_GeneralController@V1_GetClicksAfterAssociated'));
Route::post('api/v1/getdishdetailsbyid', array('as' => 'v1_getdishdetailsbyid', 'uses' => 'RestApi_V1_GeneralController@V1_GetDishDetailsById'));

Route::get('api/v1/getcuisines','RestApi_V1_GeneralController@V1_GetCuisines');
Route::get('api/v1/getlifestylechoices','RestApi_V1_GeneralController@V1_GetLifeStyleChoices');
Route::get('api/v1/getnutritions','RestApi_V1_GeneralController@V1_GetNutritions');
Route::get('api/v1/getallergens','RestApi_V1_GeneralController@V1_GetAllergens');
Route::get('api/v1/ajaxsearch','ajaxSearchController@ajaxSearchByResult');

Route::post('api/v1/addtofavouriteeatery', array('as' => 'v1_addtofavouriteeatery', 'uses' => 'RestApi_V1_GeneralController@V1_AddToFavouriteEatery'));
Route::post('api/v1/removefromfavouriteeatery', array('as' => 'v1_removefromfavouriteeatery', 'uses' => 'RestApi_V1_GeneralController@V1_RemoveFromFavouriteEatery'));
Route::post('api/v1/getfavouriteeateries', array('as' => 'v1_getfavouriteeateries', 'uses' => 'RestApi_V1_GeneralController@V1_GetFavouriteEateries'));
Route::post('api/v1/removefavouriteeateries', array('as' => 'v1_removefavouriteeateries', 'uses' => 'RestApi_V1_GeneralController@V1_RemoveFavouriteEateries'));
Route::post('api/v1/addfeedbackeateries', array('as' => 'v1_addfeedbackeateries', 'uses' => 'RestApi_V1_GeneralController@V1_AddFeedbackEatery'));

Route::get('api/geteaterybylocation','RestApi_V1_GeneralController@GetEateryByLocation');
Route::get('api/getmenusectionbymenuIds','RestApi_V1_GeneralController@GetMenusectionByMenuIds');
Route::get('api/getmenusubsectionbymenusection','RestApi_V1_GeneralController@GetMenuSubsectionByMenuSection');


//End------------------------Api Version 1-------------------------End//