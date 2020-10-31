<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'title', 'description', 'branch_id', 'total', 'paid',' balance', 'type'
    ];
    public function branch(){
        return $this->belongsTo(RestaurantBranch::class);
    }
}
