<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


//GET ALL

Route::get('/category', 'CategoryController@apiIndex');
Route::get('/customer', 'CustomerController@apiIndex');
Route::get('/order', 'OrderController@apiIndex');
Route::get('/order-item', 'OrderItemController@apiIndex');
Route::get('/product', 'ProductController@apiIndex');
Route::get('/restaurant', 'RestaurantController@apiIndex');
Route::get('/restaurant-branch', 'RestaurantBranchController@apiIndex');
Route::get('/unit', 'UnitController@apiIndex');

//Get by id

Route::get('/category/{id}','CategoryController@apiIndexById');
Route::get('/customer/{id}','CustomerController@apiIndexById');
Route::get('/order/{id}','OrderController@apiIndexById');
Route::get('/order-item/{id}','OrderItemController@apiIndexById');
Route::get('/product/{id}','ProductController@apiIndexById');
Route::get('/restaurant/{id}','RestaurantController@apiIndexById');
Route::get('/restaurant-branch/{id}','RestaurantBranchController@apiIndexById');
Route::get('/unit/{id}','UnitController@apiIndexById');


//Get Products by category id
Route::get('/category/{id}/products','CategoryController@apiProducts');


//Customer Register and Login

Route::middleware('auth:api')->get('/customer-user', function (Request $request) {
    return $request->user();
});

Route::post('/register-customer','CustomerController@register');



