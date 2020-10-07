<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleOrderItem extends Model
{
    public function product(){
        return $this->HasOne(Product::class,'id','product_id');
    }
}
