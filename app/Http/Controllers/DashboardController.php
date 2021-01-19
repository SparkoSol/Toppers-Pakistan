<?php

namespace App\Http\Controllers;

use App\DayClose;
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
use App\ItemTransaction;
use App\RestaurantBranch;
use PDF;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function home() {
        echo ('Apna Pos -> Server Is Working');
    }

    public function dashboardSummary($id) {
        if ($id <= 0) {
            $dayClose = DayClose::latest()->first();
            if ($dayClose) {
                $sales = SaleOrder::whereNull('return_status')->where('invoice_date','>', $dayClose->date)->get();
                $saleAmount = 0;
                foreach ($sales as $sale) {
                    $saleAmount += $sale->amount;
                }
                $purchases = PurchaseOrder::whereNull('return_status')->where('invoice_date','>', $dayClose->date)->get();
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
            }
        } else {
            $dayClose = DayClose::where('branch_id',$id)->latest()->first();
            if ($dayClose) {
                $sales = SaleOrder::where('branch_id',$id)->whereNull('return_status')->where('invoice_date','>', $dayClose->date)->get();
                $saleAmount = 0;
                foreach ($sales as $sale) {
                    $saleAmount += $sale->amount;
                }
                $purchases = PurchaseOrder::where('branch_id',$id)->whereNull('return_status')->where('invoice_date','>', $dayClose->date)->get();
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
                $expenses = Expense::where('branch_id',$id)->where('created_at','>', $dayClose->date)->get();
                $expenseAmount = 0;
                foreach($expenses as $expense) {
                    $expenseAmount += $expense->total;
                }
                $toPay = 0;
                $toReceive = 0;
                $saleOrders = SaleOrder::where('branch_id', $id)->whereNull('return_status')->where('invoice_date','>', $dayClose->date)->get();
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
                $paymentIns = PaymentIn::where('branch_id', $id)->where('receipt_date','>', $dayClose->date)->get();
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
                $purchaseOrders = PurchaseOrder::where('branch_id', $id)->where('invoice_date','>', $dayClose->date)->whereNull('return_status')->get();
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
                $paymentOuts = PaymentOut::where('branch_id', $id)->where('receipt_date','>=', $dayClose->date)->get();
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

    public function profitLoss($id) {
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
            $itemTransactions = ItemTransaction::where('type', 0)->where('active',1)->get();
            $openingStock = 0;
            foreach($itemTransactions as $itemTransaction) {
                 $openingStock += ($itemTransaction->quantity * $itemTransaction->price);
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
            $items = Item::where('branch_id', $id)->get();
            $openingStock = 0;
            foreach($items as $item) {
                $itemTransactions = ItemTransaction::where('type', 0)->where('item_id', $item->id)->where('active',1)->get();
                foreach($itemTransactions as $itemTransaction) {
                     $openingStock += ($itemTransaction->quantity * $itemTransaction->price);
                }
            }
        }

        return response()->json([
            'creditNote' => abs($toPay),
            'debitNote' => $toReceive,
            'sale' => $saleAmount,
            'purchase' => $purchaseAmount,
            'openingStock' => $openingStock,
            'closingStock' => $stockValue,
            'expense' => $expenseAmount
        ],200);
    }

    public function profitLossPrint($id) {
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
            $itemTransactions = ItemTransaction::where('type', 0)->where('active',1)->get();
            $openingStock = 0;
            foreach($itemTransactions as $itemTransaction) {
                 $openingStock += ($itemTransaction->quantity * $itemTransaction->price);
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
            $items = Item::where('branch_id', $id)->get();
            $openingStock = 0;
            foreach($items as $item) {
                $itemTransactions = ItemTransaction::where('type', 0)->where('item_id', $item->id)->where('active',1)->get();
                foreach($itemTransactions as $itemTransaction) {
                     $openingStock += ($itemTransaction->quantity * $itemTransaction->price);
                }
            }
        }
        if ($id > 0) {
            $branch = RestaurantBranch::where('id', $id)->first();
        } else {
            $branch = null;
        }
        return PDF::loadView('profit-loss-report-1', array(
            'branch' => $branch,
            'branch_id' => $id,
            'creditNote' => abs($toPay),
            'debitNote' => $toReceive,
            'sale' => $saleAmount,
            'purchase' => $purchaseAmount,
            'openingStock' => $openingStock,
            'closingStock' => $stockValue,
            'expense' => $expenseAmount))
            ->setPaper('a4', 'portrait')->setWarnings(false)->stream('profitLoss.pdf');

    }

    public function profitLossRange($id) {
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

        return response()->json([
            'creditNote' => abs($toPay),
            'debitNote' => $toReceive,
            'sale' => $saleAmount,
            'purchase' => $purchaseAmount,
            'openingStock' => $openingStockValue,
            'closingStock' => $closingStockValue,
            'expense' => $expenseAmount
        ],200);
}

    public function filterSale($id,$branchId) {
        $month = date("m");
        $year = date('Y');
        $sales = [];
        /*
        0 all
        1 this month
        2 last month
        3 this quarter
        4 this year
        */
        switch ([$id, $branchId]) {
            case [0,-1]:
                $sales = SaleOrder::whereNull('return_status')->get();
                break;
            case [1,-1]:
                $sales = SaleOrder::whereNull('return_status')->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                break;
            case [2,-1]:
                if (date('m') == 01) {
                    $sales = SaleOrder::whereNull('return_status')->whereMonth('invoice_date', 12)->whereYear('invoice_date', date('Y') - 1)->get();
                } else {
                    $sales = SaleOrder::whereNull('return_status')->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
                }
                break;
            case [3,-1]:
                if($month > 1 && $month <= 3)
                {
                    $start_date = date("Y-m-d",strtotime('1-January-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-April-'.$year));
                }
                else  if($month >= 4 && $month <= 6)
                {
                    $start_date = date("Y-m-d",strtotime('1-April-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-July-'.$year));
                }
                else  if($month >= 7 && $month <= 9)
                {
                    $start_date = date("Y-m-d",strtotime('1-July-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-October-'.$year));
                }
                else  if($month >= 10 && $month <= 12)
                {
                    $start_date = date("Y-m-d",strtotime('1-October-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-January-'.($year+1)));
                }
                $sales = SaleOrder::whereNull('return_status')->whereBetween('invoice_date', [$start_date, $end_date])->get();
                break;
            case [4,-1]:
                $sales = SaleOrder::whereNull('return_status')->whereYear('invoice_date', date('Y'))->get();
                break;
            case [0, $branchId > 0]:
                $sales = SaleOrder::whereNull('return_status')->where('branch_id',$branchId)->get();
                break;
            case [1, $branchId > 0]:
                $sales = SaleOrder::whereNull('return_status')->where('branch_id',$branchId)->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                break;
            case [2, $branchId > 0]:
                if (date('m') == 01) {
                    $sales = SaleOrder::whereNull('return_status')->where('branch_id',$branchId)->whereMonth('invoice_date', 12)->whereYear('invoice_date', date('Y') - 1)->get();
                } else {
                    $sales = SaleOrder::whereNull('return_status')->where('branch_id',$branchId)->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
                }
                break;
            case [3, $branchId > 0]:
                if($month >= 1 && $month <= 3)
                {
                    $start_date = date("Y-m-d",strtotime('1-January-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-April-'.$year));
                }
                else  if($month >= 4 && $month <= 6)
                {
                    $start_date = date("Y-m-d",strtotime('1-April-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-July-'.$year));
                }
                else  if($month >= 7 && $month <= 9)
                {
                    $start_date = date("Y-m-d",strtotime('1-July-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-October-'.$year));
                }
                else  if($month >= 10 && $month <= 12)
                {
                    $start_date = date("Y-m-d",strtotime('1-October-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-January-'.($year+1)));
                }
                $sales = SaleOrder::whereNull('return_status')->where('branch_id',$branchId)->whereBetween('invoice_date', [$start_date, $end_date])->get();
                break;
            case [4, $branchId > 0]:
                $sales = SaleOrder::whereNull('return_status')->where('branch_id',$branchId)->whereYear('invoice_date', date('Y'))->get();
                break;
        }
        $saleAmount = 0;
        foreach ($sales as $sale) {
            $saleAmount += $sale->amount;
        }
        return $saleAmount;
    }

    public function filterPurchase($id,$branchId) {
        $month = date("m");
        $year = date('Y');
        $purchases = [];
        /*
        0 all
        1 this month
        2 last month
        3 this quarter
        4 this year
        */
        switch ([$id, $branchId]) {
            case [0,-1]:
                $purchases = PurchaseOrder::whereNull('return_status')->get();
                break;
            case [1,-1]:
                $purchases = PurchaseOrder::whereNull('return_status')->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                break;
            case [2,-1]:
                if (date('m') == 01) {
                    $purchases = PurchaseOrder::whereNull('return_status')->whereMonth('invoice_date', 12)->whereYear('invoice_date', date('Y') - 1)->get();
                } else {
                    $purchases = PurchaseOrder::whereNull('return_status')->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
                }
                break;
            case [3,-1]:
                if($month >= 1 && $month <= 3)
                {
                    $start_date = date("Y-m-d",strtotime('1-January-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-April-'.$year));
                }
                else  if($month >= 4 && $month <= 6)
                {
                    $start_date = date("Y-m-d",strtotime('1-April-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-July-'.$year));
                }
                else  if($month >= 7 && $month <= 9)
                {
                    $start_date = date("Y-m-d",strtotime('1-July-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-October-'.$year));
                }
                else  if($month >= 10 && $month <= 12)
                {
                    $start_date = date("Y-m-d",strtotime('1-October-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-January-'.($year+1)));
                }
                $purchases = PurchaseOrder::whereNull('return_status')->whereBetween('invoice_date', [$start_date, $end_date])->get();
                break;
            case [4,-1]:
                $purchases = PurchaseOrder::whereNull('return_status')->whereYear('invoice_date', date('Y'))->get();
                break;
            case [0, $branchId > 0]:
                $purchases = PurchaseOrder::whereNull('return_status')->where('branch_id',$branchId)->get();
                break;
            case [1, $branchId > 0]:
                $purchases = PurchaseOrder::whereNull('return_status')->where('branch_id',$branchId)->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                break;
            case [2, $branchId > 0]:
                if (date('m') == 01) {
                    $purchases = PurchaseOrder::whereNull('return_status')->where('branch_id',$branchId)->whereMonth('invoice_date', 12)->whereYear('invoice_date', date('Y') - 1)->get();
                } else {
                    $purchases = PurchaseOrder::whereNull('return_status')->where('branch_id',$branchId)->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
                }
                break;
            case [3, $branchId > 0]:
                if($month >= 1 && $month <= 3)
                {
                    $start_date = date("Y-m-d",strtotime('1-January-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-April-'.$year));
                }
                else  if($month >= 4 && $month <= 6)
                {
                    $start_date = date("Y-m-d",strtotime('1-April-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-July-'.$year));
                }
                else  if($month >= 7 && $month <= 9)
                {
                    $start_date = date("Y-m-d",strtotime('1-July-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-October-'.$year));
                }
                else  if($month >= 10 && $month <= 12)
                {
                    $start_date = date("Y-m-d",strtotime('1-October-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-January-'.($year+1)));
                }
                $purchases = PurchaseOrder::whereNull('return_status')->where('branch_id',$branchId)->whereBetween('invoice_date', [$start_date, $end_date])->get();
                break;
            case [4, $branchId > 0]:
                $purchases = PurchaseOrder::whereNull('return_status')->where('branch_id',$branchId)->whereYear('invoice_date', date('Y'))->get();
                break;
        }
        $purchaseAmount = 0;
        foreach ($purchases as $purchase) {
            $purchaseAmount += $purchase->amount;
        }
        return $purchaseAmount;
    }

    public function filterToPay($id,$branchId) {
        $month = date("m");
        $year = date('Y');
        $purchases = [];
        /*
        0 all
        1 this month
        2 last month
        3 this quarter
        4 this year
        */
        switch ([$id, $branchId]) {
            case [0,-1]:
                $saleOrders = SaleOrder::whereNull('return_status')->get();
                $paymentIns = PaymentIn::all();
                $purchaseOrders = PurchaseOrder::whereNull('return_status')->get();
                $paymentOuts = PaymentOut::all();
                break;
            case [1,-1]:
                $saleOrders = SaleOrder::whereNull('return_status')->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                $paymentIns = PaymentIn::whereMonth('receipt_date', date('m'))->whereYear('receipt_date', date('Y'))->get();
                $purchaseOrders = PurchaseOrder::whereNull('return_status')->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                $paymentOuts = PaymentOut::whereMonth('receipt_date', date('m'))->whereYear('receipt_date', date('Y'))->get();
                break;
            case [2,-1]:
                if (date('m') == 01) {
                    $saleOrders = SaleOrder::whereNull('return_status')->whereMonth('invoice_date', 12)->whereYear('invoice_date', date('Y') -1)->get();
                    $paymentIns = PaymentIn::whereMonth('receipt_date', 12)->whereYear('receipt_date', date('Y') -1)->get();
                    $purchaseOrders = PurchaseOrder::whereNull('return_status')->whereMonth('invoice_date', 12)->whereYear('invoice_date', date('Y')-1)->get();
                    $paymentOuts = PaymentOut::whereMonth('receipt_date', 12)->whereYear('receipt_date', date('Y')-1)->get();
                } else {
                    $saleOrders = SaleOrder::whereNull('return_status')->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
                    $paymentIns = PaymentIn::whereMonth('receipt_date', date('m') - 1)->whereYear('receipt_date', date('Y'))->get();
                    $purchaseOrders = PurchaseOrder::whereNull('return_status')->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
                    $paymentOuts = PaymentOut::whereMonth('receipt_date', date('m') - 1)->whereYear('receipt_date', date('Y'))->get();
                }
                break;
            case [3,-1]:
                if($month >= 1 && $month <= 3)
                {
                    $start_date = date("Y-m-d",strtotime('1-January-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-April-'.$year));
                }
                else  if($month >= 4 && $month <= 6)
                {
                    $start_date = date("Y-m-d",strtotime('1-April-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-July-'.$year));
                }
                else  if($month >= 7 && $month <= 9)
                {
                    $start_date = date("Y-m-d",strtotime('1-July-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-October-'.$year));
                }
                else  if($month >= 10 && $month <= 12)
                {
                    $start_date = date("Y-m-d",strtotime('1-October-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-January-'.($year+1)));
                }
                $saleOrders = SaleOrder::whereNull('return_status')->whereBetween('invoice_date', [$start_date, $end_date])->get();
                $paymentIns = PaymentIn::whereBetween('receipt_date', [$start_date, $end_date])->get();
                $purchaseOrders = PurchaseOrder::whereNull('return_status')->whereBetween('invoice_date', [$start_date, $end_date])->get();
                $paymentOuts = PaymentOut::whereBetween('receipt_date', [$start_date, $end_date])->get();
                break;
            case [4,-1]:
                $saleOrders = SaleOrder::whereNull('return_status')->whereYear('invoice_date', date('Y'))->get();
                $paymentIns = PaymentIn::whereYear('receipt_date', date('Y'))->get();
                $purchaseOrders = PurchaseOrder::whereNull('return_status')->whereYear('invoice_date', date('Y'))->get();
                $paymentOuts = PaymentOut::whereYear('receipt_date', date('Y'))->get();
                break;
            case [0, $branchId > 0]:
                $purchases = PurchaseOrder::whereNull('return_status')->where('branch_id',$branchId)->get();
                $saleOrders = SaleOrder::whereNull('return_status')->where('branch_id',$branchId)->get();
                $paymentIns = PaymentIn::where('branch_id',$branchId)->get();
                $purchaseOrders = PurchaseOrder::where('branch_id',$branchId)->whereNull('return_status')->get();
                $paymentOuts = PaymentOut::where('branch_id',$branchId)->get();
                break;
            case [1, $branchId > 0]:
                $saleOrders = SaleOrder::where('branch_id',$branchId)->whereNull('return_status')->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                $paymentIns = PaymentIn::where('branch_id',$branchId)->whereMonth('receipt_date', date('m'))->whereYear('receipt_date', date('Y'))->get();
                $purchaseOrders = PurchaseOrder::where('branch_id',$branchId)->whereNull('return_status')->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                $paymentOuts = PaymentOut::where('branch_id',$branchId)->whereMonth('receipt_date', date('m'))->whereYear('receipt_date', date('Y'))->get();
                break;
            case [2, $branchId > 0]:
                if (date('m') == 01) {
                    $saleOrders = SaleOrder::where('branch_id',$branchId)->whereNull('return_status')->whereMonth('invoice_date', 12)->whereYear('invoice_date', date('Y') -1)->get();
                    $paymentIns = PaymentIn::where('branch_id',$branchId)->whereMonth('receipt_date', 12)->whereYear('receipt_date', date('Y') -1)->get();
                    $purchaseOrders = PurchaseOrder::where('branch_id',$branchId)->whereNull('return_status')->whereMonth('invoice_date', 12)->whereYear('invoice_date', date('Y')-1)->get();
                    $paymentOuts = PaymentOut::where('branch_id',$branchId)->whereMonth('receipt_date', 12)->whereYear('receipt_date', date('Y')-1)->get();
                } else {
                    $saleOrders = SaleOrder::where('branch_id',$branchId)->whereNull('return_status')->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
                    $paymentIns = PaymentIn::where('branch_id',$branchId)->whereMonth('receipt_date', date('m') - 1)->whereYear('receipt_date', date('Y'))->get();
                    $purchaseOrders = PurchaseOrder::where('branch_id',$branchId)->whereNull('return_status')->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
                    $paymentOuts = PaymentOut::where('branch_id',$branchId)->whereMonth('receipt_date', date('m') - 1)->whereYear('receipt_date', date('Y'))->get();
                }
                break;
            case [3, $branchId > 0]:
                if($month >= 1 && $month <= 3)
                {
                    $start_date = date("Y-m-d",strtotime('1-January-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-April-'.$year));
                }
                else  if($month >= 4 && $month <= 6)
                {
                    $start_date = date("Y-m-d",strtotime('1-April-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-July-'.$year));
                }
                else  if($month >= 7 && $month <= 9)
                {
                    $start_date = date("Y-m-d",strtotime('1-July-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-October-'.$year));
                }
                else  if($month >= 10 && $month <= 12)
                {
                    $start_date = date("Y-m-d",strtotime('1-October-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-January-'.($year+1)));
                }
                $saleOrders = SaleOrder::where('branch_id',$branchId)->whereNull('return_status')->whereBetween('invoice_date', [$start_date, $end_date])->get();
                $paymentIns = PaymentIn::where('branch_id',$branchId)->whereBetween('receipt_date', [$start_date, $end_date])->get();
                $purchaseOrders = PurchaseOrder::where('branch_id',$branchId)->whereNull('return_status')->whereBetween('invoice_date', [$start_date, $end_date])->get();
                $paymentOuts = PaymentOut::where('branch_id',$branchId)->whereBetween('receipt_date', [$start_date, $end_date])->get();
                break;
            case [4, $branchId > 0]:
                $saleOrders = SaleOrder::where('branch_id',$branchId)->whereNull('return_status')->whereYear('invoice_date', date('Y'))->get();
                $paymentIns = PaymentIn::where('branch_id',$branchId)->whereYear('receipt_date', date('Y'))->get();
                $purchaseOrders = PurchaseOrder::where('branch_id',$branchId)->whereNull('return_status')->whereYear('invoice_date', date('Y'))->get();
                $paymentOuts = PaymentOut::where('branch_id',$branchId)->whereYear('receipt_date', date('Y'))->get();
                break;
        }
        $toPay = 0;
        foreach($saleOrders as $saleOrder) {
            $saleCustomerTransaction = SaleOrderCustomerTransaction::where('sale_order_id',$saleOrder->id)->first();
            if($saleCustomerTransaction){
                $customerTransaction = CustomerTransaction::where('id', $saleCustomerTransaction->transaction_id)->first();
                if(!($customerTransaction->balance > 0)) {
                    $toPay += $customerTransaction->balance;
                }
            }
        }
        foreach($paymentIns as $paymentIn) {
            $paymentCustomerTransaction = PaymentInCustomerTransaction::where('payment_in_id',$paymentIn->id)->first();
            if($paymentCustomerTransaction){
            $customerTransaction = CustomerTransaction::where('id', $paymentCustomerTransaction->transaction_id)->first();
                if(!($customerTransaction->balance > 0)) {
                    $toPay += $customerTransaction->balance;
                }
            }
        }
        foreach($purchaseOrders as $purchaseOrder) {
            $purchaseSupplierTransaction = PurchaseOrderSupplierTransaction::where('purchase_order_id',$purchaseOrder->id)->first();
            if($purchaseSupplierTransaction){
                $supplierTransaction = SupplierTransaction::where('id', $purchaseSupplierTransaction->transaction_id)->first();
                if(!($supplierTransaction->balance > 0)) {
                    $toPay += $supplierTransaction->balance;
                }
            }
        }
        foreach($paymentOuts as $paymentOut) {
            $paymentSupplierTransaction = PaymentOutSupplierTransaction::where('payment_out_id',$paymentOut->id)->first();
            if($paymentSupplierTransaction){
                $supplierTransaction = SupplierTransaction::where('id', $paymentSupplierTransaction->transaction_id)->first();
                if(!($supplierTransaction->balance > 0)) {
                    $toPay += $supplierTransaction->balance;
                }
            }
        }
        return abs($toPay);
    }

    public function filterToRecieve($id,$branchId) {
        $month = date("m");
        $year = date('Y');
        $purchases = [];
        /*
        0 all
        1 this month
        2 last month
        3 this quarter
        4 this year
        */
        switch ([$id, $branchId]) {
            case [0,-1]:
                $saleOrders = SaleOrder::whereNull('return_status')->get();
                $paymentIns = PaymentIn::all();
                $purchaseOrders = PurchaseOrder::whereNull('return_status')->get();
                $paymentOuts = PaymentOut::all();
                break;
            case [1,-1]:
                $saleOrders = SaleOrder::whereNull('return_status')->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                $paymentIns = PaymentIn::whereMonth('receipt_date', date('m'))->whereYear('receipt_date', date('Y'))->get();
                $purchaseOrders = PurchaseOrder::whereNull('return_status')->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                $paymentOuts = PaymentOut::whereMonth('receipt_date', date('m'))->whereYear('receipt_date', date('Y'))->get();
                break;
            case [2,-1]:
                if (date('m') == 01) {
                    $saleOrders = SaleOrder::whereNull('return_status')->whereMonth('invoice_date', 12)->whereYear('invoice_date', date('Y') -1)->get();
                    $paymentIns = PaymentIn::whereMonth('receipt_date', 12)->whereYear('receipt_date', date('Y') -1)->get();
                    $purchaseOrders = PurchaseOrder::whereNull('return_status')->whereMonth('invoice_date', 12)->whereYear('invoice_date', date('Y')-1)->get();
                    $paymentOuts = PaymentOut::whereMonth('receipt_date', 12)->whereYear('receipt_date', date('Y')-1)->get();
                } else {
                    $saleOrders = SaleOrder::whereNull('return_status')->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
                    $paymentIns = PaymentIn::whereMonth('receipt_date', date('m') - 1)->whereYear('receipt_date', date('Y'))->get();
                    $purchaseOrders = PurchaseOrder::whereNull('return_status')->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
                    $paymentOuts = PaymentOut::whereMonth('receipt_date', date('m') - 1)->whereYear('receipt_date', date('Y'))->get();
                }
                break;
            case [3,-1]:
                if($month >= 1 && $month <= 3)
                {
                    $start_date = date("Y-m-d",strtotime('1-January-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-April-'.$year));
                }
                else  if($month >= 4 && $month <= 6)
                {
                    $start_date = date("Y-m-d",strtotime('1-April-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-July-'.$year));
                }
                else  if($month >= 7 && $month <= 9)
                {
                    $start_date = date("Y-m-d",strtotime('1-July-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-October-'.$year));
                }
                else  if($month >= 10 && $month <= 12)
                {
                    $start_date = date("Y-m-d",strtotime('1-October-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-January-'.($year+1)));
                }
                $saleOrders = SaleOrder::whereNull('return_status')->whereBetween('invoice_date', [$start_date, $end_date])->get();
                $paymentIns = PaymentIn::whereBetween('receipt_date', [$start_date, $end_date])->get();
                $purchaseOrders = PurchaseOrder::whereNull('return_status')->whereBetween('invoice_date', [$start_date, $end_date])->get();
                $paymentOuts = PaymentOut::whereBetween('receipt_date', [$start_date, $end_date])->get();
                break;
            case [4,-1]:
                $saleOrders = SaleOrder::whereNull('return_status')->whereYear('invoice_date', date('Y'))->get();
                $paymentIns = PaymentIn::whereYear('receipt_date', date('Y'))->get();
                $purchaseOrders = PurchaseOrder::whereNull('return_status')->whereYear('invoice_date', date('Y'))->get();
                $paymentOuts = PaymentOut::whereYear('receipt_date', date('Y'))->get();
                break;
            case [0, $branchId > 0]:
                $purchases = PurchaseOrder::whereNull('return_status')->where('branch_id',$branchId)->get();
                $saleOrders = SaleOrder::whereNull('return_status')->where('branch_id',$branchId)->get();
                $paymentIns = PaymentIn::where('branch_id',$branchId)->get();
                $purchaseOrders = PurchaseOrder::where('branch_id',$branchId)->whereNull('return_status')->get();
                $paymentOuts = PaymentOut::where('branch_id',$branchId)->get();
                break;
            case [1, $branchId > 0]:
                $saleOrders = SaleOrder::where('branch_id',$branchId)->whereNull('return_status')->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                $paymentIns = PaymentIn::where('branch_id',$branchId)->whereMonth('receipt_date', date('m'))->whereYear('receipt_date', date('Y'))->get();
                $purchaseOrders = PurchaseOrder::where('branch_id',$branchId)->whereNull('return_status')->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                $paymentOuts = PaymentOut::where('branch_id',$branchId)->whereMonth('receipt_date', date('m'))->whereYear('receipt_date', date('Y'))->get();
                break;
            case [2, $branchId > 0]:
                if (date('m') == 01) {
                    $saleOrders = SaleOrder::where('branch_id',$branchId)->whereNull('return_status')->whereMonth('invoice_date', 12)->whereYear('invoice_date', date('Y') -1)->get();
                    $paymentIns = PaymentIn::where('branch_id',$branchId)->whereMonth('receipt_date', 12)->whereYear('receipt_date', date('Y') -1)->get();
                    $purchaseOrders = PurchaseOrder::where('branch_id',$branchId)->whereNull('return_status')->whereMonth('invoice_date', 12)->whereYear('invoice_date', date('Y')-1)->get();
                    $paymentOuts = PaymentOut::where('branch_id',$branchId)->whereMonth('receipt_date', 12)->whereYear('receipt_date', date('Y')-1)->get();
                } else {
                    $saleOrders = SaleOrder::where('branch_id',$branchId)->whereNull('return_status')->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
                    $paymentIns = PaymentIn::where('branch_id',$branchId)->whereMonth('receipt_date', date('m') - 1)->whereYear('receipt_date', date('Y'))->get();
                    $purchaseOrders = PurchaseOrder::where('branch_id',$branchId)->whereNull('return_status')->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
                    $paymentOuts = PaymentOut::where('branch_id',$branchId)->whereMonth('receipt_date', date('m') - 1)->whereYear('receipt_date', date('Y'))->get();
                }
                break;
            case [3, $branchId > 0]:
                if($month >= 1 && $month <= 3)
                {
                    $start_date = date("Y-m-d",strtotime('1-January-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-April-'.$year));
                }
                else  if($month >= 4 && $month <= 6)
                {
                    $start_date = date("Y-m-d",strtotime('1-April-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-July-'.$year));
                }
                else  if($month >= 7 && $month <= 9)
                {
                    $start_date = date("Y-m-d",strtotime('1-July-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-October-'.$year));
                }
                else  if($month >= 10 && $month <= 12)
                {
                    $start_date = date("Y-m-d",strtotime('1-October-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-January-'.($year+1)));
                }
                $saleOrders = SaleOrder::where('branch_id',$branchId)->whereNull('return_status')->whereBetween('invoice_date', [$start_date, $end_date])->get();
                $paymentIns = PaymentIn::where('branch_id',$branchId)->whereBetween('receipt_date', [$start_date, $end_date])->get();
                $purchaseOrders = PurchaseOrder::where('branch_id',$branchId)->whereNull('return_status')->whereBetween('invoice_date', [$start_date, $end_date])->get();
                $paymentOuts = PaymentOut::where('branch_id',$branchId)->whereBetween('receipt_date', [$start_date, $end_date])->get();
                break;
            case [4, $branchId > 0]:
                $saleOrders = SaleOrder::where('branch_id',$branchId)->whereNull('return_status')->whereYear('invoice_date', date('Y'))->get();
                $paymentIns = PaymentIn::where('branch_id',$branchId)->whereYear('receipt_date', date('Y'))->get();
                $purchaseOrders = PurchaseOrder::where('branch_id',$branchId)->whereNull('return_status')->whereYear('invoice_date', date('Y'))->get();
                $paymentOuts = PaymentOut::where('branch_id',$branchId)->whereYear('receipt_date', date('Y'))->get();
                break;
        }
        $toRecieve = 0;
        foreach($saleOrders as $saleOrder) {
            $saleCustomerTransaction = SaleOrderCustomerTransaction::where('sale_order_id',$saleOrder->id)->first();
            if($saleCustomerTransaction){
                $customerTransaction = CustomerTransaction::where('id', $saleCustomerTransaction->transaction_id)->first();
                if($customerTransaction->balance > 0) {
                    $toRecieve += $customerTransaction->balance;
                }
            }
        }
        foreach($paymentIns as $paymentIn) {
            $paymentCustomerTransaction = PaymentInCustomerTransaction::where('payment_in_id',$paymentIn->id)->first();
            if($paymentCustomerTransaction){
            $customerTransaction = CustomerTransaction::where('id', $paymentCustomerTransaction->transaction_id)->first();
                if($customerTransaction->balance > 0) {
                    $toRecieve += $customerTransaction->balance;
                }
            }
        }
        foreach($purchaseOrders as $purchaseOrder) {
            $purchaseSupplierTransaction = PurchaseOrderSupplierTransaction::where('purchase_order_id',$purchaseOrder->id)->first();
            if($purchaseSupplierTransaction){
                $supplierTransaction = SupplierTransaction::where('id', $purchaseSupplierTransaction->transaction_id)->first();
                if($supplierTransaction->balance > 0) {
                    $toRecieve += $supplierTransaction->balance;
                }
            }
        }
        foreach($paymentOuts as $paymentOut) {
            $paymentSupplierTransaction = PaymentOutSupplierTransaction::where('payment_out_id',$paymentOut->id)->first();
            if($paymentSupplierTransaction){
                $supplierTransaction = SupplierTransaction::where('id', $paymentSupplierTransaction->transaction_id)->first();
                if($supplierTransaction->balance > 0) {
                    $toRecieve += $supplierTransaction->balance;
                }
            }
        }
        return $toRecieve;
    }

    public function filterExpense($id,$branchId) {
        $month = date("m");
        $year = date('Y');
        $purchases = [];
        /*
        0 all
        1 this month
        2 last month
        3 this quarter
        4 this year
        */
        switch ([$id, $branchId]) {
            case [0,-1]:
                $expenses = Expense::all();
                break;
            case [1,-1]:
                $expenses = Expense::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get();
                break;
            case [2,-1]:
                if (date('m') == 01) {
                    $expenses = Expense::whereMonth('created_at', 12)->whereYear('created_at', date('Y') - 1)->get();
                } else {
                    $expenses = Expense::whereMonth('created_at', date('m') - 1)->whereYear('created_at', date('Y'))->get();
                }
                break;
            case [3,-1]:
                if($month >= 1 && $month <= 3)
                {
                    $start_date = date("Y-m-d",strtotime('1-January-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-April-'.$year));
                }
                else  if($month >= 4 && $month <= 6)
                {
                    $start_date = date("Y-m-d",strtotime('1-April-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-July-'.$year));
                }
                else  if($month >= 7 && $month <= 9)
                {
                    $start_date = date("Y-m-d",strtotime('1-July-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-October-'.$year));
                }
                else  if($month >= 10 && $month <= 12)
                {
                    $start_date = date("Y-m-d",strtotime('1-October-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-January-'.($year+1)));
                }
                $expenses = Expense::whereBetween('created_at', [$start_date, $end_date])->get();
                break;
            case [4,-1]:
                $expenses = Expense::whereYear('created_at', date('Y'))->get();
                break;
            case [0, $branchId > 0]:
                $expenses = Expense::where('branch_id',$branchId)->get();
                break;
            case [1, $branchId > 0]:
                $expenses = Expense::where('branch_id',$branchId)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get();
                break;
            case [2, $branchId > 0]:
                if (date('m') == 01) {
                    $expenses = Expense::where('branch_id',$branchId)->whereMonth('created_at', 12)->whereYear('created_at', date('Y') - 1)->get();
                } else {
                    $expenses = Expense::where('branch_id',$branchId)->whereMonth('created_at', date('m') - 1)->whereYear('created_at', date('Y'))->get();
                }
                break;
            case [3, $branchId > 0]:
                if($month >= 1 && $month <= 3)
                {
                    $start_date = date("Y-m-d",strtotime('1-January-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-April-'.$year));
                }
                else  if($month >= 4 && $month <= 6)
                {
                    $start_date = date("Y-m-d",strtotime('1-April-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-July-'.$year));
                }
                else  if($month >= 7 && $month <= 9)
                {
                    $start_date = date("Y-m-d",strtotime('1-July-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-October-'.$year));
                }
                else  if($month >= 10 && $month <= 12)
                {
                    $start_date = date("Y-m-d",strtotime('1-October-'.$year));
                    $end_date = date("Y-m-d",strtotime('1-January-'.($year+1)));
                }
                $expenses = Expense::where('branch_id',$branchId)->whereBetween('created_at', [$start_date, $end_date])->get();
                break;
            case [4, $branchId > 0]:
                $expenses = Expense::where('branch_id',$branchId)->whereYear('created_at', date('Y'))->get();
                break;
        }
        $expenseAmount = 0;
        foreach($expenses as $expense) {
            $expenseAmount += $expense->total;
        }
        return $expenseAmount;
    }

}
