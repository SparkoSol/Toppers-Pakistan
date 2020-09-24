<?php

namespace App\Http\Controllers;

use App\CustomerTransaction;
use Illuminate\Http\Request;

class CustomerTransactionController extends Controller
{
    public function getByCustomer($id) {
        return CustomerTransaction::where('customer_id',$id)->get();
    }
}
