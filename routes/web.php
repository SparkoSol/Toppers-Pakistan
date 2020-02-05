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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//Restaurant Routes
Route::get('/restaurant', 'RestaurantController@index');
Route::get('/add-restaurant', 'RestaurantController@addRestaurant');
Route::post('/store-restaurant','RestaurantController@storeRestaurant');
Route::get('/edit-restaurant/{id}','RestaurantController@editRestaurant');
Route::post('/update-restaurant/{id}','RestaurantController@updateRestaurant');
Route::get('/delete-restaurant/{id}','RestaurantController@deleteRestaurant');

//Restaurant Branch Routes
Route::get('/branch', 'RestaurantBranchController@index');
Route::get('/add-branch', 'RestaurantBranchController@addBranch');
Route::post('/store-branch','RestaurantBranchController@storeBranch');
Route::get('/edit-branch/{id}','RestaurantBranchController@editBranch');
Route::post('/update-branch/{id}','RestaurantBranchController@updateBranch');
Route::get('/delete-branch/{id}','RestaurantBranchController@deleteBranch');

//restaurant Product Routes
Route::get('/product', 'ProductController@index');
Route::get('/add-product', 'ProductController@addProduct');
Route::post('/store-product','ProductController@storeProduct');
Route::get('/edit-product/{id}','ProductController@editProduct');
Route::post('/update-product/{id}','ProductController@updateProduct');
Route::get('/delete-product/{id}','ProductController@deleteProduct');


//Category Route
Route::get('/category', 'CategoryController@index');
Route::get('/add-category', 'CategoryController@addCategory');
Route::post('/store-category','CategoryController@storeCategory');
Route::get('/edit-category/{id}','CategoryController@editCategory');
Route::post('/update-category/{id}','CategoryController@updateCategory');
Route::get('/delete-category/{id}','CategoryController@deleteCategory');

//Unit Route
Route::get('/unit', 'UnitController@index');
Route::get('/add-unit', 'UnitController@addUnit');
Route::post('/store-unit','UnitController@storeUnit');
Route::get('/edit-unit/{id}','UnitController@editUnit');
Route::post('/update-unit/{id}','UnitController@updateUnit');
Route::get('/delete-unit/{id}','UnitController@deleteUnit');






Route::get('/users', function() {
    return view('users')->with('restaurant', [App\Restaurant::all()]);
});


