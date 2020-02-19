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


//Daily Reports
    public function productWise(){
        $title  = "Daily Product Wise Report";
        $time = date('Y-m-d');
        if(Auth::user()->type == "Main Admin"){
            $orders = Order::whereDate('created_at', '=', date('Y-m-d'))->where('status','Complete')->get();
        }
        else
        {
            $orders = Order::whereDate('created_at', '=', date('Y-m-d'))->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
        }
        $products = Product::orderBy('name')->get();
        return view('report-product',compact('orders','products','title','time'));
    }
    public function productWiseDaily(){
        $title  = "Daily Product Wise Report";
        $time = request('date');
        if(Auth::user()->type == "Main Admin"){
            $orders = Order::whereDate('created_at', '=', request('date'))->where('status','Complete')->get();
        }
        else
        {
            $orders = Order::whereDate('created_at', '=', request('date'))->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
        }
        $products = Product::orderBy('name')->get();
        return view('report-product',compact('orders','products','title','time'));
    }
    public function daily(){
        $title  = "Daily Report";
        $time = date('Y-m-d');

        if(Auth::user()->type == "Main Admin"){
            $orders = Order::whereDate('created_at', '=', date('Y-m-d'))->where('status','Complete')->get();
        }
        else
        {
            $orders = Order::whereDate('created_at', '=', date('Y-m-d'))->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
        }

        $products = Product::orderBy('name')->get();
        return view('report',compact('orders','products','title','time'));
    }
    public function dailyPrint(){
        $title  = "Daily Report";
        $time = date('Y-m-d');

        if(Auth::user()->type == "Main Admin"){
            $orders = Order::whereDate('created_at', '=', date('Y-m-d'))->where('status','Complete')->get();
        }
        else
        {
            $orders = Order::whereDate('created_at', '=', date('Y-m-d'))->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
        }

        $products = Product::orderBy('name')->get();
        return view('report-preview-order',compact('orders','products','title','time'));
    }
    public function dailySpecific(){
        $title  = "Daily  Report";
        $time = request('date');
        if(Auth::user()->type == "Main Admin"){
            $orders = Order::whereDate('created_at', '=', request('date'))->where('status','Complete')->get();
        }
        else
        {
            $orders = Order::whereDate('created_at', '=', request('date'))->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
        }

        $products = Product::orderBy('name')->get();
        return view('report',compact('orders','products','title','time'));
    }

//Monthly Reports
    public function monthly(){
        $title = "Monthly Report";
        $time = date('Y-m');
        if(Auth::user()->type == "Main Admin"){
            $orders = Order::whereMonth('created_at', '=', date('m'))->whereYear('created_at', '=', date('Y'))->where('status','Complete')->get();
        }
        else
        {
            $orders = Order::whereMonth('created_at', '=', date('m'))->whereYear('created_at', '=', date('Y'))->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
        }
        $products = Product::orderBy('name')->get();
        return view('report-monthly',compact('orders','products','time','title'));
    }
    public function monthlySpecific(){
        $title = "Monthly Report";
        $time = request('date');
        $month =  date("m",strtotime(request('date')));
        $year = date("Y",strtotime(request('date')));
        if(Auth::user()->type == "Main Admin"){
            $orders = Order::whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year)->where('status','Complete')->get();
        }
        else
        {
            $orders = Order::whereMonth('created_at', '=', $month)->whereYear('created_at', '=',$year)->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
        }
        $products = Product::orderBy('name')->get();
        return view('report-monthly',compact('orders','products','time','title'));
    }
    public function monthlyProduct(){
        $title = "Monthly Product Wise Report";
        $time = date('Y-m');
        if(Auth::user()->type == "Main Admin"){
            $orders = Order::whereMonth('created_at', '=', date('m'))->whereYear('created_at', '=', date('Y'))->where('status','Complete')->get();
        }
        else
        {
            $orders = Order::whereMonth('created_at', '=', date('m'))->whereYear('created_at', '=', date('Y'))->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
        }
        $products = Product::orderBy('name')->get();
        return view('report-monthly-product',compact('orders','products','time','title'));
    }
    public function monthlyProductSpecific(){
        $title = "Monthly Product Wise Report";
        $time = request('date');
        $month =  date("m",strtotime(request('date')));
        $year = date("Y",strtotime(request('date')));
        if(Auth::user()->type == "Main Admin"){
            $orders = Order::whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year)->where('status','Complete')->get();
        }
        else
        {
            $orders = Order::whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year)->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
        }
        $products = Product::orderBy('name')->get();
        return view('report-monthly-product',compact('orders','products','time','title'));
    }
//Yearly Reports

