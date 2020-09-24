<?php

namespace App\Http\Controllers;

use App\Address;
use App\ProductHistory;
use App\Restaurant;
use App\Product;
use App\Unit;
use App\Category;
use App\SubCategory;
use App\OrderItem;
use App\Customer;
use App\Order;
use App\RestaurantBranch;
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
           $product->restaurant_id = 1;
           $product->branch_id = request('branch_id');
           $product->subCategory_id = request('subCategory_id');
           $product->unit_id = request('unit_id');
           $product->sale_price = request('sale_price');
           $product->purchase_price = request('purchase_price');
           $product->stock = request('stock');
           $product->stock_value = request('stock') * request('purchase_price');
           $imageName = time() . '.' . request()->image->getClientOriginalExtension();
           request()->image->move(public_path('images/products'), $imageName);
           $product->image = $imageName;
           $product->save();

           $productHistory = new ProductHistory();
           $productHistory->product_id = $product->id;
           $productHistory->quantity = $product->stock;
           $productHistory->value = $product->purchase_price;
           $productHistory->action_type = 2;
           $productHistory->date = $product->created_at;
           $productHistory->save();
           error_log($productHistory->quantity);
           return $product;
    }

    public function updateProduct($id)
    {
        try {
            error_log('here');
            error_log(request('subCategory_id'));
            $name = request('name');
            $restaurant_id = 1;
            $branch_id = request('branch_id');
            $subCategory_id = request('subCategory_id');
            $unit_id = request('unit_id');
            $sale_price =  request('sale_price');
            $purchase_price = request('purchase_price');

            if(request('image') != null){
                $imageName = time().'.'.request()->image->getClientOriginalExtension();
                request()->image->move(public_path('images/products'), $imageName);
                Product::where('id', $id)
                ->update([
                        'name'  => $name,
                        'restaurant_id' => $restaurant_id,
                        'subCategory_id'  => $subCategory_id,
                        'branch_id' => $branch_id,
                        'unit_id'  => $unit_id,
                        'sale_price'  => $sale_price,
                        'purchase_price'  => $purchase_price,
                        'image'=>$imageName
                    ]);
            }
            else{
                Product::where('id', $id)
                ->update([
                        'name'  => $name,
                        'restaurant_id' => $restaurant_id,
                        'subCategory_id'  => $subCategory_id,
                        'branch_id' => $branch_id,
                        'unit_id'  => $unit_id,
                        'sale_price'  => $sale_price,
                        'purchase_price'  => $purchase_price
                    ]);
            }

            ProductHistory::where('product_id', $id)->where('action_type', 2)->update([
                'value' => $purchase_price
            ]);


            $productHistories = ProductHistory::where('product_id', $id)->get();
            $quantity = 0;
            $value = 0;
            foreach ($productHistories as $item) {
                if ($item->action_type === 1) {
                    $quantity = $quantity - $item->quantity;
                    $value = $value - ($item->quantity * $item->value);
                } else {
                    $quantity = $quantity + $item->quantity;
                    $value = $value + ($item->quantity * $item->value);
                }
            }
            Product::where('id', $id)->update([
                'stock' => $quantity,
                'stock_value' => $value
            ]);

            return Product::where('id', $id)->first();
        } catch(\Throwable $e) {
            error_log($e);
        }
    }


    public function deleteProduct($id)
    {
        try {
            error_log('here');
            Product::where('id', $id)
                   ->delete();
        } catch(\Throwable $e)  {
            return response()->json([
                'error' => 'Cannot delete product since it is part an order.',
            ],200);
        }
    }

    public function apiIndex()
    {
        return Product::all();
    }
    public function filterByBranch($id) {
        error_log($id);
        if($id == '-1') {
            return Product::all();
        } else {
            return Product::where('branch_id', $id)->get();
        }
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
            $branch = RestaurantBranch::where('id', $order->branch_id)->first();
            $restaurant = Restaurant::where('id', $branch->restaurant_id)->first();
            $address = Address::where('id',$order->address_id)->first();
            $object = (object) [
                'orderNo' => $order->id,
                'total' => $order->total_price,
                'status' => $order->status,
                'origin' => $order->origin,
                'isDelivery' => $order->delivery,
                'date' => $order->created_at,
                'quantity' => $orderItems[$i]->quantity,
                'customer' => $customer->name,
                'customerEmail' => $customer->email,
                'customerPhone' => $customer->phone,
                'deliveryAddress' => $address->description.' '.
                    $address->house.' '.
                    $address->street.' '.
                    $address->area.' '.
                    $address->city,
                'deliveryPhone' => $address->mobile,
                'restaurantName' => $restaurant->name,
                'branchName' => $branch->name
            ];
            $values[] = $object;
        }
        return $values;
    }
}
