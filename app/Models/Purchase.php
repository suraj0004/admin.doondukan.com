<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Purchase extends Model
{
	protected $table = "purchases";
	
    public function product()
    {
    	return $this->belongsTo(Product::class,'product_id','id');
    }
}