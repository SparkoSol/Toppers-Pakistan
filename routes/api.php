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

//Route::middleware(['auth:api'])->group(function () {

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
    Route::get('/carosel', 'CaroselController@indexApi');

    Route::get('saleOrder/mail/{id}', 'SaleOrderController@sendEmailApi');

//Forget Password
    Route::get('/forgetPassword/{id}', 'ForgetPasswordController@get');
    Route::post('/forgetPassword', 'ForgetPasswordController@index');
    Route::post('reset-password','ForgetPasswordController@resetPassword');

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
    Route::get('/subCategory/{id}/product/available/{branch}', 'SubCategoryController@availableItems');
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
    Route::delete('productHistory/delete/{id}','ProductHistoryController@deleteProductHistory');

//product
    Route::get('product/filter/{id}', 'ProductController@filterByBranch');
    Route::post('product/store', 'ProductController@storeProduct');
    Route::post('product/update/{id}','ProductController@updateProduct');
    Route::delete('product/delete/{id}','ProductController@deleteProduct');

//Auth Admin

    Route::get('auth/user', 'UserController@getAuthenticatedUser');
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

// Sale Order Routes
    Route::get('/saleOrder','SaleOrderController@getSale');
    Route::get('/saleOrder/summary/{id}','SaleOrderController@getSummary');
    Route::get('/saleOrder/filterMonth','SaleOrderController@filterMonth');
    Route::get('/saleOrder/getInvoice','SaleOrderController@getInvoiceNumber');
    Route::post('/saleOrder/filter/{id}','SaleOrderController@customFilter');
    Route::post('/saleOrder/store','SaleOrderController@store');
    Route::get('/saleOrder/filterYear','SaleOrderController@filterYear');
    Route::get('/saleOrder/filter/{id}/{branchId}','SaleOrderController@filter');
    Route::get('/saleOrder/{id}','SaleOrderController@getSaleById');
    Route::post('/saleOrder/update/{id}','SaleOrderController@update');
    Route::get('/saleOrder/branch/{branchId}','SaleOrderController@getSaleByBranch');
    Route::get('saleOrder/customer/{id}/{branchId}', 'SaleOrderController@getSalesByCustomer');
    Route::delete('/saleOrder/delete/{id}', 'SaleOrderController@delete');
    Route::get('/saleOrder/paid/{id}', 'SaleOrderController@markAsPaid');
    Route::get('/saleOrder/complete/{id}', 'SaleOrderController@markAsComplete');


// Sale Order Items Route
    Route::get('/saleOrderItems/saleOrder/{id}','SaleOrderItemController@getSaleItemsBySaleId');

// Payment In Routes

    Route::get('/paymentIn/branch/{id}','PaymentInController@getPaymentIn');
    Route::get('/paymentIn/getReceipt','PaymentInController@getReceiptNumber');
    Route::post('/paymentIn/store','PaymentInController@store');
    Route::post('/paymentIn/filter/{id}','PaymentInController@customFilter');
    Route::get('/paymentIn/{id}','PaymentInController@getPaymentInById');
    Route::post('/paymentIn/update/{id}','PaymentInController@update');
    Route::delete('/paymentIn/delete/{id}', 'PaymentInController@delete');

// Sale Return Routes

    Route::get('/saleReturn/branch/{id}','SaleReturnController@getSaleReturn');
    Route::get('/saleReturn/getInvoice','SaleReturnController@getInvoiceNumber');
    Route::get('/saleReturn/{id}','SaleReturnController@getSaleReturnById');
    Route::post('/saleReturn/store', 'SaleReturnController@store');
    Route::post('/saleReturn/update/{id}', 'SaleReturnController@update');
    Route::post('/saleReturn/filter/{id}','SaleReturnController@customFilter');
    Route::delete('/saleReturn/delete/{id}', 'SaleReturnController@delete');

// Carousel
    Route::get('/carousel', 'CarouselController@index');
    Route::get('/carousel/{id}', 'CarouselController@getById');
    Route::post('/carousel/store','CarouselController@store');
    Route::post('/carousel/update/{id}','CarouselController@update');
    Route::delete('/carousel/delete/{id}','CarouselController@delete');

// Notification
    Route::get('notification', 'FirebaseController@get');
    Route::post('notification/store', 'FirebaseController@store');
    Route::delete('notification/delete/{id}', 'FirebaseController@delete');

// Item

    Route::get('item/filter/{id}', 'ItemController@filterByBranch');
    Route::get('item', 'ItemController@get');
    Route::get('item/{id}', 'ItemController@getById');
    Route::post('/item/store', 'ItemController@store');
    Route::post('/item/update/{id}', 'ItemController@update');
    Route::delete('/item/delete/{id}', 'ItemController@delete');

