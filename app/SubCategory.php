<?php

namespace App;

use App\Category;
use App\Product;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function products(){
        return $this->hasMany(Product::class);
    }
}
