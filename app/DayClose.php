<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DayClose extends Model
{
    public function branch(){
        return $this->HasOne(RestaurantBranch::class,'id','branch_id');
    }
}
