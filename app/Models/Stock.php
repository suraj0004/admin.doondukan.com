<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
use App\Models\TempProduct;

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

    public function tempProduct()
    {
    	return $this->belongsTo(TempProduct::class,'product_id','id');
    }
}
