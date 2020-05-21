<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login', 'Api\UserController@login');
Route::post('register', 'Api\UserController@register');
Route::group(['middleware' => 'auth:api','prefix'=>'retail'], function()
{
	//User Log out API(POST)
	Route::post('logout', 'Api\UserController@logout');

	//User Check API(GET)
	Route::get('isAuthenticated' , function (){
		  return response()->json([
	      "success" => true,
	      "message" => "Authenticated"
		  ],200);
	});
	
	/*
	|--------------------------------------------------------------------------
	| Purchased APIs
	|--------------------------------------------------------------------------
	*/
	//Add Purchase API (POST)
	Route::post('addPurchase', 'Api\PurchaseController@addPurchase');

	//Get Purchase list API (GET)
	Route::get('purchasedList', 'Api\PurchaseController@purchasedList');
    //End Of purchase API's.

	//Product API(GET)
	Route::get('globalProductList', 'Api\ProductController@getproductList');
	/*
	|--------------------------------------------------------------------------
	| Stock APIs
	|--------------------------------------------------------------------------
	*/
	//Add Slot Price API(POST)
	Route::post('setStockprice', 'Api\StockController@setStockprice');

	//Get GlobalStock list(GET)
	Route::get('globalStockList', 'Api\StockController@getglobalStockList');

	//Get Stock List(GET)
	Route::get('getstocklist', 'Api\StockController@getstocklist');

	//Get Global Available Stock List(GET)
	Route::get('getAvailableGlobalStockList', 'Api\StockController@getAvailableGlobalStockList');

	/*
	|--------------------------------------------------------------------------
	| Generate Bill APIs
	|--------------------------------------------------------------------------
	*/
	//Generate Bill API(GET)
	Route::post('generateBill', 'Api\BillController@generateBill');

	//Get User Sale(GET)
	Route::get('saleList', 'Api\SaleController@saleList');
});