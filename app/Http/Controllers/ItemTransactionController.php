<?php

namespace App\Http\Controllers;

use App\Item;
use App\ItemTransaction;
use App\Variant;
use Illuminate\Http\Request;

class ItemTransactionController extends Controller
{
    public function getByItemId($id) {
        return ItemTransaction::where('item_id', $id)->with('customer')->with('variant')->orderBy('id','desc')->get();
    }
    public function getByVariantId($id) {
        return ItemTransaction::where('variant_id', $id)->with('customer')->with('variant')->orderBy('id','desc')->get();
    }
    public function storeNoVariant() {
        if(request('type') == 'Add Stock') {
            $type = 1;
        } else if (request('type') == 'Reduce Stock') {
            $type = 2;
        } else {
            $type = 0;
        }
        $itemTransaction = new ItemTransaction();
        $itemTransaction->item_id = request('item_id');
        $itemTransaction->quantity = request('quantity');
        $itemTransaction->price = request('price');
        $itemTransaction->type = $type;
        $itemTransaction->date = request('date');
        $itemTransaction->save();

        $itemTransactions = ItemTransaction::where('item_id', request('item_id'))->where('active',1)->get();
        $stock = 0;
        $stock_value = 0;
        foreach($itemTransactions as $itemTransaction) {
            if ($itemTransaction->type === 2 || $itemTransaction->type === 3) {
                $stock = $stock - $itemTransaction->quantity;
                $stock_value = $stock_value - ($itemTransaction->quantity * $itemTransaction->price);
            } else {
                $stock = $stock + $itemTransaction->quantity;
                $stock_value = $stock_value + ($itemTransaction->quantity * $itemTransaction->price);
            }
        }
        Item::where('id', request('item_id'))->update([
            'stock' => $stock,
            'stock_value' => $stock_value
        ]);
        return Item::where('id',request('item_id'))->first();
    }

    public function storeVariant() {
    try {
        if(request('type') == 'Add Stock') {
            $type = 1;
        } else if (request('type') == 'Reduce Stock') {
            $type = 2;
        } else {
            $type = 0;
        }
        $itemTransaction = new ItemTransaction();
        $itemTransaction->item_id = request('item_id');
        $itemTransaction->variant_id = request('variant_id');
        $itemTransaction->quantity = request('quantity');
        $itemTransaction->price = request('price');
        $itemTransaction->type = $type;
        $itemTransaction->date = request('date');
        $itemTransaction->save();

        $itemTransactions = ItemTransaction::where('variant_id', request('variant_id'))->where('active', 1)->get();
        $stock = 0;
        $stock_value = 0;
        foreach($itemTransactions as $itemTransaction) {
            if ($itemTransaction->type === 2 || $itemTransaction->type === 3) {
                $stock = $stock - $itemTransaction->quantity;
                $stock_value = $stock_value - ($itemTransaction->quantity * $itemTransaction->price);
            } else {
                $stock = $stock + $itemTransaction->quantity;
                $stock_value = $stock_value + ($itemTransaction->quantity * $itemTransaction->price);
            }
        }
        Variant::where('id', request('variant_id'))->update([
            'stock' => $stock,
            'stock_value' => $stock_value
        ]);
        return Variant::where('id',request('variant_id'))->first();
    } catch (\Throwable $e) {
        error_log($e);
    }
    }

    public function updateNoVariant($id) {
        if(request('type') == 'Add Stock') {
            $action = 1;
        } else if (request('type') == 'Reduce Stock') {
            $action = 2;
        } else {
            $action = 0;
        }
        ItemTransaction::where('id',$id)->update([
            'quantity' => request('quantity'),
            'price' => request('price'),
            'date' => request('date')
        ]);
        $itemTransactions = ItemTransaction::where('item_id', request('item_id'))->where('active',1)->get();
        $stock = 0;
        $stock_value = 0;
        foreach($itemTransactions as $itemTransaction) {
            if ($itemTransaction->type === 2 || $itemTransaction->type === 3) {
                $stock = $stock - $itemTransaction->quantity;
                $stock_value = $stock_value - ($itemTransaction->quantity * $itemTransaction->price);
            } else {
                $stock = $stock + $itemTransaction->quantity;
                $stock_value = $stock_value + ($itemTransaction->quantity * $itemTransaction->price);
            }
        }
        if ($action == 0) {
            $purchase_price = request('price');
            Item::where('id', request('item_id'))->update([
                'stock' => $stock,
                'stock_value' => $stock_value,
                'purchase_price' => $purchase_price
            ]);
        } else {
            Item::where('id', request('item_id'))->update([
                'stock' => $stock,
                'stock_value' => $stock_value
            ]);
        }
        return Item::where('id',request('item_id'))->first();
    }
    public function updateVariant($id) {
        if(request('type') == 'Add Stock') {
            $action = 1;
        } else if (request('type') == 'Reduce Stock') {
            $action = 2;
        } else {
            $action = 0;
        }
        ItemTransaction::where('id',$id)->update([
            'quantity' => request('quantity'),
            'price' => request('price'),
            'date' => request('date')
        ]);
        $itemTransactions = ItemTransaction::where('variant_id', request('variant_id'))->where('active',1)->get();
        $stock = 0;
        $stock_value = 0;
        foreach($itemTransactions as $itemTransaction) {
            if ($itemTransaction->type === 2 || $itemTransaction->type === 3) {
                $stock = $stock - $itemTransaction->quantity;
                $stock_value = $stock_value - ($itemTransaction->quantity * $itemTransaction->price);
            } else {
                $stock = $stock + $itemTransaction->quantity;
                $stock_value = $stock_value + ($itemTransaction->quantity * $itemTransaction->price);
            }
        }
        if ($action == 0) {
            $purchase_price = request('price');
            Variant::where('id', request('variant_id'))->update([
                'stock' => $stock,
                'stock_value' => $stock_value,
                'purchase_price' => $purchase_price
            ]);
        } else {
            Variant::where('id', request('variant_id'))->update([
                'stock' => $stock,
                'stock_value' => $stock_value
            ]);
        }
        return Variant::where('id',request('variant_id'))->first();
    }
    public function delete($id) {
        try {
            ItemTransaction::where('id', $id)->delete();
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Cannot Delete Transaction.',
            ],200);
        }
    }
}
