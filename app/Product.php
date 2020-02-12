<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function restaurant(){
        return $this->belongsTo(Restaurant::class);
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function unit(){
        return $this->HasOne(Unit::class,'id','unit_id');
    }
    public function orderItem(){
        return $this->belongsTo(OrderItem::class);
    }
}
