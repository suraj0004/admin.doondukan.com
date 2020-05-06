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
	//User Check API(GET)
	Route::get('isAuthenticated' , function (){
		  return response()->json([
	      "success" => true,
	      "message" => "Authenticated"
		  ],200);
	});
	
	//Purchased APIs (POST)
	Route::post('addPurchase', 'Api\PurchaseController@addPurchase');

	//Purchased APIs (GET)
	Route::get('purchasedList', 'Api\PurchaseController@purchasedList');

	//Product API(GET)
	Route::get('globalProductList', 'Api\ProductController@getproductList');   
});