// Item Transaction

    Route::get('itemTransaction/item/{id}', 'ItemTransactionController@getByItemId');
    Route::get('itemTransaction/variant/{id}', 'ItemTransactionController@getByVariantId');
    Route::post('itemTransaction/no-variant/store', 'ItemTransactionController@storeNoVariant');
    Route::post('itemTransaction/variant/store', 'ItemTransactionController@storeVariant');
    Route::post('itemTransaction/variant/update/{id}', 'ItemTransactionController@updateVariant');
    Route::post('itemTransaction/no-variant/update/{id}', 'ItemTransactionController@updateNoVariant');
    Route::delete('itemTransaction/delete/{id}', 'ItemTransactionController@delete');

// Item Images

    Route::post('itemImages/store/{id}', 'ItemImageController@store');
    Route::post('itemImages/update/{id}', 'ItemImageController@update');
    Route::get('itemImages/item/{id}', 'ItemImageController@getByItemId');

// Option

    Route::get('option/item/{id}', 'OptionController@getByItem');
    Route::get('option/item/{id}/app', 'OptionController@getByItemApp');

// Variant

    Route::get('variant/item/{id}', 'VariantController@getByItem');
    Route::get('order/variant/item/{id}', 'VariantController@getByOrderItem');

// Payment Out Routes

    Route::get('/paymentOut/branch/{id}','PaymentOutController@getPaymentOut');
    Route::get('/paymentOut/getReceipt','PaymentOutController@getReceiptNumber');
    Route::post('/paymentOut/store','PaymentOutController@store');
    Route::post('/paymentOut/filter/{id}','PaymentOutController@customFilter');
    Route::get('/paymentOut/{id}','PaymentOutController@getPaymentOutById');
    Route::post('/paymentOut/update/{id}','PaymentOutController@update');
    Route::delete('/paymentOut/delete/{id}', 'PaymentOutController@delete');

// Purchase Order Routes
    Route::get('/purchaseOrder','PurchaseOrderController@getPurchase');
    Route::get('/purchaseOrder/summary/{id}','PurchaseOrderController@getSummary');
    Route::get('/purchaseOrder/filterMonth','PurchaseOrderController@filterMonth');
    Route::get('/purchaseOrder/getInvoice','PurchaseOrderController@getInvoiceNumber');
    Route::post('/purchaseOrder/filter/{id}','PurchaseOrderController@customFilter');
    Route::post('/purchaseOrder/store','PurchaseOrderController@store');
    Route::get('/purchaseOrder/filterYear','PurchaseOrderController@filterYear');
    Route::get('/purchaseOrder/filter/{id}/{branchId}','PurchaseOrderController@filter');
    Route::get('/purchaseOrder/{id}','PurchaseOrderController@getPurchaseById');
    Route::post('/purchaseOrder/update/{id}','PurchaseOrderController@update');
    Route::get('/purchaseOrder/supplier/{id}/{branchId}','PurchaseOrderController@getPurchaseBySupplier');
    Route::delete('/purchaseOrder/delete/{id}', 'PurchaseOrderController@delete');


// Purchase Order Items Route
    Route::get('/purchaseOrderItems/purchaseOrder/{id}','PurchaseOrderItemController@getPurchaseItemsByPurchaseId');

// Purchase Return Routes

    Route::get('/purchaseReturn/branch/{id}','PurchaseReturnController@getPurchaseReturn');
    Route::get('/purchaseReturn/getInvoice','PurchaseReturnController@getInvoiceNumber');
    Route::get('/purchaseReturn/{id}','PurchaseReturnController@getPurchaseReturnById');
    Route::post('/purchaseReturn/store', 'PurchaseReturnController@store');
    Route::post('/purchaseReturn/update/{id}', 'PurchaseReturnController@update');
    Route::post('/purchaseReturn/filter/{id}','PurchaseReturnController@customFilter');
    Route::delete('/purchaseReturn/delete/{id}', 'PurchaseReturnController@delete');


// New Notification Routes

    Route::get('/notification/new','NotificationController@get');
    Route::get('/notification/new/{id}','NotificationController@getById');
    Route::post('/notification/new','NotificationController@store');
    Route::delete('/notification/new/{id}','NotificationController@delete');

// Expense Routes

    Route::get('/expense','ExpenseController@get');
    Route::get('/expense/{id}','ExpenseController@getById');
    Route::get('/expense/branch/{id}','ExpenseController@getByBranch');
    Route::post('/expense/store','ExpenseController@store');
    Route::post('/expense/update/{id}','ExpenseController@update');
    Route::delete('/expense/delete/{id}','ExpenseController@delete');

// Dashboard
    Route::get('dashboard/{id}', 'DashboardController@dashboardSummary');
    Route::get('/', 'DashboardController@home');
//});


    Route::post('auth/log-in', 'UserController@authenticate');
    Route::post('auth/log-out','UserController@logout');
