<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Http\Controllers\Client;
use Illuminate\Http\Request;

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

    public function register(Request $request){

        $request->validate([
			'email'=>'required',
			'name'=>'required',
            'password'=>'required',
            'phone'=>'required',
            'address'=>'required'
		]);

		$customer = new Customer();
		$customer->email =    $request->email;
        $customer->name =     $request->name;
        $customer->phone =    $request->phone;
        $customer->address =  $request->address;
		$customer->password = bcrypt($request->password);
		$customer->save();


		$http = new Client;

		$response = $http->post(url('oauth/token'), [
		    'form_params' => [
		        'grant_type' => 'password',
		        'client_id' => '1',
		        'client_secret' => 'vmB8vMcV6ayavXMmjkE0U1htp8g81dfS0AvRWjEG',
		        'username' => $request->email,
                'password' => $request->password,
                'theNewProvider'=>'api',
		        'scope' => '',
		    ],
		]);


		// return response(['auth'=>json_decode((string) $response->getBody(), true),'customer'=>$customer]);
		
	}

	public function login(Request $request){
		
		$request->validate([
			'email'=>'required',
			'password'=>'required'
		]);

		$customer= Customer::where('email',$request->email)->first();

		if(!$customer){
			return response(['status'=>'error','message'=>'customer not found']);
		}

		if(Hash::check($request->password, $customer->password)){

				$http = new Client;

			$response = $http->post(url('oauth/token'), [
				'form_params' => [
					'grant_type' => 'password',
					'client_id' => '2',
					'client_secret' => 'cK13jXYdcIjETs7yKO8wpkvFGoZhN6WgEex9eCbB',
					'username' => $request->email,
					'password' => $request->password,
					'scope' => '',
				],
			]);
			return response(['auth' => json_decode((string)$response->getBody(), true), 'customer' => $customer]);

		
		}else{
			return response(['message'=>'password not match','status'=>'error']);
		}


	}

	public function refreshToken() {

		$http = new Client;

		$response = $http->post(url('oauth/token'), [
		    'form_params' => [
		        'grant_type' => 'refresh_token',
		        'refresh_token' => request('refresh_token'),
		        'client_id' => '2',
		        'client_secret' => 'cK13jXYdcIjETs7yKO8wpkvFGoZhN6WgEex9eCbB',
		        'scope' => '',
		    ],
		]);

		return json_decode((string) $response->getBody(), true);

	}

}
