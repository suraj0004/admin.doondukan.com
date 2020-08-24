<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\TempProduct;

class SaleReturn extends Model
{
    protected $table = "sale_returns";

    protected $fillable = [
        "user_id","bill_id","sale_id","product_id","product_source","price","quantity","created_at","updated_at"
    ];

    public function product()
    {
    	return $this->belongsTo(Product::class,'product_id','id');
    }

    public function tempProduct()
    {
    	return $this->belongsTo(TempProduct::class,'product_id','id');
    }
}
