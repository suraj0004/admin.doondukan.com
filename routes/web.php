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
Route::get('storeEcomData', 'HomeController@storeEcomData');

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::group(['middleware' => 'auth:admin','prefix'=>'admin'], function()
{
	Route::get('home', 'HomeController@index')->name('home');
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
	Route::get('/users','Admin\AdminController@user')->name('users');
	Route::get('delete/user/{id}','Admin\AdminController@deleteUser')->name('DeleteUser');
	Route::get('create/user', function() { return view('createUser'); } )->name('CreateUser');
	Route::get('edit/users/{id}','Admin\AdminController@editUser')->name('EditUser');
	Route::get('temp/product','Admin\AdminController@TempProduct')->name('TempProduct');
	Route::get('delete/temp/product/{id}','Admin\AdminController@deleteTempProduct')->name('DeleteTempProduct');
	Route::get('add-tempproduct-to-mainproduct/{id}','Admin\AdminController@addTempProductToProduct')->name('AddTempProductToProduct');
	Route::get('search-product','Admin\AdminController@searchProduct')->name('SearchProduct');

	//post routes
	Route::post('/store/brand', 'Admin\AdminController@storeBrand')->name('StoreBrand');
	Route::post('/store/category', 'Admin\AdminController@StoreCategory')->name('StoreCategory');
	Route::post('/store/product', 'Admin\AdminController@storeProduct')->name('StoreProduct');
	Route::post('update/brand/{id}','Admin\AdminController@updateBrand')->name('UpdateBrand');
	Route::post('update/category/{id}','Admin\AdminController@updateCategory')->name('UpdateCategory');
	Route::post('update/product/{id}','Admin\AdminController@updateProduct')->name('UpdateProduct');
	Route::post('/store/user', 'Admin\AdminController@storeUser')->name('StoreUser');
	Route::post('update/user/{id}','Admin\AdminController@updateUser')->name('UpdateUser');
	Route::post('brand/import-excel','Admin\AdminController@importBrands')->name('import-excel');
	Route::post('/store/product/{id}', 'Admin\AdminController@storeTempProductToMainProduct')->name('StoreTempProductToMainProduct');
});

