<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    public function branches(){
        return $this->hasMany(RestaurantBranch::class);
    }
    public function products(){
        return $this->hasMany(Product::class);
    }
}
