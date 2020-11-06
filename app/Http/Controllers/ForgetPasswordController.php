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
        try {
            $customer = Customer::where('email',request('email'))->first();

            if($customer){
                if($customer->other){
                    return "false";
                }else{
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
                return "true";
                }
            }else{
                return "false";
            }
        } catch (\Throwable $e) {
            error_log($e);
        }
    }

    public function get($id) {
       return ForgetPassword::where('id',$id)->first();
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
        try {
            $validatedData = request()->validate([
                'password' => ['required']
            ]);
            $forgetPass = ForgetPassword::where('id',request('id'))->first();
            $customer = Customer::where('id',$forgetPass->customer_id)->first();
            if($customer){
                $customer->update([
                    'password'  => bcrypt(request('password')),
                ]);

                ForgetPassword::where('id',$forgetPass->id)->delete();
                return "true";
            }
            return "false";
        } catch(\Throwable $e) {
            error_log($e);
        }
    }
}
