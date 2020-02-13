<?php

namespace App\Http\Controllers;

use App\Customer;
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
        return Customer::all();
    }

    public function apiIndexById($id)
    {
        return Customer::where('id', $id)->first();
    }

	public function index() {
        return view('customer')->with('customers', Customer::all());
	}

	public function address($id) {
        return view('customer.customer-addresses')->with('addresses', Customer::where('id',$id)->first()->addresses);
	}

	public function getOrders($id){
		return Customer::where('id',$id)->first()->orders;
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

		$allCustomers = Customer::all();


		foreach ($allCustomers as $customerOne) {
			if($customerOne->email == request('email')){
				return "error";
			}
		}
		request()->validate([
			'email'=>'required',
			'name'=>'required',
            'password'=>'required',
            'phone'=>'required'
		]);

		$customer = new Customer();
		$customer->email =    request('email');
        $customer->name =     request('name');
        $customer->phone =    request('phone');
		$customer->password = bcrypt(request('password'));
		$customer->save();

		$this->login();
	}

	public function login(){
		
		request()->validate([
			'email'=>'required',
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

}
