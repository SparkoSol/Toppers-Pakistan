<?php

namespace App\Http\Controllers;

use App\SaleReturn;
use App\SaleOrder;
use App\SaleOrderItem;
use App\SaleOrderItemTransaction;
use App\SaleOrderCustomerTransaction;
use App\SaleReturnCustomerTransaction;
use App\SaleReturnItemTransaction;
use App\Item;
use App\Customer;
use App\CustomerTransaction;
use App\ItemTransaction;
use App\Variant;
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
    public function getSaleReturn($id) {
        if($id === '-1') {
            return SaleReturn::orderBy('id','desc')->with('customer')->with('SaleOrder.branch')->get();
        } else {
            return SaleReturn::orderBy('id','desc')->where('branch_id',$id)->with('customer')->with('SaleOrder.branch')->get();
        }
    }
    public function getSaleReturnById($id) {
        return SaleReturn::where('id', $id)->with('customer')->with('SaleOrder.branch')->first();
    }
    public function customFilter($id) {
        if($id === '-1') {
            return SaleReturn::orderBy('id','desc')->whereBetween('invoice_date', [request('from'), request('to')])->with('customer')->with('SaleOrder.branch')->get();
        } else {
            return SaleReturn::orderBy('id','desc')->where('branch_id',$id)->whereBetween('invoice_date', [request('from'), request('to')])->with('customer')->with('SaleOrder.branch')->get();
        }
    }
    public function store() {
        try {
            $saleOrder = SaleOrder::where('id', request('sale_order_id'))->first();
            $saleOrderCustomerTransaction = SaleOrderCustomerTransaction::where('sale_order_id',request('sale_order_id'))->first();
            if ($saleOrderCustomerTransaction) {
                CustomerTransaction::where('id', $saleOrderCustomerTransaction->transaction_id)->update([
                    'active' => false
                ]);
            }
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
            $saleOrderItemTransactions = SaleOrderItemTransaction::where('sale_order_id', $saleOrder->id)->get();
            foreach($saleOrderItemTransactions as $saleOrderItemTransaction) {
                $productTransaction = ItemTransaction::where('id', $saleOrderItemTransaction->transaction_id)->first();
                $newItemTransaction = new ItemTransaction();
                $newItemTransaction->customer_id = $productTransaction->customer_id;
                $newItemTransaction->item_id = $productTransaction->item_id;
                $newItemTransaction->variant_id = $productTransaction->variant_id;
                $newItemTransaction->quantity = $productTransaction->quantity;
                $newItemTransaction->price = $productTransaction->price;
                $newItemTransaction->type = 5;
                $newItemTransaction->date = request('invoiceDate');
                if (request('balance')) {
                    if (request('balance') != 0)
                        $newItemTransaction->status = 'Partial';
                    else if (request('balance') == 0)
                        $newItemTransaction->status = 'Paid';
                    else
                        $newItemTransaction->status = 'Unpaid';
                } else {
                        $newItemTransaction->status = 'Paid';
                }
                $newItemTransaction->save();
                $saleReturnItemTransaction = new SaleReturnItemTransaction();
                $saleReturnItemTransaction->sale_return_id = $saleReturn->id;
                $saleReturnItemTransaction->transaction_id = $newItemTransaction->id;
                $saleReturnItemTransaction->save();
                if($newItemTransaction->variant_id === null) {
                    error_log('here');
                    $itemTransactions = ItemTransaction::where('item_id', $newItemTransaction->item_id)->where('active',1)->get();
                    $stock = 0;
                    $stock_value = 0;
                    foreach($itemTransactions as $itemTransaction) {
                        if ($itemTransaction->type === 2 || $itemTransaction->type === 3 || $itemTransaction->type === 8) {
                            $stock = $stock - $itemTransaction->quantity;
                            $stock_value = $stock_value - ($itemTransaction->quantity * $itemTransaction->price);
                        } else {
                            $stock = $stock + $itemTransaction->quantity;
                            $stock_value = $stock_value + ($itemTransaction->quantity * $itemTransaction->price);
                        }
                    }
                    Item::where('id', $newItemTransaction->item_id)->update([
                        'stock' => $stock,
                        'stock_value' => $stock_value
                    ]);
                } else {
                    $itemTransactions = ItemTransaction::where('variant_id', $newItemTransaction->variant_id)->where('active', 1)->get();
                    $stock = 0;
                    $stock_value = 0;
                    foreach($itemTransactions as $itemTransaction) {
                        if ($itemTransaction->type === 2 || $itemTransaction->type === 3 || $itemTransaction->type === 8) {
                            $stock = $stock - $itemTransaction->quantity;
                            $stock_value = $stock_value - ($itemTransaction->quantity * $itemTransaction->price);
                        } else {
                            $stock = $stock + $itemTransaction->quantity;
                            $stock_value = $stock_value + ($itemTransaction->quantity * $itemTransaction->price);
                        }
                    }
                    Variant::where('id', $newItemTransaction->variant_id)->update([
                        'stock' => $stock,
                        'stock_value' => $stock_value
                    ]);
                }
            }
            // create customer transactions
            $saleOrderCustomerTransaction = SaleOrderCustomerTransaction::where('sale_order_id', $saleOrder->id)->first();
            if ($saleOrderCustomerTransaction) {
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
                $customerTransactions = CustomerTransaction::where('customer_id', $customerTransaction->customer_id)->where('active', 1)->get();
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
            }
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
            $saleReturnItemTransactions = SaleReturnItemTransaction::where('sale_return_id', $id)->get();
            foreach ($saleReturnItemTransactions as $saleReturnItemTransaction) {
                ItemTransaction::where('id', $saleReturnItemTransaction->transaction_id)->update([
                    'status' => $status
                ]);
            }
            $saleReturnCustomerTransaction = SaleReturnCustomerTransaction::where('sale_return_id', $id)->first();
            $customerTransaction = CustomerTransaction::where('id', $saleReturnCustomerTransaction->transaction_id)->first();
            CustomerTransaction::where('id', $saleReturnCustomerTransaction->transaction_id)->update([
                'status' => $status,
                'balance' => - request('balance')
            ]);
            $customerTransactions = CustomerTransaction::where('customer_id', $customerTransaction->customer_id)->where('active', 1)->get();
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
