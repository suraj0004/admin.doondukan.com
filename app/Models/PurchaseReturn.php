<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\TempProduct;
class PurchaseReturn extends Model
{
    protected $table = "purchase_returns";

    public function product()
    {
    	return $this->belongsTo(Product::class,'product_id','id');
    }

    public function tempProduct()
    {
    	return $this->belongsTo(TempProduct::class,'product_id','id');
    }
}
