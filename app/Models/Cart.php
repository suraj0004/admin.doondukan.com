<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = ["id", "updated_at", "created_at"];
    public function product()
    {
    	return $this->belongsTo(Product::class,'product_id','id');
    }
}
