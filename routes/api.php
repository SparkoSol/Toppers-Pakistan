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
Route::get('/subCategory', 'SubCategoryController@apiIndex');
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
Route::get('/subCategory/{id}/products','SubCategoryController@apiProducts');
Route::get('/category/{id}/subCategories','CategoryController@apiSubCategories');


//Customer Register and Login

Route::middleware('auth:api')->get('/customer-user', function (Request $request) {
    return $request->user();
});

//Customer
Route::post('/register-customer','CustomerController@register');
Route::post('/login-customer','CustomerController@login');
Route::post('/change-customer-password/{id}','CustomerController@changePassword');
Route::post('/userExists','CustomerController@apiCheckUserExists');

Route::post('/address','AddressController@storeAddress');
Route::get('/address/{id}','AddressController@getAddressByCustomerId');
Route::delete('/address/{id}','AddressController@deleteAddress');


Route::post('/order','OrderController@storeOrderApi');
Route::post('/order-item','OrderItemController@storeOrderItemApi');

Route::get('/customer-order/{id}','CustomerController@getOrders');

Route::get('/order-items/{id}','OrderItemController@getItemsApi');
Route::get('/order-item-product/{id}','OrderItemController@orderItemProduct');


//Carosel
Route::get('/carosel','CaroselController@indexApi');

Route::post('/orderConfirmation','OrderController@sendEmailApi');



//Forget Password

Route::post('/forgetPassword','ForgetPasswordController@index');


