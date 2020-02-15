<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderItem;
use Illuminate\Support\Facades\Auth;
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
        if(Auth::user()->type == "Main Admin"){
            $pendingOrders = Order::where('status','Pending')->get();
        }
        else{
            $pendingOrders = Order::where('status','Pending')->where('branch_id',Auth::user()->branch_id)->get();
        }
        return view('order',compact('pendingOrders'));
    }

    public function orderByCustomerId($id)
    {
        $Orders = Order::where('customer_id',$id)->get();
        return view('customer.customer-order',compact('Orders'));
    }

    public function completedOrder()
    {
        if(Auth::user()->type == "Main Admin"){
            $completedOrders = Order::where('status','Complete')->get();
        
        }
        else{
            $completedOrders = Order::where('status','Complete')->where('branch_id',Auth::user()->branch_id)->get();
        }
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

    public function punchOrder(){    

        session_start();

        return view('punch-order');
    }


    public function storeOrderAdmin(){
        session_start();
        $order = new Order();
        $order->customer_id = 1;
        $order->address_id = 1;
        $order->branch_id = Auth::user()->branch_id;
        $order->total_price = $_SESSION['total'];
        $order->instructions = "No Instructions";
        $order->status = "Complete";

        $order->save();
        


        if (isset($_SESSION['items'])){
            for ($i = 0; $i < count($_SESSION['items']); $i++){
                $_SESSION['items'][$i]->order_id = $order->id;
                $_SESSION['items'][$i]->save();
            }
        }

        session_unset();
        session_destroy();
        return redirect('home');
    }
}
