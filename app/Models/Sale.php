<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
use App\Models\TempProduct;
use App\Models\Bill;
use App\Models\Stock;

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

    public function bill()
    {
        return $this->belongsTo(Bill::class,'bill_id','id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class,'product_id','product_id');
    }
}
