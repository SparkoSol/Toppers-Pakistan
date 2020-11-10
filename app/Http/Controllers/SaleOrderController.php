<?php

namespace App\Http\Controllers;

use App\SaleOrder;
use App\SaleOrderItem;
use App\Customer;
use App\CustomerTransaction;
use App\SaleOrderItemTransaction;
use App\SaleOrderCustomerTransaction;
use App\Item;
use App\ItemTransaction;
use App\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailSenderApi;
use PDF;


class SaleOrderController extends Controller
{
    public function getInvoiceNumber() {
        $invoice = SaleOrder::latest()->first();
        if($invoice) {
            return $invoice->invoice_id + 1;
        } else {
            return 1;
        }
    }
    public function getSale() {
        return SaleOrder::with('customer')->with('branch')->with('address')->get();
    }
    public function filter($id,$branchId) {
        $month = date("m");
        $year = date('Y');
        switch ([$id, $branchId]) {
            case [4,-1]:
                return SaleOrder::with('customer')->with('branch')->get();
            case [0,-1]:
                return SaleOrder::whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
            case [1,-1]:
                return SaleOrder::whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
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
                return SaleOrder::whereBetween('invoice_date', [$start_date, $end_date])->with('customer')->with('branch')->get();
            case [3,-1]:
                return SaleOrder::whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
            case [4, $branchId > 0]:
                return SaleOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
            case [0,$branchId > 0]:
                return SaleOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
            case [1,$branchId > 0]:
                return SaleOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
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
                return SaleOrder::where('branch_id',$branchId)->whereBetween('invoice_date', [$start_date, $end_date])->with('customer')->with('branch')->get();
            case [3,$branchId > 0]:
                return SaleOrder::where('branch_id',$branchId)->whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
        }
    }
    public function customFilter($id) {
        if ($id === '-1') {
            return SaleOrder::whereBetween('invoice_date', [request('from'), request('to')])->with('customer')->with('branch')->get();
        } else {
            return SaleOrder::where('branch_id',$id)->whereBetween('invoice_date', [request('from'), request('to')])->with('customer')->with('branch')->get();
        }
    }
    public function getSaleById($id) {
        return SaleOrder::where('id',$id)->with('customer')->with('branch')->first();
    }
    public function getSaleByBranch($branchId) {
        if ($branchId === '-1') {
            return SaleOrder::where('return_status', null)->with('branch')->get();
        } else {
            return SaleOrder::where('branch_id',$branchId)->where('return_status', null)->with('branch')->get();
        }
    }
    public function getSalesByCustomer($id,$branchId) {
        if ($branchId === '-1') {
            return SaleOrder::where('customer_id',$id)->where('return_status', null)->with('branch')->get();
        } else {
            return SaleOrder::where('customer_id',$id)->where('branch_id',$branchId)->where('return_status', null)->with('branch')->get();
        }
    }
    public function store() {
    try {
        // sale order data
        $sale = new SaleOrder();
        $sale->invoice_date = request('invoiceDate');
        $sale->invoice_id = request('invoiceNumber');
        $sale->customer_id = request('customer');
        $sale->branch_id = request('branchId');
        if (request('address_id') != null)
            $sale->address_id = request('address_id');
        if(request('instruction') != null)
            $sale->instructions = request('instruction');
        if(request('origin') != null)
            $sale->origin = request('origin');
        if(request('delivery') != null)
            $sale->delivery = request('delivery');
        $sale->payment_type = request('type');
        if (request('billingAddress') !== 'null')
        $sale->billing_address = request('billingAddress');
        $sale->amount = request('amount');
        if (request('balance') !== 'null')
        $sale->balance_due = request('balance');
        if (request('discount') !== 'null')
        $sale->discount = request('discount');
        $sale->save();
        $qty = 0;
        if ($sale->balance_due) {
            if($sale->balance_due == $sale->amount) {
                $status = 'Unpaid';
            } else {
                $status = 'Partial';
            }
        } else {
            $status = 'Paid';
        }
        $customer = Customer::where('id', request('customer'))->first();
        foreach (request('items') as $item) {
            error_log('In Item List');
            $product = Item::where('id', $item['item']['product']['id'])->first();
            // sale order item data
            $qty = $qty + $item['item']['qty'];
            $saleItem = new SaleOrderItem();
            $saleItem->sale_order_id = $sale->id;
            $saleItem->item_id = $item['item']['product']['id'];
            if ($item['item']['variant'] !== null) {
                $saleItem->variant_id = $item['item']['variant']['id'];
            }
            $saleItem->qty = $item['item']['qty'];
            $saleItem->price = $item['item']['price'];
            $saleItem->save();

            // product transactions

            $productTransaction = new ItemTransaction();
            if ($customer) {
                $productTransaction->customer_id = $customer->id;
            }
            $productTransaction->item_id = $saleItem->item_id;
            $productTransaction->variant_id = $saleItem->variant_id;
            $productTransaction->quantity = $saleItem->qty;
            if($saleItem->variant_id !== null) {
                $productTransaction->price =  $item['item']['variant']['purchase_price'];
            } else {
                $productTransaction->price =  $product->purchase_price;
            }
            $productTransaction->type = 3;
            $productTransaction->date = $sale->invoice_date;
            if ($sale->balance_due) {
                if($sale->balance_due == $sale->amount) {
                    $productTransaction->status = 'Unpaid';
                } else {
                    $productTransaction->status = 'Partial';
                }
            } else {
                $productTransaction->status = 'Paid';
            }
            $productTransaction->save();
            $saleProductTransaction = new SaleOrderItemTransaction();
            $saleProductTransaction->sale_order_id = $sale->id;
            $saleProductTransaction->transaction_id = $productTransaction->id;
            $saleProductTransaction->save();
            if($saleItem->variant_id === null) {
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
        // customer transactions
        if ($customer) {
            $customerTransaction = new CustomerTransaction();
            $customerTransaction->customer_id = $customer->id;
            $customerTransaction->quantity = $qty;
            $customerTransaction->value = request('amount');
            if (request('balance') !== 'null')
                $customerTransaction->balance = request('balance');
            $customerTransaction->action_type = 3;
            $customerTransaction->date = request('invoiceDate');
            $customerTransaction->status = $status;
            $customerTransaction->save();
            $customerTransactions = CustomerTransaction::where('customer_id', request('customer'))->get();
            $totalAmount = 0;
            $totalBalance = 0;
            foreach($customerTransactions as $value) {
                $totalBalance = $totalBalance + $value->balance;
            }
            $customerBalance = $totalBalance;

            Customer::where('id', request('customer'))->update([
                'balance' => $customerBalance
            ]);
            error_log('here');
            $saleCustomerTransaction = new SaleOrderCustomerTransaction();
            $saleCustomerTransaction->sale_order_id = $sale->id;
            $saleCustomerTransaction->transaction_id = $customerTransaction->id;
            $saleCustomerTransaction->save();
        }
        return $sale;
        } catch (\Throwable $e) {
            error_log($e);
        }
    }
    public function update($id) {
        try {
        // delete item transactions
        $itemTransactions = SaleOrderItemTransaction::where('sale_order_id', $id)->get();
        foreach ($itemTransactions as $itemTransaction) {
            SaleOrderItemTransaction::where('id', $itemTransaction->id)->delete();
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
    // delete sale order items
        $saleOrderItems = SaleOrderItem::where('sale_order_id', $id)->get();
        foreach ($saleOrderItems as $saleOrderItem) {
            SaleOrderItem::where('id', $saleOrderItem->id)->delete();
        }
    // update sale order data
        if (request('billingAddress') !== 'null')
            $billing_address = request('billingAddress');
        $amount = request('amount');
        if (request('balance') !== 'null')
            $balance_due = request('balance');
        if (request('discount') !== 'null')
            $discount = request('discount');


        SaleOrder::where('id', $id)->update([
            'billing_address' => $billing_address,
            'amount' => $amount,
            'balance_due' => $balance_due,
            'discount' => $discount
        ]);
        $sale = SaleOrder::where('id', $id)->first();

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
        $customer = Customer::where('id', request('customer'))->first();
        foreach (request('items') as $item) {
            $product = Item::where('id', $item['item']['product']['id'])->first();
            // sale order item data
            $qty = $qty + $item['item']['qty'];
            $saleItem = new SaleOrderItem();
            $saleItem->sale_order_id = $sale->id;
            $saleItem->item_id = $item['item']['product']['id'];
            if ($item['item']['variant'] !== null) {
                $saleItem->variant_id = $item['item']['variant']['id'];
            }
            $saleItem->qty = $item['item']['qty'];
            $saleItem->price = $item['item']['price'];
            $saleItem->save();

            // product transactions

            $productTransaction = new ItemTransaction();
            if ($customer) {
                $productTransaction->customer_id = $customer->id;
            }
            $productTransaction->item_id = $saleItem->item_id;
            $productTransaction->variant_id = $saleItem->variant_id;
            $productTransaction->quantity = $saleItem->qty;
            if($saleItem->variant_id !== null) {
                $productTransaction->price =  $item['item']['variant']['purchase_price'];
            } else {
                $productTransaction->price =  $product->purchase_price;
            }
            $productTransaction->type = 3;
            $productTransaction->date = $sale->invoice_date;
            if ($sale->balance_due) {
                if($sale->balance_due == $sale->amount) {
                    $productTransaction->status = 'Unpaid';
                } else {
                    $productTransaction->status = 'Partial';
                }
            } else {
                $productTransaction->status = 'Paid';
            }
            $productTransaction->save();
            $saleProductTransaction = new SaleOrderItemTransaction();
            $saleProductTransaction->sale_order_id = $sale->id;
            $saleProductTransaction->transaction_id = $productTransaction->id;
            $saleProductTransaction->save();
            if($saleItem->variant_id === null) {
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
        // customer transactions
        if ($customer) {
            $saleCustomerTransaction = SaleOrderCustomerTransaction::where('sale_order_id', $id)->first();
            CustomerTransaction::where('id', $saleCustomerTransaction->transaction_id)->update([
                'quantity' => $qty,
                'value' => request('amount'),
                'balance' => request('balance'),
                'status' =>$status
            ]);
            $customerTransactions = CustomerTransaction::where('customer_id', request('customer'))->get();
            $totalAmount = 0;
            $totalBalance = 0;
            foreach($customerTransactions as $value) {
                error_log($value);
                $totalBalance = $totalBalance + $value->balance;
            }
            $customerBalance = $totalBalance;

            Customer::where('id', request('customer'))->update([
                'balance' => $customerBalance
            ]);
        }

        return $sale;
        } catch (\Throwable $e) {
            error_log($e);
            return response()->json([
                'error' => $e
            ],200);
        }
    }
    public function getSummary($id, $branchId) {
        $month = date("m");
        $year = date('Y');
        $sales = [];
        switch ([$id, $branchId]) {
            case [4,-1]:
                $sales = SaleOrder::All();
                break;
            case [0,-1]:
                $sales = SaleOrder::whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                break;
            case [1,-1]:
                $sales = SaleOrder::whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
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
                $sales = SaleOrder::whereBetween('invoice_date', [$start_date, $end_date])->get();
                break;
            case [3,-1]:
                $sales = SaleOrder::whereYear('invoice_date', date('Y'))->get();
                break;
            case [4, $branchId > 0]:
                $sales = SaleOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                break;
            case [0,$branchId > 0]:
                $sales = SaleOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->get();
                break;
            case [1,$branchId > 0]:
                $sales = SaleOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->get();
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
                $sales = SaleOrder::where('branch_id',$branchId)->whereBetween('invoice_date', [$start_date, $end_date])->get();
                break;
            case [3,$branchId > 0]:
                $sales = SaleOrder::where('branch_id',$branchId)->whereYear('invoice_date', date('Y'))->get();
                break;
        }
        $unpaid = 0;
        $paid = 0;
        $total = 0;
        foreach ($sales as $sale) {
            $total = $total + $sale->amount;
            $unpaid = $unpaid + $sale->balance_due;
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
            $sales = SaleOrder::whereBetween('invoice_date', [request('from'), request('to')])->with('customer')->with('branch')->get();
        } else {
            $sales = SaleOrder::where('branch_id',$id)->whereBetween('invoice_date', [request('from'), request('to')])->with('customer')->with('branch')->get();
        }
        $unpaid = 0;
        $paid = 0;
        $total = 0;
        foreach ($sales as $sale) {
            $total = $total + $sale->amount;
            $unpaid = $unpaid + $sale->balance_due;
        }
        $paid = $total - $unpaid;
        return response()->json([
            'total' => $total,
            'paid' => $paid,
            'unpaid' => $unpaid
        ],200);
    }
    public function delete($id) {
        try {
            SaleOrder::where('id', $id)->delete();
            return response()->json([
                'message' => 'Order Deleted Successfully'
            ],200);
        } catch(\Throwable $e) {
            return response()->json([
                'error' => 'Sale Order Cannot Be Deleted'
            ],200);
        }
    }
    public function markAsPaid($id) {
        try {
            // get sale order
            $saleOrder = SaleOrder::where('id', $id)->first();
            // set sale order balance to zero
            SaleOrder::where('id',$id)->update([
                'balance_due' => 0
            ]);
            // get sale order item transactions
            $saleOrderItemTransactions = SaleOrderItemTransaction::where('sale_order_id',$id)->get();
            foreach ($saleOrderItemTransactions as $saleOrderItemTransaction) {
            // set item transaction status to paid
                ItemTransaction::where('id', $saleOrderItemTransaction->transaction_id)->update([
                    'status' => 'Paid'
                ]);
            }
            // get sale order customer transaction
            $saleOrderCustomerTransaction = SaleOrderCustomerTransaction::where('sale_order_id',$id)->first();
            // set customer transaction balance to zero
            CustomerTransaction::where('id', $saleOrderCustomerTransaction->transaction_id)->update([
                'balance' => 0,
                'status' => 'Paid'
            ]);
            // recalculate customer balance
            $customerTransactions = CustomerTransaction::where('customer_id', $saleOrder->customer_id)->get();
            $totalAmount = 0;
            $totalBalance = 0;
            foreach($customerTransactions as $value) {
                error_log($value);
                $totalBalance = $totalBalance + $value->balance;
            }
            $customerBalance = $totalBalance;

            Customer::where('id', $saleOrder->customer_id)->update([
                'balance' => $customerBalance
            ]);
        } catch (\Throwable $e) {
            error_log($e);
        }
    }
    public function markAsComplete($id) {
        SaleOrder::where('id',$id)->update([
            'delivery_status' => 'Complete'
        ]);
    }
    public function sendEmailApi($id) {
        $saleOrder = SaleOrder::where('id',$id)->with('customer')->with('branch')->with('address')->first();
        $saleOrderItems = SaleOrderItem::where('sale_order_id',$id)->with('variant')->with('product')->get();
        $data = array(
            'customer' => $saleOrder->customer,
            'order' => $saleOrder,
            'address' => $saleOrder->address,
            'branch' => $saleOrder->branch['name'],
            'price' => $saleOrder->amount,
            'delivery' => $saleOrder->delivery,
            'items' => $saleOrderItems,
            'orderId' => $saleOrder->invoice_id
        );

        Mail::to($saleOrder->customer['email'])->send(
            new MailSenderApi($data,'Order Placed')
        );
    }

    public function pdf($id) {
        $saleOrder = SaleOrder::where('id',$id)->with('customer')->with('address')->with('branch')->first();
        $saleOrderItems = SaleOrderItem::where('sale_order_id',$id)->with('product')->with('variant')->get();
        $size = 0;
        foreach ($saleOrderItems as $saleOrderItem) {
            if ($saleOrderItem->variant != null) {
                $size += 50;
            } else {
                $size += 15;
            }
        }
        if ($saleOrder->customer != null) {
            $size += 50;
        }
        error_log($size);
        return PDF::loadView('sale-order-receipt', array('saleOrder' => $saleOrder, 'items' => $saleOrderItems))->setPaper(array(0,0,220,300 + $size), 'portrait')->setWarnings(false)->stream('receipt.pdf');
    }

    public function printReport($id, $branchId) {
        $month = date("m");
        $year = date('Y');
        $to = null;
        $from = null;
        $sales = [];
        switch ([$id, $branchId]) {
            case [4,-1]:
                $sales = SaleOrder::with('customer')->with('branch')->get();
                break;
            case [0,-1]:
                $sales = SaleOrder::whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
                break;
            case [1,-1]:
                $sales = SaleOrder::whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
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
                $sales = SaleOrder::whereBetween('invoice_date', [$start_date, $end_date])->with('customer')->with('branch')->get();
                break;
            case [3,-1]:
                $sales = SaleOrder::whereYear('invoice_date', date('Y'))->get();
                break;
            case [5,-1]:
                error_log('here');
                if (request('from') != null && request('to') != null) {
                    $to = request('to');
                    $from = request('from');
                    error_log('if here');
                    $sales = SaleOrder::whereBetween('invoice_date', [request('from'), request('to')])->with('customer')->with('branch')->get();
                } else {
                    error_log('else here');
                    $sales = SaleOrder::with('customer')->with('branch')->get();
                }
                break;
            case [4, $branchId > 0]:
                $sales = SaleOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
                break;
            case [0,$branchId > 0]:
                $sales = SaleOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
                break;
            case [1,$branchId > 0]:
                $sales = SaleOrder::where('branch_id',$branchId)->whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
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
                $sales = SaleOrder::where('branch_id',$branchId)->whereBetween('invoice_date', [$start_date, $end_date])->with('customer')->with('branch')->get();
                break;
            case [3,$branchId > 0]:
                $sales = SaleOrder::where('branch_id',$branchId)->whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
                break;
            case [5,$branchId > 0]:
                if (request('from') != null && request('to') != null) {
                    $to = request('to');
                    $from = request('from');
                    $sales = SaleOrder::where('branch_id',$branchId)->whereBetween('invoice_date', [request('from'), request('to')])->with('customer')->with('branch')->get();
                } else {
                    $sales = SaleOrder::where('branch_id',$branchId)->with('customer')->with('branch')->get();
                }
                break;
        }
        return PDF::loadView('sale-report-1', array('sales' => $sales, 'to' => $to, 'from' => $from))->setPaper('a4', 'portrait')->setWarnings(false)->stream('receipt.pdf');
    }
}
