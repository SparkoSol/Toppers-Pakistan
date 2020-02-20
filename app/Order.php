<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function branch(){
        return $this->belongsTo(RestaurantBranch::class);
    }
    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }
    public function address(){
        return $this->HasOne(Address::class,'id','address_id');
    }
}
