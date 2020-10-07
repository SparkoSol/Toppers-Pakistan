<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentIn extends Model
{
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
