<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleOrder extends Model
{
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function branch(){
        return $this->belongsTo(RestaurantBranch::class);
    }
    public function address(){
        return $this->belongsTo(Address::class);
    }
}
