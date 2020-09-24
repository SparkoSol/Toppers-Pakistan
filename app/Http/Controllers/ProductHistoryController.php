<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductHistory;
use Illuminate\Http\Request;

class ProductHistoryController extends Controller
{
    public function storeProductHistory(Request $request) {
        if(request('actionType') == 'Add Stock') {
            $action = 0;
        } else if (request('actionType') == 'Reduce Stock') {
            $action = 1;
        } else {
            $action = 2;
        }
        $productHistory = new ProductHistory();
        $productHistory->product_id = request('product_id');
        $productHistory->quantity = request('quantity');
        $productHistory->value = request('atPrice');
        $productHistory->action_type = $action;
        $productHistory->date = request('date');
        $productHistory->save();

        $productHistories = ProductHistory::where('product_id', request('product_id'))->get();
        $quantity = 0;
        $value = 0;
        foreach ($productHistories as $item) {
            if ($item->action_type === 1) {
                $quantity = $quantity - $item->quantity;
                $value = $value - ($item->quantity * $item->value);
            } else {
                $quantity = $quantity + $item->quantity;
                $value = $value + ($item->quantity * $item->value);
            }
        }
        Product::where('id', request('product_id'))->update([
            'stock' => $quantity,
            'stock_value' => $value
        ]);
        return Product::where('id',request('product_id'))->first();;
    }
    public function apiIndexById($id) {
        return ProductHistory::where('product_id',$id)->orderBy('id','desc')->get();
    }
    public function updateProductHistory(Request $request, $id) {
        if(request('actionType') == 'Add Stock') {
            $action = 0;
        } else if (request('actionType') == 'Reduce Stock') {
            $action = 1;
        } else {
            $action = 2;
        }
        ProductHistory::where('id',$id)->update([
            'quantity' => request('quantity'),
            'value' => request('atPrice'),
            'date' => request('date')
        ]);
        $productHistories = ProductHistory::where('product_id', request('product_id'))->get();
        $quantity = 0;
        $value = 0;
        foreach ($productHistories as $item) {
            if ($item->action_type === 1) {
                $quantity = $quantity - $item->quantity;
                $value = $value - ($item->quantity * $item->value);
            } else {
                $quantity = $quantity + $item->quantity;
                $value = $value + ($item->quantity * $item->value);
            }
        }
        Product::where('id', request('product_id'))->update([
            'stock' => $quantity,
            'stock_value' => $value
        ]);
        return Product::where('id',request('product_id'))->first();
    }
    public function deleteProductHistory($id) {
        ProductHistory::where('id',$id)->delete();
        $productHistories = ProductHistory::where('product_id', request('product_id'))->get();
        $quantity = 0;
        $value = 0;
        foreach ($productHistories as $item) {
            if ($item->action_type === 1) {
                $quantity = $quantity - $item->quantity;
                $value = $value - ($item->quantity * $item->value);
            } else {
                $quantity = $quantity + $item->quantity;
                $value = $value + ($item->quantity * $item->value);
            }
        }
        Product::where('id', request('product_id'))->update([
            'stock' => $quantity,
            'stock_value' => $value
        ]);
        return Product::where('id',request('product_id'))->first();
    }
}
