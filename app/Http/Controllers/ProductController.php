<?php

namespace App\Http\Controllers;

use App\Restaurant;
use App\Product;
use App\Unit;
use App\Category;
use App\SubCategory;
use App\OrderItem;
use App\Customer;
use App\Order;
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
        $subCategories = SubCategory::all();
        return view('restaurant.product.add-product',compact('restaurants','units','subCategories'));
    }

    public function addProductOrder()
    {
        $products = Product::all();
        $categories = Category::all();
        return view('restaurant.product.add-product-order',compact('products','categories'));
    }

    public function addProductOrderList()
    {
        session_start();
        if( request('check_list') != null){
            for ($i=0; $i < count(request('check_list')) ; $i++) {
                $orderItem  =  new OrderItem();
                $orderItem->product_id =  request('check_list')[$i];
                $orderItem->quantity =  request('quantity')[request('check_list')[$i]];
                array_push($_SESSION['items'],$orderItem);
            }
        }

        return redirect('punch-order');

    }

    public function storeProduct(Request $request) {
        $product = new Product();
        $product->name = request('name');
        $product->restaurant_id = request('restaurant');
        $product->subCategory_id = request('subCategory');
        $product->unit_id = request('unit');
        $product->unit_price =  request('price');
        $product->quantity = request('quantity');
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
        $subCategories = SubCategory::all();
        return view('restaurant.product.edit-product',compact('product','restaurants','units','subCategories'));
    }

    public function updateProduct($id)
    {
        $name = request('name');
        $restaurant_id = request('restaurant');
        $subCategory_id = request('subCategory');
        $unit_id = request('unit');
        $unit_price =  request('price');
        $quantity = request('quantity');

        if(request('image') != null){
            $imageName = time().'.'.request()->image->getClientOriginalExtension();
            request()->image->move(public_path('images/products'), $imageName);
            Product::where('id', $id)
            ->update([
                    'name'  => $name,
                    'restaurant_id' => $restaurant_id,
                    'subCategory_id'  => $subCategory_id,
                    'unit_id'  => $unit_id,
                    'unit_price'  => $unit_price,
                    'quantity'  => $quantity,
                    'image'=>$imageName
                ]);
        }
        else{
            Product::where('id', $id)
            ->update([
                    'name'  => $name,
                    'restaurant_id' => $restaurant_id,
                    'subCategory_id'  => $subCategory_id,
                    'unit_id'  => $unit_id,
                    'unit_price'  => $unit_price,
                    'quantity'  => $quantity,
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


    public function filterProduct(){
        $category = Category::where('id',request('category'))->first();
        $subCategory = SubCategory::where('id',request('subCategory'))->first();
        $products = Product::where('subCategory_id',request('subCategory'))->get();
        $categories = Category::all();
        return view('restaurant.product.add-product-order',compact('products','categories'));

    }

    public function getProductOrders($id) {
        $values = array();
        $orderItems = OrderItem::where('product_id',$id)->get();
        for($i = 0; $i < count($orderItems); $i++) {
            $order = Order::where('id', $orderItems[$i]->order_id)->first();
            $customer = Customer::where('id',$order->customer_id)->first();
            $object = (object) [
                'orderNo' => $order->id,
                'customer' => $customer->name,
                'quantity' => $orderItems[$i]->quantity,
                'total' => $order->total_price,
                'origin' => $order->origin
            ];
            $values[] = $object;
        }
        return $values;
    }
}
