<?php

namespace App\Http\Controllers;

use App\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function get(){
        return Expense::with('branch')->get();
    }
    public function getById($id) {
        return Expense::where('id',$id)->with('branch')->first();
    }
    public function getByBranch($id) {
        return Expense::where('branch_id',$id)->with('branch')->get();
    }
    public function store() {
       error_log(request('balance'));
       $expense = new Expense();
       $expense->title = request('title');
       $expense->description = request('description');
       $expense->branch_id = request('branch_id');
       $expense->total = request('total');
       $expense->paid = request('paid');
       $expense->type = request('type');
       $expense->balance = request('balance');
       $expense->save();
    }
    public function update($id) {
           error_log(request('balance'));
        return Expense::where('id',$id)->update([
            'title' => request('title'),
            'description' => request('description'),
            'branch_id' => request('branch_id'),
            'total' => request('total'),
            'paid' => request('paid'),
            'balance' => request('balance'),
            'type' => request('type')
        ]);
    }
    public function delete($id) {
        Expense::where('id',$id)->delete();
        return response()->json([
            'message' => 'Deleted Successfully'
        ],200);
     }
}
