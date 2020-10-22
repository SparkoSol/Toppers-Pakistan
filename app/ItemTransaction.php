<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemTransaction extends Model
{
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function variant(){
        return $this->belongsTo(Variant::class);
    }
}
