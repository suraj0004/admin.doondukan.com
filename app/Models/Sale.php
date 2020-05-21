<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
	use SoftDeletes;
	protected $table = "sales";
	protected $dates = ['deleted_at'];
	protected $hidden = ['deleted_at'];

	public function product()
    {
    	return $this->belongsTo(Product::class,'product_id','id')->withCasts(['created_at'=>'datetime:d M, Y h:i a']);
    }

    public function tempProduct()
    {
    	return $this->belongsTo(TempProduct::class,'product_id','id')->withCasts(['created_at'=>'datetime:d M, Y h:i a']);
    }
}
