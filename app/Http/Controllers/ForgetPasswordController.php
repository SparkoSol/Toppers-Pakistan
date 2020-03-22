<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Mail\ForgetMail;
use App\ForgetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgetPasswordController extends Controller
{
    public function index(){
        $customer = Customer::where('email',request('email'))->first();

        if($customer){
            $token = bcrypt($customer->name);
            $forgetPass = new ForgetPassword();
            $forgetPass->customer_id = $customer->id;
            $forgetPass->token = $token;

            $forgetPass->save();
            $data = array(
                'id' =>  $forgetPass->id,
                'name' => $customer->name,
                'token' => $token
            );
    
            Mail::to($customer->email)->send(
                new ForgetMail($data,'Reset Password')
            );
        }else{
            return null;
        }
    }

    public function changePass(){
        session_start();
        $forgetPass = ForgetPassword::where('id',request('id'))->first();
        if($forgetPass){
            $_SESSION['forgetPass'] = $forgetPass; 
            if($forgetPass->token == request('token')){
                return view('reset-password');
            }else{
                return view('problem');
            }
        }else{
            return view('problem');
        }
    }

    public function resetPassword(){
        session_start();
        $validatedData = request()->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);
        $forgetPass = $_SESSION['forgetPass'];
        $customer = Customer::where('id',$forgetPass->customer_id)->first();
        if($customer){
            $customer->update([
                'password'  => bcrypt(request('password')),
            ]);
    
            ForgetPassword::where('id',$forgetPass->id)->delete();
            session_destroy();
            return view('password-changed');
        }
        else{
            session_destroy();
            return view('problem');
        }
    }
}
