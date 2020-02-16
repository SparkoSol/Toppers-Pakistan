<?php

namespace App\Http\Controllers;

use App\Order;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReportsController extends Controller
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

    public function index(){
        return view('report');
    }
    public function productWise(){
        if(Auth::user()->type == "Main Admin"){
            $orders = Order::where('status','Complete')->get();
        }
        else
        {
            $orders = Order::where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
        }
        // $orders =  Order::whereDate('created_at', '=', date('Y-m-d'))->where('status','Complete')
        //     ->get();

        // dd($orders);
        // $products = Product::all();
        $products = Product::orderBy('name')->get();
        // dd($orders, $products);
        return view('report-product',compact('orders','products'));
    }
    public function yearly(){
        if(Auth::user()->type == "Main Admin"){
            $orders = Order::where('status','Complete')->get();
        }
        else
        {
            $orders = Order::where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
        }
        $orders = Order::all();
        // $orders =  Order::whereDate('created_at', '=', date('Y-m-d'))->where('status','Complete')
        //     ->get();

        // dd($orders);
        // $products = Product::all();
        $products = Product::orderBy('name')->get();
        // dd($orders, $products);
        return view('report-product',compact('orders','products'));
    }
    public function daily(){
        if(Auth::user()->type == "Main Admin"){
            $orders = Order::whereDate('created_at', '=', date('Y-m-d'))->where('status','Complete')->get();
        }
        else
        {
            $orders = Order::whereDate('created_at', '=', date('Y-m-d'))->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
        }

        $products = Product::orderBy('name')->get();
        return view('report',compact('orders','products'));
    }


    public function monthly(){
        if(Auth::user()->type == "Main Admin"){
            $orders = Order::where('status','Complete')->get();
        }
        else
        {
            $orders = Order::where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
        }
        $orders = Order::all();
        $products = Product::orderBy('name')->get();
        return view('report-product',compact('orders','products'));
    }
}
