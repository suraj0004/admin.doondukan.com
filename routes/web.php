<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware' => 'auth:web','prefix'=>'admin'], function()
{
	//get routes
	Route::get('/brands', 'Admin\AdminController@brand')->name('brands');
	Route::get('/categories', 'Admin\AdminController@category')->name('categories');
	Route::get('/products', 'Admin\AdminController@product')->name('products');
	Route::get('create/category', function() { return view('createCategory'); } )->name('CreateCategory');
	Route::get('create/brand', function() { return view('createBrand'); })->name('CreateBrand');
	Route::get('delete/brand/{id}','Admin\AdminController@deleteBrand')->name('DeleteBrand');
	Route::get('delete/category/{id}','Admin\AdminController@deleteCategory')->name('DeleteCategory');
	Route::get('create/product', 'Admin\AdminController@createProduct')->name('CreateProduct');
	Route::get('delete/product/{id}','Admin\AdminController@deleteProduct')->name('DeleteProduct');
	Route::get('edit/brand/{id}','Admin\AdminController@editBrand')->name('EditBrand');
	Route::get('edit/category/{id}','Admin\AdminController@editCategory')->name('EditCategory');
	Route::get('edit/product/{id}','Admin\AdminController@editProduct')->name('EditProduct');

	//post routes
	Route::post('/store/brand', 'Admin\AdminController@storeBrand')->name('StoreBrand');
	Route::post('/store/category', 'Admin\AdminController@StoreCategory')->name('StoreCategory');
	Route::post('/store/product', 'Admin\AdminController@storeProduct')->name('StoreProduct');
	Route::post('update/brand/{id}','Admin\AdminController@updateBrand')->name('UpdateBrand');
	Route::post('update/category/{id}','Admin\AdminController@updateCategory')->name('UpdateCategory');
	Route::post('update/product/{id}','Admin\AdminController@updateProduct')->name('UpdateProduct');
});