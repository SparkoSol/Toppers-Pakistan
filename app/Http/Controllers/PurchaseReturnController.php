<?php

namespace App\Http\Controllers;

use App\PurchaseReturn;
use App\PurchaseOrder;
use App\PurchaseOrderItem;
use App\PurchaseOrderItemTransaction;
use App\PurchaseOrderSupplierTransaction;
use App\PurchaseReturnSupplierTransaction;
use App\PurchaseReturnItemTransaction;
use App\Item;
use App\Supplier;
use App\SupplierTransaction;
use App\ItemTransaction;
use App\Variant;
use Illuminate\Http\Request;

class PurchaseReturnController extends Controller
{
    public function getInvoiceNumber() {
        $invoice = PurchaseReturn::latest()->first();
        if($invoice) {
            return $invoice->invoice_id + 1;
        } else {
            return 1;
        }
    }
    public function getPurchaseReturn($id) {
        if($id === '-1') {
            return PurchaseReturn::with('supplier')->with('PurchaseOrder.branch')->get();
        } else {
            return PurchaseReturn::where('branch_id',$id)->with('supplier')->with('PurchaseOrder.branch')->get();
        }
    }
    public function getPurchaseReturnById($id) {
        return PurchaseReturn::where('id', $id)->with('supplier')->with('PurchaseOrder.branch')->first();
    }
    public function customFilter($id) {
        if($id === '-1') {
            return PurchaseReturn::whereBetween('invoice_date', [request('from'), request('to')])->with('supplier')->with('PurchaseOrder.branch')->get();
        } else {
            return PurchaseReturn::where('branch_id',$id)->whereBetween('invoice_date', [request('from'), request('to')])->with('supplier')->with('PurchaseOrder.branch')->get();
        }
    }
    public function store() {
        try {
            $purchaseOrder = PurchaseOrder::where('id', request('purchase_order_id'))->first();
            // create purchase return
            $purchaseReturn = new PurchaseReturn();
            $purchaseReturn->invoice_id = request('invoiceId');
            $purchaseReturn->invoice_date = request('invoiceDate');
            $purchaseReturn->supplier_id = $purchaseOrder->supplier_id;
            $purchaseReturn->branch_id = $purchaseOrder->branch_id;
            $purchaseReturn->purchase_order_id = $purchaseOrder->id;
            $purchaseReturn->payment_type = request('paymentType');
            $purchaseReturn->total = $purchaseOrder->amount;
            $purchaseReturn->paid = request('paid');
            $purchaseReturn->balance = request('balance');
            $purchaseReturn->save();

            // get product transaction and duplicate with purchase return
            $purchaseOrderItemTransactions = PurchaseOrderItemTransaction::where('purchase_order_id', $purchaseOrder->id)->get();
            foreach($purchaseOrderItemTransactions as $purchaseOrderItemTransaction) {
                $productTransaction = ItemTransaction::where('id', $purchaseOrderItemTransaction->transaction_id)->first();
                $newItemTransaction = new ItemTransaction();
                $newItemTransaction->supplier_id = $productTransaction->supplier_id;
                $newItemTransaction->item_id = $productTransaction->item_id;
                $newItemTransaction->variant_id = $productTransaction->variant_id;
                $newItemTransaction->quantity = $productTransaction->quantity;
                $newItemTransaction->price = $productTransaction->price;
                $newItemTransaction->type = 8;
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
                $purchaseReturnItemTransaction = new PurchaseReturnItemTransaction();
                $purchaseReturnItemTransaction->purchase_return_id = $purchaseReturn->id;
                $purchaseReturnItemTransaction->transaction_id = $newItemTransaction->id;
                $purchaseReturnItemTransaction->save();
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
            // create supplier transactions
            $purchaseOrderSupplierTransaction = PurchaseOrderSupplierTransaction::where('purchase_order_id', $purchaseOrder->id)->first();
            $supplierTransaction = SupplierTransaction::where('id', $purchaseOrderSupplierTransaction->transaction_id)->first();
            $newSupplierTransaction = new SupplierTransaction();
            $newSupplierTransaction->supplier_id = $supplierTransaction->supplier_id;
            $newSupplierTransaction->quantity = $supplierTransaction->quantity;
            $newSupplierTransaction->value = $supplierTransaction->value;
            $newSupplierTransaction->balance = request('balance');
            $newSupplierTransaction->action_type = 8;
            $newSupplierTransaction->date = request('invoiceDate');
            if (request('balance')) {
                if (request('balance') != 0)
                    $newSupplierTransaction->status = 'Partial';
                else if (request('balance') == 0)
                    $newSupplierTransaction->status = 'Paid';
                else
                    $newSupplierTransaction->status = 'Unpaid';
            } else {
                $newSupplierTransaction->status = 'Paid';
            }
            $newSupplierTransaction->save();
            $supplierTransactions = SupplierTransaction::where('supplier_id', $supplierTransaction->supplier_id)->get();
            $totalBalance = 0;
            foreach($supplierTransactions as $value) {
                error_log($value);
                $totalBalance = $totalBalance + $value->balance;
            }
            Supplier::where('id', $supplierTransaction->supplier_id)->update([
                'balance' => $totalBalance
            ]);
            // create purchase return supplier transaction
            $purchaseReturnSupplierTransaction = new PurchaseReturnSupplierTransaction();
            $purchaseReturnSupplierTransaction->purchase_return_id = $purchaseReturn->id;
            $purchaseReturnSupplierTransaction->transaction_id = $newSupplierTransaction->id;
            $purchaseReturnSupplierTransaction->save();
            PurchaseOrder::where('id', request('purchase_order_id'))->update([
                'return_status' => true
            ]);
            return $purchaseReturn;
        } catch (\Throwable $e) {
            error_log($e);
            return response()->json([
                'error' => $e
            ],200);
        }
    }
    public function update($id) {
        try {
            PurchaseReturn::where('id', $id)->update([
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
            $purchaseReturnItemTransactions = PurchaseReturnItemTransaction::where('purchase_return_id', $id)->get();
            foreach ($purchaseReturnItemTransactions as $purchaseReturnItemTransaction) {
                ItemTransaction::where('id', $purchaseReturnItemTransaction->transaction_id)->update([
                    'status' => $status
                ]);
            }
            $purchaseReturnSupplierTransaction = PurchaseReturnSupplierTransaction::where('purchase_return_id', $id)->first();
            $supplierTransaction = SupplierTransaction::where('id', $purchaseReturnSupplierTransaction->transaction_id)->first();
            SupplierTransaction::where('id', $purchaseReturnSupplierTransaction->transaction_id)->update([
                'status' => $status,
                'balance' => request('balance')
            ]);
            $supplierTransactions = SupplierTransaction::where('supplier_id', $supplierTransaction->supplier_id)->get();
            $totalBalance = 0;
            foreach($supplierTransactions as $value) {
                error_log($value);
                $totalBalance = $totalBalance + $value->balance;
            }
            Supplier::where('id', $supplierTransaction->supplier_id)->update([
                'balance' => $totalBalance
            ]);
            return response()->json([
                'message' => 'Successfully Updated Purchase Return'
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
            PurchaseReturn::where('id', $id)->delete();
            return response()->json([
                'message' => 'Deleted Successfully'
            ],200);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Cannot delete Purchase Return'
            ],200);
        }
    }
}
