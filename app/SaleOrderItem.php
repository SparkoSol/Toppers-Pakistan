<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleOrderItem extends Model
{
    public function product(){
        return $this->HasOne(Item::class,'id','item_id');
    }
    public function variant() {
        return $this->hasOne(Variant::class,'id','variant_id');
    }
}
