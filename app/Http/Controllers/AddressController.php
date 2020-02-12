<?php

namespace App\Http\Controllers;

use App\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
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


    public function storeAddress()
    {
        $address = new Address();
        $address->description = request('description');
        $address->house = request('house');
        $address->street = request('street');
        $address->area = request('area');
        $address->city = request('city');     
        $address->mobile = request('mobile');     
        $address->customer_id = request('customer_id');
        $address->save();

        return $address;
    }

    public function getAddressByCustomerId($id) {
        return Address::where('customer_id', $id)->get();
    }


    public function deleteAddress($id)
    {
        return Address::where('id', $id)
                ->delete();
    }

}
