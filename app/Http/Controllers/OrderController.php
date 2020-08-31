<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderItem;
use App\Customer;
use App\RestaurantBranch;
use App\Address;
use App\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\MailSender;
use App\Mail\MailSenderApi;


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
        if(Auth::user()){
            if(Auth::user()->type == "Main Admin"){
                $pendingOrders = Order::where('status','Pending')->get();
            }
            else{
                $pendingOrders = Order::where('status','Pending')->where('branch_id',Auth::user()->branch_id)->get();
            }
            return view('order',compact('pendingOrders'));
        }

        return redirect('login');

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

    public function reject($id)
    {
        Order::where('id',$id)->update(['status'  => 'Rejected']);
        return redirect('/home');
    }

    public function invoice($id)
    {
        $order =  Order::where('id',$id)->first();
        $orderItems = $order->orderItems;
        return view('order-invoice',compact('order','orderItems'));
    }

    public function invoicePrint($id)
    {
        $order =  Order::where('id',$id)->first();
        $orderItems = $order->orderItems;
        return view('order-invoice-print',compact('order','orderItems'));
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
        $order->branch_id = request('branch_id');
        $order->delivery = request('delivery');

        $order->save();
        return $order;
    }

    public function sendEmailApi() {
        $order = request('order');
        $customer = Customer::where('id',$order['customer_id'])->first();
        $branch = RestaurantBranch::where('id',$order['branch_id'])->first();
        $data = array(
            'name' => $customer->name,
            'branch' => $branch->name,
            'price' => $order['total_price'],
            'delivery' => $order['delivery'],
            'items' => request('orderItems'),
            'orderId' => $order['id']
        );

        Mail::to($customer->email)->send(
            new MailSenderApi($data,'Order Placed')
        );

    }

    public function addCustomerInfo(){    
        session_start();
        if(isset($_SESSION['customer'])){
            $customer = $_SESSION['customer'];
            $address= $_SESSION['address'];
    
            return view('punch-order',compact('customer','address'));
        }else{
            $_SESSION['items'] = array();
            return view('add-customer-order');
        }

    }

    public function storeCustomerInfo(){

        session_start();

        $time = date('h:i:sa');

        $allCustomers = Customer::all();
		foreach ($allCustomers as $customer) {
			if($customer->email == request('email')){
                $address = new Address();
                $address->customer_id = $customer->id;
                if(request('description') == null){
                    $address->description = "toppers";
                }else{
                    $address->description = request('description');
                }
                if(request('house') == null){
                    $address->house = 'toppers';
                }else{
                    $address->house = request('house');
                }
                if(request('street') == null){
                    $address->street = 'toppers';
                }else{
                    $address->street = request('street');
                }
                if(request('area') == null){
                    $address->area = 'toppers';
                }else{
                    $address->area = request('area');
                }
                if(request('phone') == null){
                    $address->mobile = "00000000";
                }else{
                    $address->mobile = request('phone');
                }
                if(request('city') == null){
                    $address->city = 'toppers';
                }else{
                    $address->city = request('city');
                }
                $address->save();
                $_SESSION['ins'] = request('ins');
                $_SESSION['customer'] = $customer;
                $_SESSION['address'] = $address;
                return view('punch-order',compact('customer','address'));
			}
        }
        
        $_SESSION['ins'] = request('ins');
        $customer = new Customer();
        if(request('name') == null){
            $customer->name = "Toppers Pakistan";    
        }else{
            $customer->name = request('name');
        }
        if(request('email') == null){
            $customer->email = "toppersPakistan@$time";
        }else{
            $customer->email = request('email');
        }
        if(request('phone') == null){
            $customer->phone = "000000";
        }else{
            $customer->phone = request('phone');
        }

        $customer->password = bcrypt('12345678');
        $customer->save();

        $address = new Address();
        $address->customer_id = $customer->id;
        if(request('description') == null){
            $address->description = "toppers";
        }else{
            $address->description = request('description');
        }
        if(request('house') == null){
            $address->house = 'toppers';
        }else{
            $address->house = request('house');
        }
        if(request('street') == null){
            $address->street = 'toppers';
        }else{
            $address->street = request('street');
        }
        if(request('area') == null){
            $address->area = 'toppers';
        }else{
            $address->area = request('area');
        }
        if(request('phone') == null){
            $address->mobile = "00000000";
        }else{
            $address->mobile = request('phone');
        }
        if(request('city') == null){
            $address->city = 'toppers';
        }else{
            $address->city = request('city');
        }
        $address->save();

        return view('punch-order',compact('customer','address'));
    }

    public function punchOrder(){
        session_start();
        $customer = $_SESSION['customer'];
        $address= $_SESSION['address'];

        return view('punch-order',compact('customer','address'));
    }

    public function storeOrderAdmin(){
        session_start();
        if (isset($_SESSION['items'])){
            $order = new Order();
            $order->customer_id = $_SESSION['customer']->id;
            $order->address_id = $_SESSION['address']->id;
            $order->branch_id = Auth::user()->branch_id;
            if(request('option1') == null){
                $order->total_price = $_SESSION['total'];
                $order->delivery = false;
            }else{
                $order->total_price = $_SESSION['total'] + 50;
                $order->delivery = true;
            }
            $order->instructions = $_SESSION['ins'] ;
            $order->status = "Pending";
            $order->origin = "Branch Order";

            $order->save();
            for ($i = 0; $i < count($_SESSION['items']); $i++){
                $_SESSION['items'][$i]->order_id = $order->id;
                $_SESSION['items'][$i]->save();
            }
        }
        if (strpos($_SESSION['customer']->email, 'toppersPakistan@') !== false) {
            session_unset();
            session_destroy();
            return redirect('home');            
            // dd(strpos('toppersPakistan@02:47:21pm', 'toppersPakistan@'));
        }else{
            $data = array(
                'name' => $_SESSION['customer']->name,
                'branch' => $order->branch->name,
                'price' => $order->total_price,
                'delivery' => $order->delivery,
                'items' => $_SESSION['items'],
                'orderId' => $order->id
            );
    
            Mail::to($_SESSION['customer']->email)->send(
                new MailSender($data,'Order Placed')
            );
        }

        session_unset();
        session_destroy();
        return redirect('home');
    }
}

