<?php

namespace App\Http\Controllers;

use App\Restaurant;
use App\Product;
use App\Unit;
use App\Category;
use App\OrderItem;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    public function index()
    {
        $restaurants = Restaurant::all();
        $products = Product::all();
        return view('product',compact('restaurants','products'));
    }

    public function addProduct()
    {
        $restaurants = Restaurant::all();
        $units = Unit::all();
        $categories = Category::all();
        return view('restaurant.product.add-product',compact('restaurants','units','categories'));
    }

    public function addProductOrder()
    {
        $products = Product::all();
        return view('restaurant.product.add-product-order',compact('products'));
    }

    public function addProductOrderList()
    {
        session_start();
        $_SESSION['items'] = array();
        
        for ($i=0; $i < count(request('check_list')) ; $i++) { 
            $orderItem  =  new OrderItem();
            $orderItem->product_id =  request('check_list')[$i];
            $orderItem->quantity =  request('quantity')[request('check_list')[$i]];

            array_push($_SESSION['items'],$orderItem);
        }

        // for ($i = 0; $i < count($_SESSION['items']); $i++){
        //     echo $_SESSION['items'][$i];
        // }

        return redirect('/punch-order');

    }
    
    public function storeProduct(Request $request) {
        $product = new Product();
        $product->name = request('name');
        $product->restaurant_id = request('restaurant');
        $product->category_id = request('category');
        $product->unit_id = request('unit');
        $product->unit_price =  request('price');
        $product->quantity = request('quantity'); 
        $product->serving  = request('serving');
        $imageName = time().'.'.request()->image->getClientOriginalExtension();
        request()->image->move(public_path('images/products'), $imageName);

        $product->image = $imageName;
        $product->save();
        return redirect('/product');
    }

    public function editProduct($id)
    {
        $product = Product::where('id', $id)->first();
        $restaurants = Restaurant::all();
        $units = Unit::all();
        $categories = Category::all();
        return view('restaurant.product.edit-product',compact('product','restaurants','units','categories'));
    }
    
    public function updateProduct($id)
    {
        $name = request('name');
        $restaurant_id = request('restaurant');
        $category_id = request('category');
        $unit_id = request('unit');
        $unit_price =  request('price');
        $quantity = request('quantity'); 
        $serving  = request('serving');

        if(request('image') != null){
            $imageName = time().'.'.request()->image->getClientOriginalExtension();
            request()->image->move(public_path('images/products'), $imageName);
            Product::where('id', $id)
            ->update([
                    'name'  => $name,
                    'restaurant_id' => $restaurant_id,
                    'category_id'  => $category_id,
                    'unit_id'  => $unit_id,
                    'unit_price'  => $unit_price,
                    'quantity'  => $quantity,
                    'serving'=> $serving,
                    'image'=>$imageName
                ]);
        }
        else{
            Product::where('id', $id)
            ->update([
                    'name'  => $name,
                    'restaurant_id' => $restaurant_id,
                    'category_id'  => $category_id,
                    'unit_id'  => $unit_id,
                    'unit_price'  => $unit_price,
                    'quantity'  => $quantity,
                    'serving'=> $serving
                ]);
        }        
        return redirect('/product');
        
    }


    public function deleteProduct($id)
    {
        Product::where('id', $id)
                ->delete();
        
        return redirect('/product');
    }

    public function apiIndex()
    {
        return Product::all();
    }

    public function apiIndexById($id)
    {
        return Product::where('id', $id)->first();
    }
}