public function yearly(){
    $title = "Yearly Report";
    $time = date('Y');
    if(Auth::user()->type == "Main Admin"){
        $orders = Order::whereYear('created_at', '=', date('Y'))->where('status','Complete')->get();
    }
    else
    {
        $orders = Order::whereYear('created_at', '=', date('Y'))->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
    }
    $products = Product::orderBy('name')->get();
    return view('report-yearly',compact('orders','products','time','title'));
}
public function yearlySpecific(){
    $title = "Yearly Report";
    $time = request('date');
    if(Auth::user()->type == "Main Admin"){
        $orders = Order::whereYear('created_at', '=', request('date'))->where('status','Complete')->get();
    }
    else
    {
        $orders = Order::whereYear('created_at', '=',request('date'))->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
    }
    $products = Product::orderBy('name')->get();
    return view('report-yearly',compact('orders','products','time','title'));
}
public function yearlyProduct(){
    $title = "Yearly Product Wise Report";
    $time = date('Y-m');
    if(Auth::user()->type == "Main Admin"){
        $orders = Order::whereYear('created_at', '=', date('Y'))->where('status','Complete')->get();
    }
    else
    {
        $orders = Order::whereYear('created_at', '=', date('Y'))->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
    }
    $products = Product::orderBy('name')->get();
    return view('report-yearly-product',compact('orders','products','time','title'));
}
public function yearlyProductSpecific(){
    $title = "Yearly Product Wise Report";
    $time = request('date');
    if(Auth::user()->type == "Main Admin"){
        $orders = Order::whereYear('created_at', '=', request('date'))->where('status','Complete')->get();
    }
    else
    {
        $orders = Order::whereYear('created_at', '=', request('date'))->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
    }
    $products = Product::orderBy('name')->get();
    return view('report-yearly-product',compact('orders','products','time','title'));
}

//Custom Reports

public function custom(){
    $title = "Custom Report";
    $timeTohere = date('Y-m-d', strtotime('+1 days', strtotime(Date('Y-m-d'))));
    $timeTo = date('Y-m-d');
    $timeFromhere = $days_ago = date('Y-m-d', strtotime('-16 days', strtotime(Date('Y-m-d'))));
    $timeFrom = $days_ago = date('Y-m-d', strtotime('-15 days', strtotime(Date('Y-m-d'))));

    if(Auth::user()->type == "Main Admin"){
        $orders = Order::whereBetween('created_at', [$timeFromhere, $timeTohere])->where('status','Complete')->get();
    }
    else
    {
        $orders = Order::whereBetween('created_at', [$timeFromhere, $timeTohere])->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
    }
    $products = Product::orderBy('name')->get();
    return view('report-custom',compact('orders','products','timeTo' ,'timeFrom','title'));
}

public function customSpecific(){
    $title = "Custom Report";
    $timeTohere = date('Y-m-d', strtotime('+1 days', strtotime(request('dateTo'))));
    $timeTo = request('dateTo');
    $timeFromhere = $days_ago = date('Y-m-d', strtotime('-1 days', strtotime(request('dateFrom'))));
    $timeFrom = request('dateFrom');

    if(Auth::user()->type == "Main Admin"){
        $orders = Order::whereBetween('created_at', [$timeFromhere, $timeTohere])->where('status','Complete')->get();
    }
    else
    {
        $orders = Order::whereBetween('created_at', [$timeFromhere, $timeTohere])->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
    }
    $products = Product::orderBy('name')->get();
    return view('report-custom',compact('orders','products','timeTo' ,'timeFrom','title'));
}
public function customProduct(){
    $title = "Custom Product Wise Report";
    $timeTohere = date('Y-m-d', strtotime('+1 days', strtotime(Date('Y-m-d'))));
    $timeTo = date('Y-m-d');
    $timeFromhere = $days_ago = date('Y-m-d', strtotime('-16 days', strtotime(Date('Y-m-d'))));
    $timeFrom = $days_ago = date('Y-m-d', strtotime('-15 days', strtotime(Date('Y-m-d'))));

    if(Auth::user()->type == "Main Admin"){
        $orders = Order::whereBetween('created_at', [$timeFromhere, $timeTohere])->where('status','Complete')->get();
    }
    else
    {
        $orders = Order::whereBetween('created_at', [$timeFromhere, $timeTohere])->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
    }
    $products = Product::orderBy('name')->get();
    return view('report-custom-product',compact('orders','products','timeTo' ,'timeFrom','title'));
}
public function customProductSpecific(){
    $title = "Custom Product Wise Report";
    $timeTohere = date('Y-m-d', strtotime('+1 days', strtotime(request('dateTo'))));
    $timeTo = request('dateTo');
    $timeFromhere = $days_ago = date('Y-m-d', strtotime('-1 days', strtotime(request('dateFrom'))));
    $timeFrom = request('dateFrom');

    if(Auth::user()->type == "Main Admin"){
        $orders = Order::whereBetween('created_at', [$timeFromhere, $timeTohere])->where('status','Complete')->get();
    }
    else
    {
        $orders = Order::whereBetween('created_at', [$timeFromhere, $timeTohere])->where('branch_id', Auth::user()->branch_id)->where('status','Complete')->get();
    }
    $products = Product::orderBy('name')->get();
    return view('report-custom-product',compact('orders','products','timeTo' ,'timeFrom','title'));
}
}
