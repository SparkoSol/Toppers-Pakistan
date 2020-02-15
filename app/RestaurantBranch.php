<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantBranch extends Model
{
    public function restaurant(){
        return $this->belongsTo(Restaurant::class);
    }
    public function orders(){
        return $this->hasMany(Order::class);
    }
}
