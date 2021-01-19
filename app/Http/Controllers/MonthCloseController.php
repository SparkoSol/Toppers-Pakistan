<?php

namespace App\Http\Controllers;

use App\CustomerTransaction;
use App\Expense;
use App\Item;
use App\ItemTransaction;
use App\MonthClose;
use App\PaymentIn;
use App\PaymentInCustomerTransaction;
use App\PaymentOut;
use App\PaymentOutSupplierTransaction;
use App\PurchaseOrder;
use App\PurchaseOrderSupplierTransaction;
use App\SaleOrder;
use App\SaleOrderCustomerTransaction;
use App\SupplierTransaction;
use Illuminate\Http\Request;

class MonthCloseController extends Controller
{
    public function insert($id) {
        try {
            if ($id <= 0) {
                $sales = SaleOrder::whereNull('return_status')->whereBetween('invoice_date', [request('from'), request('to')])->get();
                $saleAmount = 0;
                foreach ($sales as $sale) {
                    $saleAmount += $sale->amount;
                }
                $purchases = PurchaseOrder::whereNull('return_status')->whereBetween('invoice_date', [request('from'), request('to')])->get();
                $purchaseAmount = 0;
                foreach ($purchases as $purchase) {
                    $purchaseAmount += $purchase->amount;
                }
                $supplierTransactions = SupplierTransaction::where('active',1)->whereBetween('date', [request('from'), request('to')])->get();
                $customerTransactions = CustomerTransaction::where('active',1)->whereBetween('date', [request('from'), request('to')])->get();
                $toPay = 0;
                $toReceive = 0;
                foreach($customerTransactions as $customerTransaction) {
                    if($customerTransaction->balance > 0) {
                        $toReceive += $customerTransaction->balance;
                    } else {
                        $toPay += $customerTransaction->balance;
                    }
                }
                foreach($supplierTransactions as $supplierTransaction) {
                    if($supplierTransaction->balance > 0) {
                        $toReceive += $supplierTransaction->balance;
                    } else {
                        $toPay += $supplierTransaction->balance;
                    }
                }
                $items = Item::all();
                $openingStock = 0;
                $openingStockValue = 0;
                $closingStock = 0;
                $closingStockValue = 0;
                foreach($items as $item) {
                    $itemTransactions = ItemTransaction::where('active',1)->where('item_id', $item->id)->whereDate('date','<=', request('from'))->get();
                    foreach($itemTransactions as $itemTransaction) {
                        if ($itemTransaction->type === 0) {
                            $openingStock = $itemTransaction->quantity;
                            $openingStockValue = $openingStockValue + ($itemTransaction->quantity * $itemTransaction->price);
                        }
                        else if ($itemTransaction->type === 2 || $itemTransaction->type === 3 || $itemTransaction->type === 8) {
                            $openingStock = $openingStock - $itemTransaction->quantity;
                            $openingStockValue = $openingStockValue - ($itemTransaction->quantity * $itemTransaction->price);
                        } else {
                            $openingStock = $openingStock + $itemTransaction->quantity;
                            $openingStockValue = $openingStockValue + ($itemTransaction->quantity * $itemTransaction->price);
                        }
                    }
                    $itemTransactions = ItemTransaction::where('active',1)->where('item_id', $item->id)->whereDate('date','<=', request('to'))->get();
                    foreach($itemTransactions as $itemTransaction) {
                        if ($itemTransaction->type === 0) {
                            $closingStock = $itemTransaction->quantity;
                            $closingStockValue = $closingStockValue + ($itemTransaction->quantity * $itemTransaction->price);
                        }
                        else if ($itemTransaction->type === 2 || $itemTransaction->type === 3 || $itemTransaction->type === 8) {
                            $closingStock = $closingStock - $itemTransaction->quantity;
                            $closingStockValue = $closingStockValue - ($itemTransaction->quantity * $itemTransaction->price);
                        } else {
                            $closingStock = $closingStock + $itemTransaction->quantity;
                            $closingStockValue = $closingStockValue + ($itemTransaction->quantity * $itemTransaction->price);
                        }
                    }
                }
                $expenses = Expense::whereBetween('created_at',[request('from'), request('to')])->get();
                $expenseAmount = 0;
                foreach($expenses as $expense) {
                    $expenseAmount += $expense->total;
                }
            } else {
                $sales = SaleOrder::whereNull('return_status')->whereBetween('invoice_date', [request('from'), request('to')])->where('branch_id', $id)->get();
                $saleAmount = 0;
                foreach ($sales as $sale) {
                    $saleAmount += $sale->amount;
                }
                $purchases = PurchaseOrder::whereNull('return_status')->whereBetween('invoice_date', [request('from'), request('to')])->where('branch_id', $id)->get();
                $purchaseAmount = 0;
                foreach ($purchases as $purchase) {
                    $purchaseAmount += $purchase->amount;
                }
                $toPay = 0;
                $toReceive = 0;
                $saleOrders = SaleOrder::where('branch_id', $id)->whereNull('return_status')->whereBetween('invoice_date', [request('from'), request('to')])->get();
                foreach($saleOrders as $saleOrder) {
                    $saleCustomerTransaction = SaleOrderCustomerTransaction::where('sale_order_id',$saleOrder->id)->first();
                    if($saleCustomerTransaction){
                        $customerTransaction = CustomerTransaction::where('id', $saleCustomerTransaction->transaction_id)->first();
                        if($customerTransaction->balance > 0) {
                            $toReceive += $customerTransaction->balance;
                        } else {
                            $toPay += $customerTransaction->balance;
                        }
                    }
                }
                $paymentIns = PaymentIn::where('branch_id', $id)->whereBetween('receipt_date', [request('from'), request('to')])->get();
                foreach($paymentIns as $paymentIn) {
                    $paymentCustomerTransaction = PaymentInCustomerTransaction::where('payment_in_id',$paymentIn->id)->first();
                    if($paymentCustomerTransaction){
                        $customerTransaction = CustomerTransaction::where('id', $paymentCustomerTransaction->transaction_id)->first();
                        if($customerTransaction->balance > 0) {
                            $toReceive += $customerTransaction->balance;
                        } else {
                            $toPay += $customerTransaction->balance;
                        }
                    }
                }
                $purchaseOrders = PurchaseOrder::where('branch_id', $id)->whereNull('return_status')->whereBetween('invoice_date', [request('from'), request('to')])->get();
                foreach($purchaseOrders as $purchaseOrder) {
                    $purchaseSupplierTransaction = PurchaseOrderSupplierTransaction::where('purchase_order_id',$purchaseOrder->id)->first();
                    if($purchaseSupplierTransaction){
                        $supplierTransaction = SupplierTransaction::where('id', $purchaseSupplierTransaction->transaction_id)->first();
                        if($supplierTransaction->balance > 0) {
                            $toReceive += $supplierTransaction->balance;
                        } else {
                            $toPay += $supplierTransaction->balance;
                        }
                    }
                }
                $paymentOuts = PaymentOut::where('branch_id', $id)->whereBetween('receipt_date', [request('from'), request('to')])->get();
                foreach($paymentOuts as $paymentOut) {
                    $paymentSupplierTransaction = PaymentOutSupplierTransaction::where('payment_out_id',$paymentOut->id)->first();
                    if($paymentSupplierTransaction){
                        $supplierTransaction = SupplierTransaction::where('id', $paymentSupplierTransaction->transaction_id)->first();
                        if($supplierTransaction->balance > 0) {
                            $toReceive += $supplierTransaction->balance;
                        } else {
                            $toPay += $supplierTransaction->balance;
                        }
                    }
                }
                $items = Item::where('branch_id', $id)->get();
                $openingStock = 0;
                $openingStockValue = 0;
                $closingStock = 0;
                $closingStockValue = 0;
                foreach($items as $item) {
                    $itemTransactions = ItemTransaction::where('active',1)->where('item_id', $item->id)->whereDate('date','<=', request('from'))->get();
                    foreach($itemTransactions as $itemTransaction) {
                        if ($itemTransaction->type === 0) {
                            $openingStock = $itemTransaction->quantity;
                            $openingStockValue = $openingStockValue + ($itemTransaction->quantity * $itemTransaction->price);
                        }
                        else if ($itemTransaction->type === 2 || $itemTransaction->type === 3 || $itemTransaction->type === 8) {
                            $openingStock = $openingStock - $itemTransaction->quantity;
                            $openingStockValue = $openingStockValue - ($itemTransaction->quantity * $itemTransaction->price);
                        } else {
                            $openingStock = $openingStock + $itemTransaction->quantity;
                            $openingStockValue = $openingStockValue + ($itemTransaction->quantity * $itemTransaction->price);
                        }
                    }
                    $itemTransactions = ItemTransaction::where('active',1)->where('item_id', $item->id)->whereDate('date','<=', request('to'))->get();
                    foreach($itemTransactions as $itemTransaction) {
                        if ($itemTransaction->type === 0) {
                            $closingStock = $itemTransaction->quantity;
                            $closingStockValue = $closingStockValue + ($itemTransaction->quantity * $itemTransaction->price);
                        }
                        else if ($itemTransaction->type === 2 || $itemTransaction->type === 3 || $itemTransaction->type === 8) {
                            $closingStock = $closingStock - $itemTransaction->quantity;
                            $closingStockValue = $closingStockValue - ($itemTransaction->quantity * $itemTransaction->price);
                        } else {
                            $closingStock = $closingStock + $itemTransaction->quantity;
                            $closingStockValue = $closingStockValue + ($itemTransaction->quantity * $itemTransaction->price);
                        }
                    }
                }
                $expenses = Expense::whereBetween('created_at',[request('from'), request('to')])->where('branch_id', $id)->get();
                $expenseAmount = 0;
                foreach($expenses as $expense) {
                    $expenseAmount += $expense->total;
                }
            }

            $monthClose = new MonthClose();
            $monthClose->cash = ($saleAmount - ($purchaseAmount + $expenseAmount)) - $toReceive;
            $monthClose->expense = $expenseAmount;
            $monthClose->purchase = $purchaseAmount;
            $monthClose->sale = $saleAmount;
            $monthClose->stock = $closingStock;
            $monthClose->stockValue = $closingStockValue;
            $monthClose->toPay = abs($toPay);
            $monthClose->toReceive = $toReceive;
            $monthClose->date_from = request('from');
            $monthClose->date_to = request('to');
            if ($id > 0) {
                $monthClose->branch_id = $id;
            }
            $monthClose->save();
        } catch (\Throwable $e) {
            error_log($e);
        }
    }
    public function get($id) {
        if ($id > 0) {
            return MonthClose::where('branch_id',$id)->with('branch')->get();
        } else {
            return MonthClose::with('branch')->get();
        }    }
}
