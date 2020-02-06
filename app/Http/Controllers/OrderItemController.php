<?php

namespace App\Http\Controllers;


use App\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
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
        return OrderItem::all();
    }

    public function apiIndexById($id)
    {
        return OrderItem::where('id', $id)->first();
    }
}
