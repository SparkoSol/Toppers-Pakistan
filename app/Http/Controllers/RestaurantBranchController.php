<?php

namespace App\Http\Controllers;

use App\Restaurant;
use App\RestaurantBranch;
use App\User;
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
    public function apiIndex()
    {
        return RestaurantBranch::all();
    }
    public function managers($id) {
        return User::where('branch_id',$id)->get();
    }
    public function apiIndexById($id)
    {
        return RestaurantBranch::where('id', $id)->first();
    }

    public function storeBranch() {
        $branch = new RestaurantBranch();
        $branch->name = request('name');
        $branch->email = request('email');
        $branch->address = request('address');
        $branch->phone = request('phone');
        $branch->restaurant_id = request('restaurant');
        $imageName = time().'.'.request()->image->getClientOriginalExtension();
        request()->image->move(public_path('images/branch'), $imageName);
        $branch->image = $imageName;
        $branch->save();
        return $branch;
    }

    public function updateBranch($id)
    {
        $name = request('name');
        $email = request('email');
        $address = request('address');
        $phone = request('phone');
        $restaurant_id = request('restaurant');
        if(request('image') != null){
            $branch = RestaurantBranch::where('id',$id)->first();
            if (file_exists(public_path('images/branch/').$branch->image)) {
                unlink(public_path('images/branch/').$branch->image);
            }
            $imageName = time().'.'.request()->image->getClientOriginalExtension();
            request()->image->move(public_path('images/branch'), $imageName);
            $branch = RestaurantBranch::where('id', $id)
                ->update(['name'  => $name,
                         'restaurant_id' => $restaurant_id,
                         'email'  => $email,
                         'address'=> $address,
                         'phone'  => $phone,
                         'image' => $imageName
                         ]);
        }
        else{
            $branch = RestaurantBranch::where('id', $id)
            ->update(['name'  => $name,
                     'restaurant_id' => $restaurant_id,
                     'email'  => $email,
                     'address'=> $address,
                     'phone'  => $phone
                     ]);
        }
        return $branch;
    }
    public function deleteBranch($id)
    {
        try {
            error_log('here');
            $branch = RestaurantBranch::where('id', $id)->first();
            RestaurantBranch::where('id', $id)
                ->delete();
            if ($branch->image != null) {
                if (file_exists(public_path('images/branch/').$branch->image)) {
                    unlink(public_path('images/branch/').$branch->image);
                }
            }
        } catch(\Throwable $e)  {
            return response()->json([
                'error' => 'Cannot delete Branch.',
            ],200);
        }
    }
}
