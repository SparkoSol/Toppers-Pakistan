<?php

namespace App\Http\Controllers;

use App\PaymentOut;
use App\SupplierTransaction;
use App\Supplier;
use App\PaymentOutSupplierTransaction;
use Illuminate\Http\Request;

class PaymentOutController extends Controller
{
    public function getReceiptNumber() {
        $receipt = PaymentOut::latest()->first();
        if($receipt) {
            return $receipt->receipt_id + 1;
        } else {
            return 1;
        }
    }
    public function getPaymentOut($id) {
        if($id === '-1') {
            return PaymentOut::with('supplier')->with('branch')->get();
        } else {
            return PaymentOut::where('branch_id',$id)->with('branch')->with('supplier')->get();
        }
    }
    public function customFilter($id) {
        if($id === '-1') {
            return PaymentOut::whereBetween('receipt_date', [request('from'), request('to')])->with('supplier')->with('branch')->get();
        } else {
            return PaymentOut::where('branch_id',$id)->whereBetween('receipt_date', [request('from'), request('to')])->with('supplier')->with('branch')->get();
        }
    }
    public function getPaymentOutById($id) {
        return PaymentOut::where('id',$id)->with('supplier')->first();
    }
    public function store() {
        // create payment in
        $paymentOut = new PaymentOut();
        $paymentOut->supplier_id = request('supplier_id');
        $paymentOut->branch_id = request('branch_id');
        $paymentOut->receipt_id = request('receipt_id');
        $paymentOut->receipt_date = request('receipt_date');
        $paymentOut->received = request('received');
        $paymentOut->description = request('description');
        $paymentOut->save();
        error_log($paymentOut);
        // create payment in transaction in supplier
        $supplierTransaction = new SupplierTransaction();
        $supplierTransaction->supplier_id = request('supplier_id');
        $supplierTransaction->value = request('received');
        $supplierTransaction->balance = request('received');
        $supplierTransaction->action_type = 7;
        $supplierTransaction->date = request('receipt_date');
        $supplierTransaction->status = 'Paid';
        $supplierTransaction->save();
        error_log($supplierTransaction);

        // create payment in supplier transaction

        $paymentOutTransaction = new PaymentOutSupplierTransaction();
        $paymentOutTransaction->payment_out_id = $paymentOut->id;
        $paymentOutTransaction->transaction_id = $supplierTransaction->id;
        $paymentOutTransaction->save();

        error_log($paymentOutTransaction);
        // update supplier balance
        $supplierTransactions = SupplierTransaction::where('supplier_id', request('supplier_id'))->get();
        $totalBalance = 0;
        foreach($supplierTransactions as $value) {
            $totalBalance = $totalBalance + $value->balance;
        }
        $supplierBalance = $totalBalance;
        Supplier::where('id', request('supplier_id'))->update([
            'balance' => $supplierBalance
        ]);
        error_log($supplierBalance);
        return $paymentOut;
    }
    public function update($id) {
        try {
        // update payment in
        paymentOut::where('id', $id)->update([
            'received' => request('received'),
            'description' => request('description')
        ]);
        // update payment in transaction in customer
        $transaction = PaymentOutSupplierTransaction::where('payment_out_id', $id)->first();
        $supplierTransaction = SupplierTransaction::where('id', $transaction->transaction_id)->first();
        SupplierTransaction::where('id', $transaction->transaction_id)->update([
            'value' => request('received'),
            'balance' => request('received')
        ]);
        // update customer balance
        $supplierTransactions = SupplierTransaction::where('supplier_id', $supplierTransaction->supplier_id)->get();
        $totalBalance = 0;
        foreach($supplierTransactions as $value) {
            $totalBalance = $totalBalance + $value->balance;
        }
        $supplierBalance = $totalBalance;
        Supplier::where('id', request('supplier_id'))->update([
            'balance' => $supplierBalance
        ]);
        return PaymentOut::where('id', $id)->first();
        } catch (\Throwable $e) {
            error_log($e);
        }
    }
    public function delete($id) {
        try {
            PaymentOut::where('id', $id)->delete();
            return response()->json([
                'message' => 'Deleted Successfully'
            ],200);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Cannot delete Payment Out'
            ],200);
        }
    }
}
