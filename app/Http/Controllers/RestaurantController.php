<?php

namespace App\Http\Controllers;

use App\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
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
        return view('restaurant')->with('restaurants', Restaurant::all());
    }

    public function addRestaurant()
    {
        return view('restaurant.add-restaurant');
    }

    public function storeRestaurant()
    {
        $restaurant = new Restaurant();
        $restaurant->name = request('name');
        $restaurant->email = request('email');
        $restaurant->address = request('address');
        $restaurant->phone = request('phone');      
        $restaurant->save();
        return redirect('/restaurant');
    }

    public function editRestaurant($id)
    {
        $restaurant = Restaurant::where('id', $id)->first();
        return view('restaurant.edit-restaurant',compact('restaurant'));
    }

    public function updateRestaurant($id)
    {
        $name = request('name');
        $email = request('email');
        $address = request('address');
        $phone = request('phone');      

        Restaurant::where('id', $id)
                ->update(['name'  => $name,
                         'email'  => $email,
                         'address'=> $address,
                         'phone'  => $phone
                         ]);
        
        return redirect('/restaurant');
        
    }

    public function deleteRestaurant($id)
    {
        Restaurant::where('id', $id)
                ->delete();
        
        return redirect('/restaurant');
    }
}
