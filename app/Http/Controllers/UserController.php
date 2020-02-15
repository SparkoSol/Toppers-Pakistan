<?php

namespace App\Http\Controllers;

use App\User;
use App\RestaurantBranch;
use Illuminate\Http\Request;

class UserController extends Controller
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


    public function registerAdmin(){
        $branches = RestaurantBranch::all();
        return view('auth.register',compact('branches'));
    }

    public function register(){

        request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
		]);

        User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'type' => request('type'),
            'branch_id' => request('restaurant-branch'),
        ]);

        return redirect('/home');
    }
}
