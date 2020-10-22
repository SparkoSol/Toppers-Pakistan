<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public function restaurant(){
        return $this->belongsTo(Restaurant::class);
    }
    public function subCategory(){
        return $this->belongsTo(SubCategory::class, 'subCategory_id');
    }
    public function branch(){
        return $this->belongsTo(RestaurantBranch::class);
    }
    public function unit(){
        return $this->belongsTo(Unit::class);
    }
}
