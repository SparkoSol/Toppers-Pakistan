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


    Route::get('/order', 'OrderController@apiIndex');
    Route::get('/order-item', 'OrderItemController@apiIndex');
    Route::get('/product', 'ProductController@apiIndex');
    Route::get('/restaurant', 'RestaurantController@apiIndex');
    Route::get('/restaurant-branch', 'RestaurantBranchController@apiIndex');

    Route::post('/register-customer', 'CustomerController@register');
    Route::post('/login-customer', 'CustomerController@login');
    Route::post('/change-customer-password/{id}', 'CustomerController@changePassword');
    Route::post('/userExists', 'CustomerController@apiCheckUserExists');
    Route::post('/address', 'AddressController@storeAddress');
    Route::get('/address/{id}', 'AddressController@getAddressByCustomerId');
    Route::delete('/address/{id}', 'AddressController@deleteAddress');
    Route::post('/order', 'OrderController@storeOrderApi');
    Route::post('/order-item', 'OrderItemController@storeOrderItemApi');
    Route::get('/customer-order/{id}', 'CustomerController@getOrders');
    Route::get('/order-items/{id}', 'OrderItemController@getItemsApi');
    Route::get('/order-item-product/{id}', 'OrderItemController@orderItemProduct');

    Route::get('product/{id}/orders', 'ProductController@getProductOrders');

//Get by id
    Route::get('/order/{id}', 'OrderController@apiIndexById');
    Route::get('/order-item/{id}', 'OrderItemController@apiIndexById');
    Route::get('/product/{id}', 'ProductController@apiIndexById');
    Route::get('/restaurant/{id}', 'RestaurantController@apiIndexById');
    Route::get('/restaurant-branch/{id}', 'RestaurantBranchController@apiIndexById');

//Customer Register and Login

//        Route::middleware('auth:api')->get('/customer-user', function (Request $request) {
//            return $request->user();
//        });


//Carousel
    Route::get('/carousel', 'CaroselController@indexApi');
    Route::post('/orderConfirmation', 'OrderController@sendEmailApi');

//Forget Password
    Route::post('/forgetPassword', 'ForgetPasswordController@index');

//New Routes For Admin Panel


//Category
    Route::get('/category', 'CategoryController@apiIndex');
    Route::get('/category/{id}', 'CategoryController@apiIndexById');
    Route::get('/category/{id}/subCategories', 'CategoryController@apiSubCategories');
    Route::post('category/store', 'CategoryController@storeCategory');
    Route::post('category/update/{id}', 'CategoryController@updateCategory');
    Route::delete('category/delete/{id}', 'CategoryController@deleteCategory');

//SubCategory
    Route::get('/subCategory', 'SubCategoryController@apiIndex');
    Route::get('subCategory/{id}', 'SubCategoryController@apiIndexById');
    Route::get('/subCategory/{id}/products', 'SubCategoryController@apiProducts');
    Route::post('subcategory/store', 'SubCategoryController@storeSubCategory');
    Route::post('subcategory/update/{id}', 'SubCategoryController@updateSubCategory');
    Route::delete('subcategory/delete/{id}', 'SubCategoryController@deleteSubCategory');
//Unit
    Route::get('/unit', 'UnitController@apiIndex');
    Route::get('/unit/{id}', 'UnitController@getById');
    Route::get('/unit/{id}/products', 'UnitController@getProducts');
    Route::post('unit/store', 'UnitController@storeUnit');
    Route::post('unit/update/{id}', 'UnitController@updateUnit');
    Route::delete('unit/delete/{id}', 'UnitController@deleteUnit');

//productHistory
    Route::post('productHistory/store', 'ProductHistoryController@storeProductHistory');
    Route::get('productHistory/product/{id}', 'ProductHistoryController@apiIndexById');
    Route::post('productHistory/update/{id}', 'ProductHistoryController@updateProductHistory');
    Route::delete('productHistory/{id}','ProductHistoryController@deleteProductHistory');

//product
    Route::get('product/filter/{id}', 'ProductController@filterByBranch');
    Route::post('product/store', 'ProductController@storeProduct');
    Route::post('product/update/{id}','ProductController@updateProduct');
    Route::delete('product/delete/{id}','ProductController@deleteProduct');

//Auth Admin
    Route::post('auth/log-in', 'UserController@authenticate');
    Route::get('auth/user', 'UserController@getAuthenticatedUser');
    Route::post('auth/log-out','UserController@logout');
    Route::post('/auth/sign-up','UserController@register');
    Route::post('/auth/change-password/{id}','UserController@changePassword');
    Route::post('/auth/update/{id}','UserController@update');

//branch
    Route::get('/branch', 'RestaurantBranchController@apiIndex');
    Route::get('/branch/{id}', 'RestaurantBranchController@apiIndexById');
    Route::get('/branch/managers/{id}', 'RestaurantBranchController@managers');
    Route::post('/branch/store','RestaurantBranchController@storeBranch');
    Route::post('/branch/update/{id}','RestaurantBranchController@updateBranch');
    Route::delete('branch/delete/{id}','RestaurantBranchController@deleteBranch');

// Admin Customer Controls
    Route::get('/customer', 'CustomerController@customers');
    Route::get('/customer/{id}', 'CustomerController@apiIndexById');
    Route::get('customer/orders/{id}', 'CustomerController@getOrders');
    Route::get('customer/transactions/{id}','CustomerTransactionController@getByCustomer');
    Route::post('customer/store', 'CustomerController@store');
    Route::post('customer/update/{id}', 'CustomerController@update');
    Route::delete('customer/delete/{id}', 'CustomerController@delete');

// Admin Supplier Controls
    Route::get('/supplier', 'SupplierController@apiIndex');
    Route::get('/supplier/{id}', 'SupplierController@apiIndexById');
    Route::get('supplier/transactions/{id}','SupplierTransactionController@getBySupplier');
    Route::post('supplier/store', 'SupplierController@store');
    Route::post('supplier/update/{id}', 'SupplierController@update');
    Route::delete('supplier/delete/{id}', 'SupplierController@delete');

