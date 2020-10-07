<?php

namespace App\Http\Controllers;

use App\SaleReturn;
use App\SaleOrder;
use App\SaleOrderItem;
use App\SaleOrderProductTransaction;
use App\SaleOrderCustomerTransaction;
use App\SaleReturnCustomerTransaction;
use App\SaleReturnProductTransaction;
use App\ProductHistory;
use App\Product;
use App\Customer;
use App\CustomerTransaction;
use Illuminate\Http\Request;

class SaleReturnController extends Controller
{
    public function getInvoiceNumber() {
        $invoice = SaleReturn::latest()->first();
        if($invoice) {
            return $invoice->invoice_id + 1;
        } else {
            return 1;
        }
    }
    public function getSaleReturn() {
        return SaleReturn::with('customer')->get();
    }
    public function getSaleReturnById($id) {
        return SaleReturn::where('id', $id)->with('customer')->with('SaleOrder.branch')->first();
    }
    public function customFilter() {
        return SaleReturn::whereBetween('invoice_date', [request('from'), request('to')])->with('customer')->get();
    }
    public function store() {
        try {
            $saleOrder = SaleOrder::where('id', request('sale_order_id'))->first();
            // create sale return
            $saleReturn = new SaleReturn();
            $saleReturn->invoice_id = request('invoiceId');
            $saleReturn->invoice_date = request('invoiceDate');
            $saleReturn->customer_id = $saleOrder->customer_id;
            $saleReturn->branch_id = $saleOrder->branch_id;
            $saleReturn->sale_order_id = $saleOrder->id;
            $saleReturn->payment_type = request('paymentType');
            $saleReturn->total = $saleOrder->amount;
            $saleReturn->paid = request('paid');
            $saleReturn->balance = request('balance');
            $saleReturn->save();

            // get product transaction and duplicate with sale return
            $saleOrderProductTransactions = SaleOrderProductTransaction::where('sale_order_id', $saleOrder->id)->get();
            foreach($saleOrderProductTransactions as $saleOrderProductTransaction) {
                $productTransaction = ProductHistory::where('id', $saleOrderProductTransaction->transaction_id)->first();
                $newProductTransaction = new ProductHistory();
                $newProductTransaction->name = $productTransaction->name;
                $newProductTransaction->product_id = $productTransaction->product_id;
                $newProductTransaction->quantity = $productTransaction->quantity;
                $newProductTransaction->value = $productTransaction->value;
                $newProductTransaction->action_type = 5;
                $newProductTransaction->date = request('invoiceDate');
                if (request('balance')) {
                    if (request('balance') != 0)
                        $newProductTransaction->status = 'Partial';
                    else if (request('balance') == 0)
                        $newProductTransaction->status = 'Paid';
                    else
                        $newProductTransaction->status = 'Unpaid';
                } else {
                        $newProductTransaction->status = 'Paid';
                }
                $newProductTransaction->save();
                $saleReturnProductTransaction = new SaleReturnProductTransaction();
                $saleReturnProductTransaction->sale_return_id = $saleReturn->id;
                $saleReturnProductTransaction->transaction_id = $newProductTransaction->id;
                $saleReturnProductTransaction->save();
                $productHistories = ProductHistory::where('product_id', $newProductTransaction->product_id)->get();
                error_log($productHistories);
                $quantity = 0;
                $value = 0;
                foreach ($productHistories as $item) {
                error_log($item);
                    if ($item->action_type === 1) {
                        $quantity = $quantity - $item->quantity;
                        $value = $value - ($item->quantity * $item->value);
                    } else if ($item->action_type === 3) {
                        $quantity = $quantity - $item->quantity;
                        $value = $value - ($item->quantity * $item->value);
                    } else {
                        $quantity = $quantity + $item->quantity;
                        $value = $value + ($item->quantity * $item->value);
                    }
                }
                Product::where('id', $newProductTransaction->product_id)->update([
                    'stock' => $quantity,
                    'stock_value' => $value
                ]);
            }
            // create customer transactions
            $saleOrderCustomerTransaction = SaleOrderCustomerTransaction::where('sale_order_id', $saleOrder->id)->first();
            $customerTransaction = CustomerTransaction::where('id', $saleOrderCustomerTransaction->transaction_id)->first();
            $newCustomerTransaction = new CustomerTransaction();
            $newCustomerTransaction->customer_id = $customerTransaction->customer_id;
            $newCustomerTransaction->quantity = $customerTransaction->quantity;
            $newCustomerTransaction->value = $customerTransaction->value;
            $newCustomerTransaction->balance = - request('balance');
            $newCustomerTransaction->action_type = 5;
            $newCustomerTransaction->date = request('invoiceDate');
            if (request('balance')) {
                if (request('balance') != 0)
                    $newCustomerTransaction->status = 'Partial';
                else if (request('balance') == 0)
                    $newCustomerTransaction->status = 'Paid';
                else
                    $newCustomerTransaction->status = 'Unpaid';
            } else {
                $newCustomerTransaction->status = 'Paid';
            }
            $newCustomerTransaction->save();
            $customerTransactions = CustomerTransaction::where('customer_id', $customerTransaction->customer_id)->get();
            $totalBalance = 0;
            foreach($customerTransactions as $value) {
                error_log($value);
                $totalBalance = $totalBalance + $value->balance;
            }
            Customer::where('id', $customerTransaction->customer_id)->update([
                'balance' => $totalBalance
            ]);
            // create sale return customer transaction
            $saleReturnCustomerTransaction = new SaleReturnCustomerTransaction();
            $saleReturnCustomerTransaction->sale_return_id = $saleReturn->id;
            $saleReturnCustomerTransaction->transaction_id = $newCustomerTransaction->id;
            $saleReturnCustomerTransaction->save();
            SaleOrder::where('id', request('sale_order_id'))->update([
                'return_status' => true
            ]);
            return $saleReturn;
        } catch (\Throwable $e) {
            error_log($e);
            return response()->json([
                'error' => $e
            ],200);
        }
    }
    public function update($id) {
        try {
            SaleReturn::where('id', $id)->update([
                'payment_type' => request('paymentType'),
                'paid' => request('paid'),
                'balance' => request('balance')
            ]);
            if (request('balance')) {
                if (request('balance') != 0)
                    $status = 'Partial';
                else if (request('balance') == 0)
                    $status = 'Paid';
                else
                    $status = 'Unpaid';
            } else {
                $status = 'Paid';
            }
            $saleReturnProductTransactions = SaleReturnProductTransaction::where('sale_return_id', $id)->get();
            foreach ($saleReturnProductTransactions as $saleReturnProductTransaction) {
                ProductHistory::where('id', $saleReturnProductTransaction->transaction_id)->update([
                    'status' => $status
                ]);
            }
            $saleReturnCustomerTransaction = SaleReturnCustomerTransaction::where('sale_return_id', $id)->first();
            $customerTransaction = CustomerTransaction::where('id', $saleReturnCustomerTransaction->transaction_id)->first();
            CustomerTransaction::where('id', $saleReturnCustomerTransaction->transaction_id)->update([
                'status' => $status,
                'balance' => request('balance')
            ]);
            $customerTransactions = CustomerTransaction::where('customer_id', $customerTransaction->customer_id)->get();
            $totalBalance = 0;
            foreach($customerTransactions as $value) {
                error_log($value);
                $totalBalance = $totalBalance + $value->balance;
            }
            Customer::where('id', $customerTransaction->customer_id)->update([
                'balance' => $totalBalance
            ]);
            return response()->json([
                'message' => 'Successfully Updated Sale Return'
            ],200);
        } catch(\Throwable $e) {
            error_log($e);
            return response()->json([
                'error' => $e
            ],200);
        }
    }
    public function delete($id) {
        try {
            SaleReturn::where('id', $id)->delete();
            return response()->json([
                'message' => 'Deleted Successfully'
            ],200);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Cannot delete Sale Return'
            ],200);
        }
    }
}
