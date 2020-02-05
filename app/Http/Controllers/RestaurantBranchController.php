<?php

namespace App\Http\Controllers;

use App\Restaurant;
use App\RestaurantBranch;
use Illuminate\Http\Request;

class RestaurantBranchController extends Controller
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
        $restaurants = Restaurant::all();
        $branches =RestaurantBranch::all();
        return view('branch',compact('restaurants','branches'));
    }

    public function addBranch()
    {
        return view('restaurant.branch.add-branch')->with('restaurants', Restaurant::all());
    }

    public function storeBranch() {
        $branch = new RestaurantBranch();
        $branch->name = request('name');
        $branch->email = request('email');
        $branch->address = request('address');
        $branch->phone = request('phone');      
        $branch->restaurant_id = request('restaurant');      
        $branch->save();
        return redirect('/branch');
    }

    public function editBranch($id)
    {
        $branch = RestaurantBranch::where('id', $id)->first();
        $restaurants = Restaurant::all();
        return view('restaurant.branch.edit-branch',compact('branch','restaurants'));
    }
    
    public function updateBranch($id)
    {
        $name = request('name');
        $email = request('email');
        $address = request('address');
        $phone = request('phone');      
        $restaurant_id = request('restaurant');   

        RestaurantBranch::where('id', $id)
                ->update(['name'  => $name,
                         'restaurant_id' => $restaurant_id,
                         'email'  => $email,
                         'address'=> $address,
                         'phone'  => $phone
                         ]);
        
        return redirect('/branch');
        
    }
    public function deleteBranch($id)
    {
        RestaurantBranch::where('id', $id)
                ->delete();
        
        return redirect('/branch');
    }

}
