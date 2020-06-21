<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Brand;
use App\Models\Category;

class Product extends Model
{
	use SoftDeletes;

    protected $table = "products";
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];

    public function brand()
    {
    	return $this->belongsTo(Brand::class,'brand_id','id');    
    }

    public function category()
    {
    	return $this->belongsTo(Category::class,'category_id','id');    
    }
}
