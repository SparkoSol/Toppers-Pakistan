<?php

namespace App\Http\Controllers;

use App\Supplier;
use App\PurchaseOrder;
use App\PurchaseOrderSupplierTransaction;
use App\PaymentOut;
use App\PaymentOutSupplierTransaction;
use App\SupplierTransaction;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class SupplierController extends Controller
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

    public function apiIndex($id)
    {
        try {
            if ($id > 0) {
                $suppliers = Supplier::all();
                $balance = 0;
                foreach ($suppliers as $supplier) {
                    $purchaseOrders = PurchaseOrder::whereNull('return_status')->where('supplier_id', $supplier->id)->where('branch_id',$id)->get();
                    foreach ($purchaseOrders as $purchaseOrder) {
                        $purchaseOrderSupplierTransactions = PurchaseOrderSupplierTransaction::where('purchase_order_id',$purchaseOrder->id)->get();
                        foreach ($purchaseOrderSupplierTransactions as $purchaseOrderSupplierTransaction) {
                            $transaction = SupplierTransaction::where('id',$purchaseOrderSupplierTransaction->transaction_id)->first();
                            $balance = $balance + $transaction->balance;
                        }
                    }
                    $paymentOuts = PaymentOut::where('supplier_id',$supplier->id)->where('branch_id',$id)->get();
                    foreach ($paymentOuts as $paymentOut) {
                        $paymentOutSupplierTransactions = PaymentOutSupplierTransaction::where('payment_out_id',$paymentOut->id)->get();
                        foreach ($paymentOutSupplierTransactions as $paymentOutSupplierTransaction) {
                            $transaction = SupplierTransaction::where('id',$paymentOutSupplierTransaction->transaction_id)->first();
                            $balance = $balance + $transaction->balance;
                        }
                    }
                    $supplier->balance = $balance;
                }
                return $suppliers;
            } else {
                return Supplier::all();
            }
        } catch (Exception $e) {
            error_log($e);
        }
    }

    public function apiIndexById($id)
    {
        return Supplier::where('id', $id)->first();
	}

	public function store() {
        request()->validate([
            'name' => ['required', 'string'],
        ]);
    	$supplier = new Supplier();
    	$supplier->email =    request('email');
        $supplier->name =     request('name');
        $supplier->phone =    request('phone');
    	$supplier->save();
    	return $supplier;
	}
	public function update($id) {
        $supplier = Supplier::where('id',$id)->update([
            'name'  => request('name'),
            'email' => request('email'),
            'phone' => request('phone')
        ]);

        return $supplier;
	}
	public function delete($id) {
        try {
            error_log('here');
            Supplier::where('id', $id)
                    ->delete();
        } catch(\Throwable $e)  {
            return response()->json([
                'error' => 'Cannot delete Supplier.',
            ],200);
        }
    }
}
