<?php

namespace App\Http\Controllers;

use App\Variant;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    public function getByItem($id) {
        return Variant::where('item_id', $id)->where('active', 1)->get();
    }
}
