<?php

namespace App\Http\Controllers;

use App\PurchaseOrderItem;
use Illuminate\Http\Request;

class PurchaseOrderItemController extends Controller
{
    public function getPurchaseItemsByPurchaseId($id) {
        return PurchaseOrderItem::where('purchase_order_id',$id)->with('product.unit')->with('variant')->get();
    }
}
