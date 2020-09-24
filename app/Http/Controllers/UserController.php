<?php

namespace App\Http\Controllers;

use App\User;
use App\RestaurantBranch;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;

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

    public function register(){
        error_log('here');
        request()->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
		]);
		error_log(request()->image);
		if (request()->image !== 'null') {
		    $imageName = time() . '.' . request()->image->getClientOriginalExtension();
            request()->image->move(public_path('images/user'), $imageName);
		} else {
		    $imageName = null;
		}

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'type' => request('type'),
            'image' => $imageName,
            'branch_id' => request('restaurant-branch'),
        ]);
        return $user;
    }
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            $token = JWTAuth::attempt($credentials);
            if (! $token) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'Unable to Process, Try Again Later'], 500);
        }
        return response()->json(compact('token'));
    }
    public function getAuthenticatedUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['User Not Found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }
    public function logout() {
        return JWTAuth::logout();
    }

    public function changePassword($id){
        request()->validate([
            'oldPassword' => ['required', 'string'],
            'newPassword' => ['required', 'string'],
    	]);
  	    $user = User::where('id',$id)->first();
        if(Hash::check(request('oldPassword'),$user->password)){
        	$user->update([
   				'password'  => bcrypt(request('newPassword')),
   			]);
   			return $user;
    	}else{
            return response()->json(['message' => 'Invalid Credentials'], 401);
    	}
    }
    public function update($id) {
        $user = User::where('id',$id)->first();
        if(request('image') != 'null'){
            if ($user->image != null) {
                if (file_exists(public_path('images/user/').$user->image)) {
                    unlink(public_path('images/user/').$user->image);
                }
            }
            $imageName = time().'.'.request()->image->getClientOriginalExtension();
            request()->image->move(public_path('images/user'), $imageName);
            error_log($imageName);
            $user =  User::where('id',$id)->update([
                'name'  => request('name'),
                'email'  => request('email'),
                'type' => request('type'),
                'branch_id' => request('branchId'),
                'image' => $imageName
            ]);
        }
        else{
            $user = User::where('id',$id)->update([
                'name'  => request('name'),
                'email'  => request('email'),
                'type' => request('type'),
                'branch_id' => request('branchId'),
            ]);
        }
        return $user;
    }
}
