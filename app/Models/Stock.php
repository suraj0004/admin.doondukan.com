<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
use App\Models\Price;

class Stock extends Model
{
	use SoftDeletes;
	
    protected $table = "stocks";
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];

    public function product()
    {
    	return $this->belongsTo(Product::class,'product_id','id');
    }

    public function price()
    {
    	return $this->hasOne(Price::class,'stock_id','id');
    }
}
