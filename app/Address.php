<?php

namespace App;

use App\Customer;
use App\Order;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function order(){
        return $this->belongsTo(Order::class);
    }
}
