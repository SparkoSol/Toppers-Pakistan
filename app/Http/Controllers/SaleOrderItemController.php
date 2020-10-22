<?php

namespace App\Http\Controllers;

use App\SaleOrderItem;
use Illuminate\Http\Request;

class SaleOrderItemController extends Controller
{
    public function getSaleItemsBySaleId($id) {
        return SaleOrderItem::where('sale_order_id',$id)->with('product.unit')->with('variant')->get();
    }
}
