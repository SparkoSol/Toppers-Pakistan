<?php

namespace App\Http\Controllers;

use App\Variant;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    public function getByItem($id) {
        return Variant::where('item_id', $id)->where('active', 1)->get();
    }
    public function getByOrderItem($id) {
        return Variant::where('item_id', $id)->where('active', 1)->where('stock','>',0)->get();
    }
}
