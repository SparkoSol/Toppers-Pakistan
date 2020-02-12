<?php

namespace App\Http\Controllers;


use App\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
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

    public function apiIndex()
    {
        return OrderItem::all();
    }

    public function apiIndexById($id)
    {
        return OrderItem::where('id', $id)->first();
    }

    public function storeOrderItemApi(){
        $orderItem = new OrderItem();
        $orderItem->order_id = request('order_id');
        $orderItem->product_id = request('product_id');
        $orderItem->quantity = request('quantity');

        $orderItem->save();
        return $orderItem;
    }

    public function getItems($id)
    {
        $orderItems = OrderItem::where('order_id',$id)->get();
        return view('order-item',compact('orderItems'));
    }
}
