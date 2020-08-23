<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
use App\Models\TempProduct;
use App\Models\Purchase;
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

    public function purchasePrice()
    {
        return $this->hasOne(Purchase::class,'product_id','product_id')
        ->select('product_id','price','created_at')
        ->latest();
    }

    public function tempProduct()
    {
    	return $this->belongsTo(TempProduct::class,'product_id','id');
    }
}
