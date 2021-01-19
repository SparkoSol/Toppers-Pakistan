<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerTransaction;
use App\Order;
use App\PaymentIn;
use App\PaymentInCustomerTransaction;
use App\SaleOrder;
use App\SaleOrderCustomerTransaction;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;


class CustomerController extends Controller
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
        error_log('we here');
        try {
            return Customer::get();
        } catch (Exception $e) {
            error_log($e);
        }
    }

    public function customers($id) {
        try {
            if ($id > 0) {
                $customers = Customer::all();
                $balance = 0;
                foreach ($customers as $customer) {
                    $saleOrders = SaleOrder::whereNull('return_status')->where('customer_id', $customer->id)->where('branch_id',$id)->get();
                    foreach ($saleOrders as $saleOrder) {
                        $saleOrderCustomerTransactions = SaleOrderCustomerTransaction::where('sale_order_id',$saleOrder->id)->get();
                        foreach ($saleOrderCustomerTransactions as $saleOrderCustomerTransaction) {
                            $transaction = CustomerTransaction::where('id',$saleOrderCustomerTransaction->transaction_id)->first();
                            $balance = $balance + $transaction->balance;
                        }
                    }
                    $paymentIns = PaymentIn::where('customer_id',$customer->id)->where('branch_id',$id)->get();
                    foreach ($paymentIns as $paymentIn) {
                        $paymentInCustomerTransactions = PaymentInCustomerTransaction::where('payment_in_id',$paymentIn->id)->get();
                        foreach ($paymentInCustomerTransactions as $paymentInCustomerTransaction) {
                            $transaction = CustomerTransaction::where('id',$paymentInCustomerTransaction->transaction_id)->first();
                            $balance = $balance + $transaction->balance;
                        }
                    }
                    $customer->balance = $balance;
                }
                return $customers;
            } else {
                return Customer::all();
            }
        } catch (Exception $e) {
            error_log($e);
        }
    }

    public function apiIndexById($id)
    {
        return Customer::where('id', $id)->first();
	}


	public function apiCheckUserExists(){
		$allCustomers = Customer::all();
		foreach ($allCustomers as $customerOne) {
			if($customerOne->email == request('email')){
				return "true";
			}
		}
		return "false";
	}

	public function index() {
        return view('customer')->with('customers', Customer::all());
	}

	public function address($id) {
        return view('customer.customer-addresses')->with('addresses', Customer::where('id',$id)->first()->addresses);
	}

	public function getOrders($id){
		$orders = Order::where('customer_id',$id)->orderBy('id','desc')->get();
		return $orders;
	}

	public function changePassword($id){
		$customer = Customer::where('id',$id)->first();
		if(Hash::check(request('oldpassword'),$customer->password)){
			$customer->update([
				'password'  => bcrypt(request('password')),
			]);
			echo($customer);
		}else{
			return "error";
		}
	}

    public function register(){
        error_log('register');
		$allCustomers = Customer::all();
		foreach ($allCustomers as $customerOne) {
			if($customerOne->email == request('email')){
				return "error";
			}
		}
		request()->validate([
			'name'=>'required'
		]);
		$customer = new Customer();
		$customer->email =    request('email');
        $customer->name =     request('name');
        $customer->phone =    request('phone');
		$customer->password = bcrypt(request('password'));
		$customer->other = request('other');
		$customer->save();
		$this->login();
	}

	public function login(){
		request()->validate([
			'password'=>'required'
		]);
		$customer= Customer::where('email',request('email'))->first();
		if(!$customer){
			return "error";
		}
		if(Hash::check(request('password'),$customer->password)){
			echo($customer);
		}
		else{
			return "error";
		}
	}

	public function store() {
	    try {
            request()->validate([
                'name' => ['required', 'string'],
            ]);
            $customer = new Customer();
            $customer->email =    request('email');
            $customer->name =     request('name');
            $customer->phone =    request('phone');
            $customer->password = bcrypt(request('password'));
            $customer->save();
            return $customer;
	    } catch (\Throwable $e) {
    	    return response()->json(['message' => $e], 401);
	    }
	}
	public function update($id) {
        $customer = Customer::where('id',$id)->update([
            'name'  => request('name'),
            'email' => request('email'),
            'phone' => request('phone')
        ]);

        return $customer;
	}
	public function delete($id) {
        try {
            error_log('here');
            Customer::where('id', $id)
                    ->delete();
        } catch(\Throwable $e)  {
            return response()->json([
                'error' => 'Cannot delete Customer.',
            ],200);
        }
    }
 }
