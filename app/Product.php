<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function restaurant(){
        return $this->belongsTo(Restaurant::class);
    }
    public function subCategory(){
        return $this->belongsTo(SubCategory::class,'subCategory_id','id');
    }
    public function productHistory(){
        return $this->belongsTo(ProductHistory::class,'product_id','id');
    }
    public function unit(){
        return $this->HasOne(Unit::class,'id','unit_id');
    }
    public function orderItem(){
        return $this->belongsTo(OrderItem::class);
    }
}
