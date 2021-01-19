<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonthClose extends Model
{
    public function branch(){
        return $this->HasOne(RestaurantBranch::class,'id','branch_id');
    }
}
