<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
    public function purchaseOrder() {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
