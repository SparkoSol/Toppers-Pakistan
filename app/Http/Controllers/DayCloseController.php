<?php

namespace App\Http\Controllers;

use App\DayClose;
use Illuminate\Http\Request;

class DayCloseController extends Controller
{
    public function insert($id) {
        try {
            $dayClose = new DayClose();
            $dayClose->cash = request('cash');
            $dayClose->expense = request('expense');
            $dayClose->purchase = request('purchase');
            $dayClose->sale = request('sale');
            $dayClose->stock = request('stock');
            $dayClose->stockValue = request('stockValue');
            $dayClose->toPay = request('toPay');
            $dayClose->toReceive = request('toReceive');
            $dayClose->date = date('Y-m-d');
            if ($id > 0) {
                $dayClose->branch_id = $id;
            }
            $dayClose->save();
        } catch (\Throwable $e) {
            error_log($e);
        }
    }
    public function get($id) {
        if ($id > 0) {
            return DayClose::where('branch_id',$id)->with('branch')->get();
        } else {
            return DayClose::with('branch')->get();
        }
    }
}
