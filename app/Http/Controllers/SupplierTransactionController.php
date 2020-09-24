<?php

namespace App\Http\Controllers;

use App\SupplierTransaction;
use Illuminate\Http\Request;

class SupplierTransactionController extends Controller
{
    public function getBySupplier($id) {
        return SupplierTransaction::where('supplier_id',$id)->get();
    }
}
