<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    public function values(){
        return $this->hasMany(OptionValue::class);
    }
}
