<?php

namespace App\Http\Controllers;

use App\SaleOrder;
use App\SaleOrderCustomerTransaction;
use App\PurchaseOrder;
use App\PurchaseOrderSupplierTransaction;
use App\Customer;
use App\CustomerTransaction;
use App\Supplier;
use App\SupplierTransaction;
use App\Item;
use App\Variant;
use App\Expense;
use App\PaymentIn;
use App\PaymentInCustomerTransaction;
use App\PaymentOut;
use App\PaymentOutSupplierTransaction;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboardSummary($id) {
        if ($id <= 0) {
            $sales = SaleOrder::whereNull('return_status')->get();
            $saleAmount = 0;
            foreach ($sales as $sale) {
                $saleAmount += $sale->amount;
            }
            $purchases = PurchaseOrder::whereNull('return_status')->get();
            $purchaseAmount = 0;
            foreach ($purchases as $purchase) {
                $purchaseAmount += $purchase->amount;
            }
            $suppliers = Supplier::all();
            $customers = Customer::all();
            $toPay = 0;
            $toReceive = 0;
            foreach($customers as $customer) {
                if($customer->balance > 0) {
                    $toReceive += $customer->balance;
                } else {
                    $toPay += $customer->balance;
                }
            }
            foreach($suppliers as $supplier) {
                if($supplier->balance > 0) {
                    $toReceive += $supplier->balance;
                } else {
                    $toPay += $supplier->balance;
                }
            }
            $items = Item::all();
            $variants = Variant::all();
            $stock = 0;
            $stockValue = 0;
            foreach($items as $item) {
                if($item->stock) {
                    $stock += $item->stock;
                    $stockValue += $item->stock_value;
                }
            }
            foreach($variants as $variant) {
                if($variant->stock) {
                    $stock += $variant->stock;
                    $stockValue += $variant->stock_value;
                }
            }
            $expenses = Expense::all();
            $expenseAmount = 0;
            foreach($expenses as $expense) {
                $expenseAmount += $expense->total;
            }
        } else {
            $sales = SaleOrder::where('branch_id',$id)->whereNull('return_status')->get();
            $saleAmount = 0;
            foreach ($sales as $sale) {
                $saleAmount += $sale->amount;
            }
            $purchases = PurchaseOrder::where('branch_id',$id)->whereNull('return_status')->get();
            $purchaseAmount = 0;
            foreach ($purchases as $purchase) {
                $purchaseAmount += $purchase->amount;
            }
            $items = Item::where('branch_id',$id)->get();
            $stock = 0;
            $stockValue = 0;
            foreach($items as $item) {
                if($item->stock) {
                    $stock += $item->stock;
                    $stockValue += $item->stock_value;
                } else {
                    $variants = Variant::where('item_id',$item->id)->get();
                    foreach($variants as $variant) {
                        if($variant->stock) {
                            $stock += $variant->stock;
                            $stockValue += $variant->stock_value;
                        }
                    }
                }
            }
            $expenses = Expense::where('branch_id',$id)->get();
            $expenseAmount = 0;
            foreach($expenses as $expense) {
                $expenseAmount += $expense->total;
            }
            $toPay = 0;
            $toReceive = 0;
            $saleOrders = SaleOrder::where('branch_id', $id)->whereNull('return_status')->get();
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
            $paymentIns = PaymentIn::where('branch_id', $id)->get();
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
            $purchaseOrders = PurchaseOrder::where('branch_id', $id)->whereNull('return_status')->get();
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
            $paymentOuts = PaymentOut::where('branch_id', $id)->get();
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
        }

        return response()->json([
            'toPay' => abs($toPay),
            'toReceive' => $toReceive,
            'sale' => $saleAmount,
            'purchase' => $purchaseAmount,
            'stock' => $stock,
            'stockValue' => $stockValue,
            'expense' => $expenseAmount,
            'cash' => ($saleAmount - ($purchaseAmount + $expenseAmount)) - $toReceive
        ],200);
    }

    public function home() {
        echo ('Toppers Pakistan -> Server Is Working');
    }
}
