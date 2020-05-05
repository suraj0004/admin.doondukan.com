<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Purchase;

class Purchase extends Model
{
    public function products()
    {
    	return $this->belongsTo(Purchase::class,'product_id','id');
    }
}
