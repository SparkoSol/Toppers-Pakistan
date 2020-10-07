<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function saleOrder() {
        return$this->belongsTo(SaleOrder::class);
    }
}
