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
Route::post('send-otp', 'Api\OtpController@sendOTP');
Route::post('ecommerce/send-otp', 'Api\OtpController@sendOTP');
Route::post('forgot-password', 'Api\ForgotPasswordController@resetPassword');
Route::group(['middleware' => ['auth:api','shoopkeeper'],'prefix'=>'retail'], function()
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

	Route::get('dashboard','Api\DashboardController@index');

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

	//API for getting all orders

	Route::get('get/orders', 'Api\ShopOrderController@index');

	// updating order confirmations status
	Route::post('shop/order', 'Api\ShopOrderController@updateStatus');
	//Get user bill list API(GET)
	Route::get('billList', 'Api\BillController@getBill');

	//Get User Sale(GET)
	Route::get('saleList', 'Api\SaleController@saleList');

	//Get customer invoice API(GET)
	Route::get('invoice/{id}', 'Api\BillController@invoice');

	//Set the bill status to paid (POST)
	Route::post('setStatusPaid', 'Api\BillController@setStatusPaid');

	//Return Sale (POST)
	Route::post('returnSale', 'Api\SaleController@saleReturn');
	/*
	|--------------------------------------------------------------------------
	| User APIs
	|--------------------------------------------------------------------------
	*/
	//Shop Setting API(GET)
	Route::post('updateShopProfile', 'Api\UserController@updateShopProfile');

	//Update user settting(POST).
	Route::post('updateProfile', 'Api\UserController@updateProfile');

	//Get User Profile(GET)
	Route::get('profile', 'Api\UserController@getUserProfile');

	//Confirm user password for certain actions(POST).
	Route::post('confirmPassword', 'Api\UserController@confirmPassword');

	Route::get('report/percentage','Api\PercentageController@getPercentageAndMore');

	//Add custom product route(POST)
	Route::post('add-user-custom-product','Api\CustomProductController@store');
    Route::post('edit-user-custom-product/{product_id}','Api\CustomProductController@update');

	//Get user temp products list(GET).
	Route::get('get-user-custom-product-list','Api\CustomProductController@index');
	Route::get('get-categories','Api\ProductController@getCategories');
	/**Purchase Return API's */
	Route::group(['namespace' => "Api"],function(){
		Route::get('/get-stock-for-purchase-return','PurchaseReturnController@getStock');
		Route::post('/add-purchase-return','PurchaseReturnController@store');
		Route::get('/purchase-return-list','PurchaseReturnController@index');

        Route::get('get-sale-for-return/{id}', 'SaleReturnController@show');
        Route::post('/add-sale-return','SaleReturnController@store');
        Route::get('/sale-return-list','SaleReturnController@index');
	});

	Route::group(['prefix'=>'report','namespace'=>'Reports'],function(){

		Route::get('top-highest-selling-products','ProductController@getTopHighestSellingProducts');
		Route::get('top-lowest-selling-products','ProductController@getTopLowestSellingProducts');
		Route::get('top-profitable-products','ProductController@getTopProfitableProducts');
		Route::get('top-less-profitable-products','ProductController@getTopLessProfitableProducts');
		Route::get('top-loosely-products','ProductController@getLooselyProducts');

		Route::get('sale-vs-profit','ComparisionController@getSaleVsProfit');
		Route::get('quantity-vs-sale','ComparisionController@getQuantityVsSale');
	  	Route::get('quantity-vs-profit','ComparisionController@getQuantityVsProfit');
		Route::get('all-in-one','ComparisionController@getAll_inOne');

		Route::get('sale-growth','SalesGrowthController');
		Route::get('profit-growth','ProfitGrowthController');
		Route::get('purchase-growth','PurchaseGrowthController');
	});

	//Download Shop QR Code
	Route::get('/get-qr','Api\UserController@getShopQR');
	Route::get('/get-product-catalogue','Api\ProductController@getProductCatalogue');
	Route::post('/add-product-from-catalogue','Api\PurchaseController@addProductToCatalogue');
});

/** Ecommerce api */
Route::group(['namespace'=>'Api\Ecommerce','prefix' => 'ecommerce'],function(){

    Route::post('login', 'UserController@login');
    Route::post('register', 'UserController@register');
    Route::get('/get-near-by-shop', 'ShopController@getNearByShop');
    /** Shop specific routes */
    Route::group(['prefix' => '{seller_id}-{shop_slug}' ], function(){

        Route::get('/shop/info','ShopController@sellerInfo');

        Route::group(['prefix' => 'cart','middleware' => ['auth:api','user'] ], function(){
            Route::post('sync','CartController@syncCart');
            Route::post('add','CartController@add');
            Route::get('get','CartController@fetchCartProducts');
            Route::patch('update','CartController@updateQuantity');
            Route::delete('delete/{cart_id}','CartController@deleteCartProduct');
        });
        Route::get('/','ShopController@index');
        Route::get('/{categorySlug}','ShopController@getCategoryProducts');
    });

    /** User specific routes */
    Route::group(['middleware' => ['auth:api','user']], function(){

        Route::post('logout', 'UserController@logout');
        Route::post('profile/update', 'UserController@updateProfile');

        Route::group(['prefix' => 'order' ], function(){
        	Route::post('checkout/{seller_id}-{shop_slug}','OrderController@checkout');
        	Route::get('list','OrderController@orderList');
        	Route::get('detail/{order_no}','OrderController@orderDetails');
            Route::post('cancel','OrderController@cancleOrder');

        });
    });
});


