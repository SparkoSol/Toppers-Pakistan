<?php

namespace App;

use App\Customer;

use Illuminate\Database\Eloquent\Model;

class CustomerTransaction extends Model
{
    public function customer(){
        return $this->hasMany(Customer::class);
    }
}
