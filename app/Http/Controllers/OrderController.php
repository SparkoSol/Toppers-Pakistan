<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
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
        $pendingOrders = Order::where('status','Pending')->get();
        return view('order',compact('pendingOrders'));
    }

    public function orderByCustomerId($id)
    {
        $Orders = Order::where('customer_id',$id)->get();
        return view('customer.customer-order',compact('Orders'));
    }

    public function completedOrder()
    {
        $completedOrders = Order::where('status','Complete')->get();
        return view('completed-order',compact('completedOrders'));
    }

    public function complete($id)
    {
        Order::where('id',$id)->update(['status'  => 'Complete']);
        return redirect('/home');

    }
    public function apiIndex()
    {
        return Order::all();
    }

    public function apiIndexById($id)
    {
        return Order::where('id', $id)->first();
    }

    public function storeOrderApi(){
        $order = new Order();
        $order->customer_id = request('customer_id');
        $order->address_id = request('address_id');
        $order->total_price = request('total_price');
        $order->instructions = request('instruction');

        $order->save();
        return $order;

    }
}
