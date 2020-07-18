<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Brand;
use App\Models\Category;

class TempProduct extends Model
{
    use SoftDeletes;
    protected $table = "temp_products";

    public function user()
    {
    	return $this->belongsTo(User::class,'user_id','id');
    }

    public function brand()
    {
    	return $this->belongsTo(Brand::class,'brand_id','id');    
    }

    public function category()
    {
    	return $this->belongsTo(Category::class,'category_id','id');    
    }
}
