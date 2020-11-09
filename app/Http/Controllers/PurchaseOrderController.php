<?php

namespace App\Http\Controllers;

use App\PurchaseOrder;
use App\PurchaseOrderItem;
use App\Supplier;
use App\SupplierTransaction;
use App\PurchaseOrderItemTransaction;
use App\PurchaseOrderSupplierTransaction;
use App\Item;
use App\ItemTransaction;
use App\Variant;
use Illuminate\Http\Request;
use PDF;

class PurchaseOrderController extends Controller
{
    public function getInvoiceNumber() {
        $invoice = PurchaseOrder::latest()->first();
        if($invoice) {
            return $invoice->invoice_id + 1;
        } else {
            return 1;
        }
    }
    public function getPurchase() {
        return PurchaseOrder::with('supplier')->with('branch')->get();
    }
    public function filter($id,$branchId) {
        $month = date("m");
        $year = date('Y');
        switch ([$id, $branchId]) {
            case [4,-1]:
                return PurchaseOrder::with('supplier')->with('branch')->get();
            case [0,-1]:
                return PurchaseOrder::whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->with('supplier')->with('branch')->get();
            case [1,-1]:
                return PurchaseOrder::whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->with('supplier')->with('branch')->get();
            case [2,-1]:
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
                return PurchaseOrder::whereBetween('invoice_date', [$start_date, $end_date])->with('supplier')->with('branch')->get();
            case [3,-1]:
                return PurchaseOrder::whereYear('invoice_date', date('Y'))->with('supplier')->with('branch')->get();
            case [4, $branchId > 0]:
                return PurchaseOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->with('supplier')->with('branch')->get();
            case [0,$branchId > 0]:
                return PurchaseOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->with('supplier')->with('branch')->get();
            case [1,$branchId > 0]:
                return PurchaseOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->with('supplier')->with('branch')->get();
            case [2,$branchId > 0]:
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
                return PurchaseOrder::where('branch_id',$branchId)->whereBetween('invoice_date', [$start_date, $end_date])->with('supplier')->with('branch')->get();
            case [3,$branchId > 0]:
                return PurchaseOrder::where('branch_id',$branchId)->whereYear('invoice_date', date('Y'))->with('supplier')->with('branch')->get();
        }
    }
    public function customFilter($id) {
        if ($id === '-1') {
            return PurchaseOrder::whereBetween('invoice_date', [request('from'), request('to')])->with('supplier')->with('branch')->get();
        } else {
            return PurchaseOrder::where('branch_id',$id)->whereBetween('invoice_date', [request('from'), request('to')])->with('supplier')->with('branch')->get();
        }
    }
    public function getPurchaseById($id) {
        return PurchaseOrder::where('id',$id)->with('supplier')->with('branch')->first();
    }
    public function getPurchaseBySupplier($id,$branchId) {
        if ($branchId === '-1') {
            return PurchaseOrder::where('supplier_id',$id)->where('return_status', null)->with('branch')->get();
        } else {
            return PurchaseOrder::where('branch_id',$branchId)->where('supplier_id',$id)->where('return_status', null)->with('branch')->get();
        }
    }
    public function getSummary($id, $branchId) {
        $month = date("m");
        $year = date('Y');
        $purchases = [];
        switch ([$id, $branchId]) {
            case [4,-1]:
                $purchases = PurchaseOrder::All();
                break;
            case [0,-1]:
                $purchases = PurchaseOrder::whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                break;
            case [1,-1]:
                $purchases = PurchaseOrder::whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
                break;
            case [2,-1]:
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
                $purchases = PurchaseOrder::whereBetween('invoice_date', [$start_date, $end_date])->get();
                break;
            case [3,-1]:
                $purchases = PurchaseOrder::whereYear('invoice_date', date('Y'))->get();
                break;
            case [4, $branchId > 0]:
                $purchases = PurchaseOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                break;
            case [0,$branchId > 0]:
                $purchases = PurchaseOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                break;
            case [1,$branchId > 0]:
                $purchases = PurchaseOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
                break;
            case [2,$branchId > 0]:
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
                $purchases = PurchaseOrder::where('branch_id',$branchId)->whereBetween('invoice_date', [$start_date, $end_date])->get();
                break;
            case [3,$branchId > 0]:
                $purchases = PurchaseOrder::where('branch_id',$branchId)->whereYear('invoice_date', date('Y'))->get();
                break;
        }
        $unpaid = 0;
        $paid = 0;
        $total = 0;
        foreach ($purchases as $purchase) {
            $total = $total + $purchase->amount;
            $unpaid = $unpaid + $purchase->balance_due;
        }
        $paid = $total - $unpaid;
        return response()->json([
            'total' => $total,
            'paid' => $paid,
            'unpaid' => $unpaid
        ],200);
    }
    public function customSummary($id) {
        if ($id === '-1') {
            $purchases = PurchaseOrder::whereBetween('invoice_date', [request('from'), request('to')])->get();
        } else {
            $purchases = PurchaseOrder::where('branch_id',$id)->whereBetween('invoice_date', [request('from'), request('to')])->get();
        }
        $unpaid = 0;
        $paid = 0;
        $total = 0;
        foreach ($purchases as $purchase) {
            $total = $total + $purchase->amount;
            $unpaid = $unpaid + $purchase->balance_due;
        }
        $paid = $total - $unpaid;
        return response()->json([
            'total' => $total,
            'paid' => $paid,
            'unpaid' => $unpaid
        ],200);
    }
    public function store() {
    try {
        // purchase order data
        $purchase = new PurchaseOrder();
        $purchase->invoice_date = request('invoiceDate');
        $purchase->invoice_id = request('invoiceNumber');
        $purchase->supplier_id = request('supplier');
        $purchase->branch_id = request('branchId');
        $purchase->payment_type = request('type');
        if (request('billingAddress') !== 'null')
        $purchase->billing_address = request('billingAddress');
        $purchase->amount = request('amount');
        if (request('balance') !== 'null')
        $purchase->balance_due = request('balance');
        $purchase->save();
        $qty = 0;
        if ($purchase->balance_due) {
            if($purchase->balance_due == $purchase->amount) {
                $status = 'Unpaid';
            } else {
                $status = 'Partial';
            }
        } else {
            $status = 'Paid';
        }
        $supplier = Supplier::where('id', request('supplier'))->first();
        error_log($supplier);
        foreach (request('items') as $item) {
            $product = Item::where('id', $item['item']['product']['id'])->first();
            // purchase order item data
            $qty = $qty + $item['item']['qty'];
            $purchaseItem = new PurchaseOrderItem();
            $purchaseItem->purchase_order_id = $purchase->id;
            $purchaseItem->item_id = $item['item']['product']['id'];
            if ($item['item']['variant'] !== null) {
                $purchaseItem->variant_id = $item['item']['variant']['id'];
            }
            $purchaseItem->qty = $item['item']['qty'];
            $purchaseItem->price = $item['item']['price'];
            $purchaseItem->save();

            // product transactions

            $productTransaction = new ItemTransaction();
            if ($supplier) {
                $productTransaction->supplier_id = $supplier->id;
            }
            $productTransaction->item_id = $purchaseItem->item_id;
            $productTransaction->variant_id = $purchaseItem->variant_id;
            $productTransaction->quantity = $purchaseItem->qty;
            if($purchaseItem->variant_id !== null) {
                $productTransaction->price =  $item['item']['variant']['purchase_price'];
            } else {
                $productTransaction->price =  $product->purchase_price;
            }
            $productTransaction->type = 6;
            $productTransaction->date = $purchase->invoice_date;
            if ($purchase->balance_due) {
                if($purchase->balance_due == $purchase->amount) {
                    $productTransaction->status = 'Unpaid';
                } else {
                    $productTransaction->status = 'Partial';
                }
            } else {
                $productTransaction->status = 'Paid';
            }
            $productTransaction->save();
            $purchaseProductTransaction = new PurchaseOrderItemTransaction();
            $purchaseProductTransaction->purchase_order_id = $purchase->id;
            $purchaseProductTransaction->transaction_id = $productTransaction->id;
            $purchaseProductTransaction->save();
            if($purchaseItem->variant_id === null) {
                error_log('here');
                $itemTransactions = ItemTransaction::where('item_id', $item['item']['product']['id'])->where('active',1)->get();
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
                Item::where('id', $item['item']['product']['id'])->update([
                    'stock' => $stock,
                    'stock_value' => $stock_value
                ]);
            } else {
                $itemTransactions = ItemTransaction::where('variant_id', $item['item']['variant']['id'])->where('active', 1)->get();
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
                Variant::where('id', $item['item']['variant']['id'])->update([
                    'stock' => $stock,
                    'stock_value' => $stock_value
                ]);
            }
        }
        // supplier transactions
        if ($supplier) {
            $supplierTransaction = new SupplierTransaction();
            $supplierTransaction->supplier_id = $supplier->id;
            $supplierTransaction->quantity = $qty;
            $supplierTransaction->value = request('amount');
            if (request('balance') !== 'null')
                $supplierTransaction->balance = - request('balance');
            $supplierTransaction->action_type = 6;
            $supplierTransaction->date = request('invoiceDate');
            $supplierTransaction->status = $status;
            $supplierTransaction->save();
            $supplierTransactions = SupplierTransaction::where('supplier_id', request('supplier'))->get();
            $totalAmount = 0;
            $totalBalance = 0;
            foreach($supplierTransactions as $value) {
                $totalBalance = $totalBalance + $value->balance;
            }
            $supplierBalance = $totalBalance;

            Supplier::where('id', request('supplier'))->update([
                'balance' => $supplierBalance
            ]);
            error_log('here');
            $purchaseSupplierTransaction = new PurchaseOrderSupplierTransaction();
            $purchaseSupplierTransaction->purchase_order_id = $purchase->id;
            $purchaseSupplierTransaction->transaction_id = $supplierTransaction->id;
            $purchaseSupplierTransaction->save();
        }
        return $purchase;
        } catch (\Throwable $e) {
            error_log($e);
        }
    }
    public function update($id) {
        try {
        // delete item transactions
        $itemTransactions = PurchaseOrderItemTransaction::where('purchase_order_id', $id)->get();
        foreach ($itemTransactions as $itemTransaction) {
            PurchaseOrderItemTransaction::where('id', $itemTransaction->id)->delete();
            $history = ItemTransaction::where('id', $itemTransaction->transaction_id)->first();
            ItemTransaction::where('id', $itemTransaction->transaction_id)->delete();
            if($history->variant_id === null) {
                $itemTransactions = ItemTransaction::where('item_id', $history->item_id)->where('active',1)->get();
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
                Item::where('id', $history->item_id)->update([
                    'stock' => $stock,
                    'stock_value' => $stock_value
                ]);
            } else {
                $itemTransactions = ItemTransaction::where('variant_id', $history->variant_id)->where('active', 1)->get();
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
                Variant::where('id', $history->variant_id)->update([
                    'stock' => $stock,
                    'stock_value' => $stock_value
                ]);
            }
        }
    // delete purchase order items
        $purchaseOrderItems = PurchaseOrderItem::where('purchase_order_id', $id)->get();
        foreach ($purchaseOrderItems as $purchaseOrderItem) {
            PurchaseOrderItem::where('id', $purchaseOrderItem->id)->delete();
        }
    // update purchase order data
        if (request('billingAddress') !== 'null')
            $billing_address = request('billingAddress');
        $amount = request('amount');
        if (request('balance') !== 'null')
            $balance_due = request('balance');

        PurchaseOrder::where('id', $id)->update([
            'billing_address' => $billing_address,
            'amount' => $amount,
            'balance_due' => $balance_due
        ]);
        $purchase = PurchaseOrder::where('id', $id)->first();

        if ($balance_due) {
            if($balance_due == $amount) {
                $status = 'Unpaid';
            } else {
                $status = 'Partial';
            }
        } else {
            $status = 'Paid';
        }
        $qty = 0;
        $supplier = Supplier::where('id', request('supplier'))->first();
        foreach (request('items') as $item) {
            $product = Item::where('id', $item['item']['product']['id'])->first();
            // purchase order item data
            $qty = $qty + $item['item']['qty'];
            $purchaseItem = new PurchaseOrderItem();
            $purchaseItem->purchase_order_id = $purchase->id;
            $purchaseItem->item_id = $item['item']['product']['id'];
            if ($item['item']['variant'] !== null) {
                $purchaseItem->variant_id = $item['item']['variant']['id'];
            }
            $purchaseItem->qty = $item['item']['qty'];
            $purchaseItem->price = $item['item']['price'];
            $purchaseItem->save();

            // product transactions

            $productTransaction = new ItemTransaction();
            if ($supplier) {
                $productTransaction->supplier_id = $supplier->id;
            }
            $productTransaction->item_id = $purchaseItem->item_id;
            $productTransaction->variant_id = $purchaseItem->variant_id;
            $productTransaction->quantity = $purchaseItem->qty;
            if($purchaseItem->variant_id !== null) {
                $productTransaction->price =  $item['item']['variant']['purchase_price'];
            } else {
                $productTransaction->price =  $product->purchase_price;
            }
            $productTransaction->type = 6;
            $productTransaction->date = $purchase->invoice_date;
            if ($purchase->balance_due) {
                if($purchase->balance_due == $purchase->amount) {
                    $productTransaction->status = 'Unpaid';
                } else {
                    $productTransaction->status = 'Partial';
                }
            } else {
                $productTransaction->status = 'Paid';
            }
            $productTransaction->save();
            $purchaseProductTransaction = new PurchaseOrderItemTransaction();
            $purchaseProductTransaction->purchase_order_id = $purchase->id;
            $purchaseProductTransaction->transaction_id = $productTransaction->id;
            $purchaseProductTransaction->save();
            if($purchaseItem->variant_id === null) {
                error_log('here');
                $itemTransactions = ItemTransaction::where('item_id', $item['item']['product']['id'])->where('active',1)->get();
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
                Item::where('id', $item['item']['product']['id'])->update([
                    'stock' => $stock,
                    'stock_value' => $stock_value
                ]);
            } else {
                $itemTransactions = ItemTransaction::where('variant_id', $item['item']['variant']['id'])->where('active', 1)->get();
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
                Variant::where('id', $item['item']['variant']['id'])->update([
                    'stock' => $stock,
                    'stock_value' => $stock_value
                ]);
            }
        }
        // supplier transactions
        if ($supplier) {
            $purchaseSupplierTransaction = PurchaseOrderSupplierTransaction::where('purchase_order_id', $id)->first();
            SupplierTransaction::where('id', $purchaseSupplierTransaction->transaction_id)->update([
                'quantity' => $qty,
                'value' => request('amount'),
                'balance' => - request('balance'),
                'status' =>$status
            ]);
            $supplierTransactions = SupplierTransaction::where('supplier_id', request('supplier'))->get();
            $totalAmount = 0;
            $totalBalance = 0;
            foreach($supplierTransactions as $value) {
                error_log($value);
                $totalBalance = $totalBalance + $value->balance;
            }
            $supplierBalance = $totalBalance;

            Supplier::where('id', request('supplier'))->update([
                'balance' => $supplierBalance
            ]);
        }

        return $purchase;
        } catch (\Throwable $e) {
            error_log($e);
            return response()->json([
                'error' => $e
            ],200);
        }
    }
    public function delete($id) {
        try {
            PurchaseOrder::where('id', $id)->delete();
            return response()->json([
                'message' => 'Purchase Order Deleted Successfully'
            ],200);
        } catch(\Throwable $e) {
            return response()->json([
                'error' => 'Purchase Order Cannot Be Deleted'
            ],200);
        }
    }
    public function printReport($id, $branchId) {
        $month = date("m");
        $year = date('Y');
        $to = null;
        $from = null;
        $purchases = [];
        switch ([$id, $branchId]) {
            case [4,-1]:
                $purchases = PurchaseOrder::with('supplier')->with('branch')->get();
                break;
            case [0,-1]:
                $purchases = PurchaseOrder::whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->with('supplier')->with('branch')->get();
                break;
            case [1,-1]:
                $purchases = PurchaseOrder::whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->with('supplier')->with('branch')->get();
                break;
            case [2,-1]:
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
                $purchases = PurchaseOrder::whereBetween('invoice_date', [$start_date, $end_date])->with('supplier')->with('branch')->get();
                break;
            case [3,-1]:
                $purchases = PurchaseOrder::whereYear('invoice_date', date('Y'))->get();
                break;
            case [5,-1]:
                error_log('here');
                if (request('from') != null && request('to') != null) {
                    $to = request('to');
                    $from = request('from');
                    error_log('if here');
                    $purchases = PurchaseOrder::whereBetween('invoice_date', [request('from'), request('to')])->with('supplier')->with('branch')->get();
                } else {
                    error_log('else here');
                    $purchases = PurchaseOrder::with('supplier')->with('branch')->get();
                }
                break;
            case [4, $branchId > 0]:
                $purchases = PurchaseOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->with('supplier')->with('branch')->get();
                break;
            case [0,$branchId > 0]:
                $purchases = PurchaseOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->with('supplier')->with('branch')->get();
                break;
            case [1,$branchId > 0]:
                $purchases = PurchaseOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->with('supplier')->with('branch')->get();
                break;
            case [2,$branchId > 0]:
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
                $purchases = PurchaseOrder::where('branch_id',$branchId)->whereBetween('invoice_date', [$start_date, $end_date])->with('supplier')->with('branch')->get();
                break;
            case [3,$branchId > 0]:
                $purchases = PurchaseOrder::where('branch_id',$branchId)->whereYear('invoice_date', date('Y'))->with('supplier')->with('branch')->get();
                break;
            case [5,$branchId > 0]:
                if (request('from') != null && request('to') != null) {
                    $to = request('to');
                    $from = request('from');
                    $purchases = PurchaseOrder::where('branch_id',$branchId)->whereBetween('invoice_date', [request('from'), request('to')])->with('supplier')->with('branch')->get();
                } else {
                    $purchases = PurchaseOrder::where('branch_id',$branchId)->with('supplier')->with('branch')->get();
                }
                break;
        }
        return PDF::loadView('purchase-report-1', array('purchases' => $purchases, 'to' => $to, 'from' => $from))->setPaper('a4', 'portrait')->setWarnings(false)->stream('receipt.pdf');
    }
}
