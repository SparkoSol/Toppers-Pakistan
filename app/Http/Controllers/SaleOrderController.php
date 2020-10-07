<?php

namespace App\Http\Controllers;

use App\SaleOrder;
use App\SaleOrderItem;
use App\ProductHistory;
use App\Customer;
use App\Product;
use App\CustomerTransaction;
use App\SaleOrderProductTransaction;
use App\SaleOrderCustomerTransaction;
use Illuminate\Http\Request;

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
        return SaleOrder::with('customer')->with('branch')->get();
    }
    public function filter($id) {

        $month = date("m");
        $year = date('Y');

        switch ($id) {
            case 0:
                return SaleOrder::whereMonth('invoice_date', date('m'))->whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
            case 1:
                return SaleOrder::whereMonth('invoice_date', date('m') - 1)->whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
            case 2:
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
            case 3:
                return SaleOrder::whereYear('invoice_date', date('Y'))->with('customer')->with('branch')->get();
            default:
                return SaleOrder::with('customer')->with('branch')->get();
        }
    }
    public function customFilter() {
        return SaleOrder::whereBetween('invoice_date', [request('from'), request('to')])->with('customer')->with('branch')->get();
    }
    public function getSaleById($id) {
        return SaleOrder::where('id',$id)->with('customer')->with('branch')->first();
    }
    public function getSaleByCustomer($id) {
        return SaleOrder::where('customer_id',$id)->where('return_status', null)->with('branch')->get();
    }
    public function store() {
        // sale order data
        $sale = new SaleOrder();
        $sale->invoice_date = request('invoiceDate');
        $sale->invoice_id = request('invoiceNumber');
        $sale->customer_id = request('customer');
        $sale->branch_id = request('branchId');
        $sale->payment_type = request('type');
        if (request('billingAddress') !== 'null')
        $sale->billing_address = request('billingAddress');
        $sale->amount = request('amount');
        if (request('balance') !== 'null')
        $sale->balance_due = request('balance');
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
            $product = Product::where('id', $item['item_id'])->first();
            // sale order item data
            $qty = $qty + $item['qty'];
            $saleItem = new SaleOrderItem();
            $saleItem->sale_order_id = $sale->id;
            $saleItem->product_id = $item['item_id'];
            $saleItem->qty = $item['qty'];
            $saleItem->save();

            // product transactions

            $productHistory = new ProductHistory();
            if ($customer) {
                $productHistory->name = $customer->name;
            }
            $productHistory->product_id = $saleItem->product_id;
            $productHistory->quantity = $saleItem->qty;
            $productHistory->value =  $product->purchase_price;
            $productHistory->action_type = 3;
            $productHistory->date = $sale->invoice_date;
            if ($sale->balance_due) {
                if($sale->balance_due == $sale->amount) {
                    $productHistory->status = 'Unpaid';
                } else {
                    $productHistory->status = 'Partial';
                }
            } else {
                $productHistory->status = 'Paid';
            }

            $productHistory->save();
            $saleProductTransaction = new SaleOrderProductTransaction();
            $saleProductTransaction->sale_order_id = $sale->id;
            $saleProductTransaction->transaction_id = $productHistory->id;
            $saleProductTransaction->save();

            $productHistories = ProductHistory::where('product_id', $product->id)->get();
            $quantity = 0;
            $value = 0;
            foreach ($productHistories as $item) {
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
            Product::where('id', $product->id)->update([
                'stock' => $quantity,
                'stock_value' => $value
            ]);
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
    }
    public function update($id) {
        try {
        // delete product histories
        $productTransactions = SaleOrderProductTransaction::where('sale_order_id', $id)->get();
        foreach ($productTransactions as $productTransaction) {
            SaleOrderProductTransaction::where('id', $productTransaction->id)->delete();
            $history = ProductHistory::where('id', $productTransaction->transaction_id)->first();
            ProductHistory::where('id', $productTransaction->transaction_id)->delete();
            $productHistories = ProductHistory::where('product_id', $history->product_id)->get();
            $quantity = 0;
            $value = 0;
            foreach ($productHistories as $item) {
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
            Product::where('id', $history->product_id)->update([
                'stock' => $quantity,
                'stock_value' => $value
            ]);
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

        SaleOrder::where('id', $id)->update([
            'billing_address' => $billing_address,
            'amount' => $amount,
            'balance_due' => $balance_due
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
            $product = Product::where('id', $item['item_id'])->first();
            // sale order item data
            $qty = $qty + $item['qty'];
            $saleItem = new SaleOrderItem();
            $saleItem->sale_order_id = $id;
            $saleItem->product_id = $item['item_id'];
            $saleItem->qty = $item['qty'];
            $saleItem->save();

            // product transactions

            $productHistory = new ProductHistory();
            if ($customer) {
                $productHistory->name = $customer->name;
            }
            $productHistory->product_id = $saleItem->product_id;
            $productHistory->quantity = $saleItem->qty;
            $productHistory->value =  $product->purchase_price;
            $productHistory->action_type = 3;
            $productHistory->date = $sale->invoice_date;
            if ($sale->balance_due) {
                if($sale->balance_due == $sale->amount) {
                    $productHistory->status = 'Unpaid';
                } else {
                    $productHistory->status = 'Partial';
                }
            } else {
                $productHistory->status = 'Paid';
            }

            $productHistory->save();

            $saleProductTransaction = new SaleOrderProductTransaction();
            $saleProductTransaction->sale_order_id = $sale->id;
            $saleProductTransaction->transaction_id = $productHistory->id;
            $saleProductTransaction->save();

            $productHistories = ProductHistory::where('product_id', $product->id)->get();
            $quantity = 0;
            $value = 0;
            foreach ($productHistories as $item) {
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
            Product::where('id', $product->id)->update([
                'stock' => $quantity,
                'stock_value' => $value
            ]);
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
            return response()->json([
                'error' => $e
            ],200);
        }
    }
    public function getSummary() {
        $sales = SaleOrder::All();
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
}
