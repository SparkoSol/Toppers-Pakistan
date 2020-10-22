<?php

namespace App\Http\Controllers;

use App\PaymentIn;
use App\CustomerTransaction;
use App\Customer;
use App\PaymentInCustomerTransaction;
use Illuminate\Http\Request;

class PaymentInController extends Controller
{
    public function getReceiptNumber() {
        $receipt = PaymentIn::latest()->first();
        if($receipt) {
            return $receipt->receipt_id + 1;
        } else {
            return 1;
        }
    }
    public function getPaymentIn() {
        return PaymentIn::with('customer')->get();
    }
    public function customFilter() {
        return PaymentIn::whereBetween('receipt_date', [request('from'), request('to')])->with('customer')->get();
    }
    public function getPaymentInById($id) {
        return PaymentIn::where('id',$id)->with('customer')->first();
    }
    public function store() {
        // create payment in
        $paymentIn = new PaymentIn();
        $paymentIn->customer_id = request('customer_id');
        $paymentIn->branch_id = request('branch_id');
        $paymentIn->receipt_id = request('receipt_id');
        $paymentIn->receipt_date = request('receipt_date');
        $paymentIn->received = request('received');
        $paymentIn->description = request('description');
        $paymentIn->save();
        error_log($paymentIn);
        // create payment in transaction in customer
        $customerTransaction = new CustomerTransaction();
        $customerTransaction->customer_id = request('customer_id');
        $customerTransaction->value = request('received');
        $customerTransaction->balance = - request('received');
        $customerTransaction->action_type = 4;
        $customerTransaction->date = request('receipt_date');
        $customerTransaction->status = 'Paid';
        $customerTransaction->save();
        error_log($customerTransaction);

        // create payment in customer transaction

        $paymentInTransaction = new PaymentInCustomerTransaction();
        $paymentInTransaction->payment_in_id = $paymentIn->id;
        $paymentInTransaction->transaction_id = $customerTransaction->id;
        $paymentInTransaction->save();

        error_log($paymentInTransaction);
        // update customer balance
        $customerTransactions = CustomerTransaction::where('customer_id', request('customer_id'))->get();
        $totalBalance = 0;
        foreach($customerTransactions as $value) {
            $totalBalance = $totalBalance + $value->balance;
        }
        $customerBalance = $totalBalance;
        Customer::where('id', request('customer_id'))->update([
            'balance' => $customerBalance
        ]);
        error_log($customerBalance);
        return $paymentIn;
    }
    public function update($id) {
        try {
        // update payment in
        PaymentIn::where('id', $id)->update([
            'received' => request('received'),
            'description' => request('description')
        ]);
        // update payment in transaction in customer
        $transaction = PaymentInCustomerTransaction::where('payment_in_id', $id)->first();
        $customerTransaction = CustomerTransaction::where('id', $transaction->transaction_id)->first();
        CustomerTransaction::where('id', $transaction->transaction_id)->update([
            'value' => request('received'),
            'balance' => - request('received')
        ]);
        // update customer balance
        $customerTransactions = CustomerTransaction::where('customer_id', $customerTransaction->customer_id)->get();
        $totalBalance = 0;
        foreach($customerTransactions as $value) {
            $totalBalance = $totalBalance + $value->balance;
        }
        $customerBalance = $totalBalance;
        Customer::where('id', request('customer_id'))->update([
            'balance' => $customerBalance
        ]);
        return PaymentIn::where('id', $id)->first();
        } catch (\Throwable $e) {
            error_log($e);
        }
    }

    public function delete($id) {
        try {
            PaymentIn::where('id', $id)->delete();
            return response()->json([
                'message' => 'Deleted Successfully'
            ],200);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Cannot delete Payment In'
            ],200);
        }
    }
}
